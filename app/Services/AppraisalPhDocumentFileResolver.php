<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalPhDocumentFileResolver
{
    public function path(array $document): string
    {
        $storage = (string) ($document['storage_filename'] ?? '');
        $path = $storage !== '' ? AppraisalPhDocumentStorage::path($storage) : '';
        if ($path !== '' && is_file($path)) return $path;
        $blob = $document['file_blob'] ?? null;
        if (!is_string($blob) || $blob === '') {
            throw new \RuntimeException('El soporte PH no tiene archivo físico ni respaldo interno disponible.');
        }
        $tmp = tempnam(sys_get_temp_dir(), 'ga_ph_reload_');
        file_put_contents($tmp, $blob);
        return $tmp;
    }

    public function temporary(string $path): bool
    {
        return str_starts_with($path, sys_get_temp_dir());
    }
}
