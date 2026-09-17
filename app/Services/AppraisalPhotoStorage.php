<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\Env;

final class AppraisalPhotoStorage
{
    private const ENV_KEY = 'APPRAISAL_PHOTO_STORAGE_DIR';
    private const DEFAULT_DIR = '/storage/fotos-avaluos';
    private const EXTENSIONS = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];

    public static function dir(): string
    {
        $configured = trim(str_replace('\\', '/', Env::get(self::ENV_KEY)));
        if ($configured === '') return BASE_PATH . self::DEFAULT_DIR;
        $absolute = str_starts_with($configured, '/') || preg_match('/^[A-Z]:\//i', $configured);
        return rtrim($absolute ? $configured : BASE_PATH . '/' . trim($configured, '/'), '/');
    }

    public static function path(string $filename): string { return self::dir() . '/' . basename($filename); }

    public static function ensure(): void
    {
        $dir = self::dir();
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new \RuntimeException('No se pudo preparar el almacenamiento de fotos.');
        }
    }

    public static function inspect(string $path, string $name): array
    {
        $ext = mb_strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!is_file($path) || !isset(self::EXTENSIONS[$ext])) {
            throw new \InvalidArgumentException('Las fotos deben ser JPG, PNG o WEBP.');
        }
        $info = @getimagesize($path);
        if (!$info || ($info['mime'] ?? '') !== self::EXTENSIONS[$ext]) {
            throw new \InvalidArgumentException('El archivo no parece una imagen válida.');
        }
        return ['extension' => $ext === 'jpeg' ? 'jpg' : $ext, 'mime' => self::EXTENSIONS[$ext]];
    }

    public static function storeUploaded(string $tmpName, string $destination): int
    {
        self::ensure();
        $moved = move_uploaded_file($tmpName, $destination)
            || (PHP_SAPI === 'cli' && rename($tmpName, $destination));
        clearstatcache(true, $destination);
        if (!$moved || !is_file($destination)) {
            throw new \RuntimeException('La foto no quedó guardada.');
        }
        return (int) filesize($destination);
    }

    public static function storeFile(string $source, string $destination): int
    {
        self::ensure();
        if (!copy($source, $destination)) {
            throw new \RuntimeException('La foto no quedó guardada.');
        }
        clearstatcache(true, $destination);
        return (int) filesize($destination);
    }
}
