<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalPhChunkUploadService
{
    private const DIR = '/storage/tmp-ph-upload';
    private const CHUNK_BYTES = 6291456;
    private const MAX_BYTES = 314572800;

    public function store(string $appraisalId, int $owner): array
    {
        [$uploadId, $index, $total, $name, $declaredSize] = $this->request();
        $file = $_FILES['chunk'] ?? [];
        if ((int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Un fragmento del soporte PH no llegó completo.');
        }
        $tmpName = (string) ($file['tmp_name'] ?? '');
        $size = is_file($tmpName) ? (int) filesize($tmpName) : 0;
        if ($size <= 0 || $size > self::CHUNK_BYTES + 1048576) {
            throw new \RuntimeException('Un fragmento del soporte PH supera el tamaño permitido.');
        }
        $dir = $this->uploadDir($appraisalId, $owner, $uploadId);
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new \RuntimeException('No se pudo preparar la carga por partes.');
        }
        file_put_contents($dir . '/meta.json', json_encode(['name' => $name, 'total' => $total,
            'size' => $declaredSize], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
        $target = $dir . '/' . str_pad((string) $index, 5, '0', STR_PAD_LEFT) . '.part';
        $moved = move_uploaded_file($tmpName, $target) || (PHP_SAPI === 'cli' && rename($tmpName, $target));
        if (!$moved || !is_file($target)) throw new \RuntimeException('No se pudo guardar un fragmento PH.');
        return ['ok' => true, 'received' => $index + 1, 'total' => $total];
    }

    public function finish(string $appraisalId, int $owner): array
    {
        [$uploadId, , $total, $name, $declaredSize] = $this->request(false);
        $dir = $this->uploadDir($appraisalId, $owner, $uploadId);
        if (!is_dir($dir)) throw new \RuntimeException('No se encontró la carga PH por partes.');
        $assembled = $dir . '/assembled-' . bin2hex(random_bytes(8)) . '-' . preg_replace('/[^A-Za-z0-9._-]+/', '-', $name);
        $out = fopen($assembled, 'wb');
        if (!$out) throw new \RuntimeException('No se pudo ensamblar el soporte PH.');
        try {
            for ($i = 0; $i < $total; $i++) {
                $part = $dir . '/' . str_pad((string) $i, 5, '0', STR_PAD_LEFT) . '.part';
                if (!is_file($part)) throw new \RuntimeException('La carga PH quedó incompleta. Intenta nuevamente.');
                $in = fopen($part, 'rb');
                if (!$in) throw new \RuntimeException('No se pudo leer un fragmento PH.');
                stream_copy_to_stream($in, $out); fclose($in);
            }
        } finally { fclose($out); }
        if ((int) filesize($assembled) !== $declaredSize) {
            @unlink($assembled); throw new \RuntimeException('El soporte PH llegó incompleto. Intenta nuevamente.');
        }
        $this->cleanParts($dir);
        return ['name' => $name, 'tmp_name' => $assembled, 'error' => UPLOAD_ERR_OK];
    }

    private function request(bool $withIndex = true): array
    {
        $uploadId = preg_replace('/[^A-Za-z0-9_-]/', '', (string) ($_POST['upload_id'] ?? ''));
        $index = max(0, (int) ($_POST['index'] ?? 0)); $total = max(1, (int) ($_POST['total'] ?? 0));
        $name = mb_substr(basename(str_replace('\\', '/', (string) ($_POST['filename'] ?? 'soporte-ph.zip'))), 0, 220);
        $size = (int) ($_POST['size'] ?? 0);
        if ($uploadId === '' || strlen($uploadId) > 80 || $total > 120 || $size <= 0 || $size > self::MAX_BYTES) {
            throw new \RuntimeException('La carga PH no tiene una referencia válida.');
        }
        if ($withIndex && $index >= $total) throw new \RuntimeException('Un fragmento PH llegó fuera de secuencia.');
        return [$uploadId, $index, $total, $name, $size];
    }

    private function uploadDir(string $appraisalId, int $owner, string $uploadId): string
    {
        return BASE_PATH . self::DIR . '/' . hash('sha256', $appraisalId . '|' . $owner . '|' . $uploadId);
    }

    private function cleanParts(string $dir): void
    {
        foreach (glob($dir . '/*.part') ?: [] as $file) @unlink($file);
        @unlink($dir . '/meta.json');
    }
}
