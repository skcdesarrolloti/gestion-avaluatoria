<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalPhClientPdfOcrService
{
    public function __construct(private MiniMaxOcrClient $client = new MiniMaxOcrClient()) {}

    public function extract(array $files): string
    {
        $texts = [];
        foreach ($this->files($files) as $file) {
            $name = mb_substr(basename(str_replace('\\', '/', (string) $file['name'])), 0, 220);
            if ((int) $file['error'] !== UPLOAD_ERR_OK) {
                throw new \RuntimeException(($name ?: 'Página PDF') . ' '
                    . AppraisalPhDocumentStorage::uploadErrorMessage((int) $file['error']));
            }
            $tmp = (string) $file['tmp_name'];
            if (!is_uploaded_file($tmp) && !is_file($tmp)) {
                throw new \RuntimeException('No se recibió la imagen temporal del PDF para MiniMax.');
            }
            $mime = (string) (@mime_content_type($tmp) ?: 'image/jpeg');
            if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], true)) {
                throw new \RuntimeException('La imagen temporal del PDF no tiene un formato admitido para MiniMax.');
            }
            $texts[] = $this->client->extract($tmp, $name, $mime);
        }
        return trim(implode("\n\n", array_filter($texts)));
    }

    private function files(array $files): array
    {
        $name = $files['name'] ?? null;
        if (!is_array($name)) {
            return trim((string) $name) === '' ? [] : [[
                'name' => (string) ($files['name'] ?? ''),
                'tmp_name' => (string) ($files['tmp_name'] ?? ''),
                'error' => (int) ($files['error'] ?? UPLOAD_ERR_NO_FILE),
            ]];
        }
        $items = [];
        foreach ($name as $index => $value) {
            if (count($items) >= 12) break;
            if (trim((string) $value) === '' && (int) ($files['error'][$index] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) continue;
            $items[] = ['name' => (string) $value, 'tmp_name' => (string) ($files['tmp_name'][$index] ?? ''),
                'error' => (int) ($files['error'][$index] ?? UPLOAD_ERR_NO_FILE)];
        }
        return $items;
    }
}
