<?php
declare(strict_types=1);
namespace App\Services;

use App\Models\MidasDocumentRepository;

final class MidasDocumentUploadService
{
    private const MAX_DB_BACKUP_BYTES = 1048576;

    public function __construct(private MidasDocumentRepository $documents) {}

    public function uploadMany(array $input, array $files, array $user): array
    {
        $items = $this->normalizeFiles($files);
        if ($items === []) throw new \RuntimeException('Selecciona al menos un documento MIDAS.');
        if (count($items) > 20) throw new \RuntimeException('Puedes subir máximo 20 documentos MIDAS por carga.');
        $stored = []; $skipped = []; $failed = [];
        foreach ($items as $file) {
            try {
                $stored[] = $this->upload($this->inputForFile($input, $file, count($items) > 1), $file, $user);
            } catch (\InvalidArgumentException|\RuntimeException $error) {
                $message = $error->getMessage();
                if (str_contains($message, 'Ya existe')) {
                    $skipped[] = $message;
                    continue;
                }
                $failed[] = $this->cleanName((string) ($file['name'] ?? 'Archivo MIDAS')) . ': ' . $message;
            }
        }
        if ($stored === [] && $skipped === [] && $failed !== []) {
            throw new \RuntimeException(implode(' ', $failed));
        }
        return ['stored' => $stored, 'skipped' => $skipped, 'failed' => $failed];
    }

    public function upload(array $input, array $file, array $user): array
    {
        $name = $this->cleanName((string) ($file['name'] ?? ''));
        $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($error !== UPLOAD_ERR_OK) {
            throw new \RuntimeException(($name ?: 'El archivo') . ' ' . MidasDocumentStorage::uploadErrorMessage($error));
        }
        $groups = MidasDocumentRepository::downloadGroups();
        $group = MidasDocumentRepository::canonicalGroup($this->text($input['layer_group'] ?? '', 120));
        $group = $this->groupForFile($group, $name);
        if (!isset($groups[$group])) throw new \InvalidArgumentException('Selecciona el grupo de capa MIDAS.');
        $title = $this->text($input['title'] ?? '', 240);
        if ($title === '') $title = pathinfo($name, PATHINFO_FILENAME) ?: 'Documento MIDAS';
        $code = $this->text($input['document_code'] ?? '', 120);
        if ($code === '') $code = $this->text($title, 120);
        if ($duplicate = $this->documents->findDuplicate($code, $name)) {
            throw new \RuntimeException('Ya existe en Biblioteca MIDAS: ' . $duplicate['title'] . '.');
        }
        $info = MidasDocumentStorage::inspect((string) $file['tmp_name'], $name);
        $id = bin2hex(random_bytes(16));
        $storageName = 'midas-biblioteca-' . $id . '.' . $info['extension'];
        $storedPath = MidasDocumentStorage::path($storageName);
        $bytes = MidasDocumentStorage::storeUploaded((string) $file['tmp_name'], $storedPath);
        $blob = $bytes <= self::MAX_DB_BACKUP_BYTES ? file_get_contents($storedPath) : null;
        if ($blob !== null && !is_string($blob)) throw new \RuntimeException('No se pudo conservar el respaldo del documento MIDAS.');
        $data = ['id' => $id, 'slug' => $this->documents->uniqueSlug($this->slug($code . ' ' . $title)),
            'layer_group' => $group, 'document_code' => $code, 'title' => $title,
            'status' => $this->status((string) ($input['status'] ?? 'vigente')),
            'practical_use' => $this->text($input['practical_use'] ?? '', 700),
            'applies_to' => $this->text($input['applies_to'] ?? '', 240),
            'source_filename' => $name, 'storage_filename' => $storageName,
            'mime_type' => $info['mime'], 'file_size_bytes' => $bytes, 'file_blob' => $blob,
            'created_by' => $this->text($user['name'] ?? '', 120), 'now' => gmdate('Y-m-d H:i:s')];
        $this->documents->store($data);
        return $data;
    }

    private function normalizeFiles(array $files): array
    {
        if (!is_array($files['name'] ?? null)) return ($files['name'] ?? '') !== '' ? [$files] : [];
        $items = [];
        foreach ($files['name'] as $i => $name) {
            $items[] = ['name' => $name, 'type' => $files['type'][$i] ?? '', 'tmp_name' => $files['tmp_name'][$i] ?? '',
                'error' => $files['error'][$i] ?? UPLOAD_ERR_NO_FILE, 'size' => $files['size'][$i] ?? 0];
        }
        return array_values(array_filter($items, fn (array $file): bool => (string) ($file['name'] ?? '') !== ''));
    }

    private function inputForFile(array $input, array $file, bool $batch): array
    {
        if (!$batch) return $input;
        $base = pathinfo($this->cleanName((string) ($file['name'] ?? '')), PATHINFO_FILENAME) ?: 'Documento MIDAS';
        if ($this->text($input['title'] ?? '', 240) === '') $input['title'] = $base;
        if ($this->text($input['document_code'] ?? '', 120) === '') $input['document_code'] = $base;
        return $input;
    }

    private function cleanName(string $name): string
    {
        return $this->text(basename(str_replace('\\', '/', $name)), 240);
    }

    private function text(mixed $value, int $limit): string
    {
        return mb_substr(trim((string) $value), 0, $limit);
    }

    private function status(string $status): string
    {
        return in_array($status, ['vigente', 'historico', 'reemplazado'], true) ? $status : 'vigente';
    }

    private function groupForFile(string $selected, string $name): string
    {
        $plain = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name) ?: $name;
        $plain = strtolower($plain);
        return match (true) {
            str_contains($plain, 'comunas_ucg'),
            str_contains($plain, 'unidad_comunera'),
            str_contains($plain, 'unidades_comuneras'),
            preg_match('/(^|[^a-z])ucg[0-9_ -]*/', $plain) === 1 => 'Unidades comuneras de gobierno',
            str_contains($plain, 'localidad') || str_contains($plain, 'localidades') => 'Localidades',
            str_contains($plain, 'circular') => 'Circulares MIDAS',
            str_contains($plain, 'educacion') => 'Educación',
            str_contains($plain, 'cambio_climatico') || str_contains($plain, 'climatico') => 'Cambio climático',
            default => $selected,
        };
    }

    private function slug(string $value): string
    {
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: $value;
        $value = strtolower((string) preg_replace('/[^a-zA-Z0-9]+/', '-', $value));
        return trim($value, '-');
    }
}
