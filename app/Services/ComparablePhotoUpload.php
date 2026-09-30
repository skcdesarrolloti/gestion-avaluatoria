<?php
declare(strict_types=1);
namespace App\Services;

final class ComparablePhotoUpload
{
    public static function inspect(array $file, string $caption): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new \InvalidArgumentException('Selecciona una foto JPG, PNG o WEBP de hasta 5 MB.');
        }
        $path = (string) ($file['tmp_name'] ?? '');
        if (!is_file($path) || filesize($path) > 5 * 1024 * 1024 || filesize($path) === 0) {
            throw new \InvalidArgumentException('Cada foto admite hasta 5 MB y no puede estar vacía.');
        }
        $name = mb_substr(basename(str_replace('\\', '/', (string) ($file['name'] ?? ''))), 0, 190);
        $info = AppraisalPhotoStorage::inspect($path, $name);
        $blob = file_get_contents($path);
        if (!is_string($blob)) throw new \RuntimeException('No se pudo leer la foto.');
        return ['name' => $name, 'mime' => $info['mime'], 'blob' => $blob,
            'caption' => mb_substr(trim($caption), 0, 300), 'hash' => hash('sha256', $blob)];
    }
}
