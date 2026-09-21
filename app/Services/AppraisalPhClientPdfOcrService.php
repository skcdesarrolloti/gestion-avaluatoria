<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalPhClientPdfOcrService
{
    private const MAX_CLIENT_PAGES = 300;
    public function __construct(private MiniMaxOcrClient $client = new MiniMaxOcrClient()) {}

    public function extract(array $files, array $encodedImages = [], array $encodedNames = []): string
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
        foreach ($this->encodedFiles($encodedImages, $encodedNames) as $file) {
            try { $texts[] = $this->client->extract($file['path'], $file['name'], $file['mime']); }
            finally { @unlink($file['path']); }
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
            if (count($items) >= self::MAX_CLIENT_PAGES) break;
            if (trim((string) $value) === '' && (int) ($files['error'][$index] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) continue;
            $items[] = ['name' => (string) $value, 'tmp_name' => (string) ($files['tmp_name'][$index] ?? ''),
                'error' => (int) ($files['error'][$index] ?? UPLOAD_ERR_NO_FILE)];
        }
        return $items;
    }

    private function encodedFiles(array $images, array $names): array
    {
        $files = [];
        foreach (array_slice($images, 0, self::MAX_CLIENT_PAGES) as $index => $dataUrl) {
            if (!is_string($dataUrl) || !preg_match('/^data:(image\/(?:jpeg|png|webp|gif));base64,(.+)$/s', $dataUrl, $match)) continue;
            $data = base64_decode($match[2], true);
            if (!is_string($data) || $data === '') continue;
            $tmp = tempnam(sys_get_temp_dir(), 'ga_ph_client_pdf_');
            if (!is_string($tmp) || file_put_contents($tmp, $data) === false) {
                if (is_string($tmp)) @unlink($tmp);
                continue;
            }
            $files[] = ['path' => $tmp, 'mime' => $match[1],
                'name' => mb_substr(basename(str_replace('\\', '/', (string) ($names[$index] ?? 'pagina-pdf.jpg'))), 0, 220)];
        }
        return $files;
    }
}
