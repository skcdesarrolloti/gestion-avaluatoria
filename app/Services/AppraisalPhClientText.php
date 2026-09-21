<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalPhClientText
{
    public static function uploaded(array $file): array
    {
        if (!$file) return [];
        $path = (string) ($file['tmp_name'] ?? '');
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !is_uploaded_file($path)
            || filesize($path) > 20 * 1024 * 1024) {
            throw new \RuntimeException('No se recibió la lectura completa del PDF. Reintenta la carga.');
        }
        $data = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($data) || count($data) > 80) throw new \RuntimeException('Lectura PH inválida.');
        return $data;
    }

    public static function text(array $document, string $name, int $size): string
    {
        if (($document['name'] ?? '') !== $name || ($document['size'] ?? -1) !== $size
            || !is_int($document['total'] ?? null) || $document['total'] < 1
            || count($document['pages'] ?? []) !== $document['total']) {
            throw new \RuntimeException('La lectura no corresponde al PDF completo seleccionado.');
        }
        $parts = []; $weak = [];
        foreach ($document['pages'] as $index => $page) {
            $number = $index + 1;
            if (($page['page'] ?? 0) !== $number || !is_string($page['text'] ?? null)
                || strlen($page['text']) > 200000) throw new \RuntimeException('Página PH inválida o incompleta.');
            $text = trim(mb_scrub($page['text'], 'UTF-8'));
            if (mb_strlen($text) < 40 || (float) ($page['confidence'] ?? 0) < 70) $weak[] = $number;
            $parts[] = "[Página $number]\n" . $text;
        }
        $warning = $weak ? 'Páginas con lectura baja o sin texto (revisar original): ' . implode(', ', $weak) : '';
        return "[Documento: $name]\n[Cobertura: {$document['total']} páginas procesadas]\n$warning\n"
            . implode("\n\n", $parts);
    }
}
