<?php
declare(strict_types=1);
namespace App\Services;

use App\Core\Env;

final class MidasDocumentStorage
{
    private const ENV_KEY = 'MIDAS_LIBRARY_STORAGE_DIR';
    private const DEFAULT_DIR = '/storage/biblioteca-midas';

    public static function configured(): bool { return trim(Env::get(self::ENV_KEY)) !== ''; }

    public static function dir(): string
    {
        $configured = trim(str_replace('\\', '/', Env::get(self::ENV_KEY)));
        if ($configured === '') return BASE_PATH . self::DEFAULT_DIR;
        $absolute = str_starts_with($configured, '/') || preg_match('/^[A-Z]:\//i', $configured);
        return rtrim($absolute ? $configured : BASE_PATH . '/' . trim($configured, '/'), '/');
    }

    public static function path(string $filename): string { return self::dir() . '/' . basename($filename); }

    public static function inspect(string $path, string $name): array
    {
        return AppraisalMidasFileStorage::inspect($path, $name);
    }

    public static function storeUploaded(string $tmpName, string $destination): int
    {
        self::ensure();
        $moved = move_uploaded_file($tmpName, $destination)
            || (PHP_SAPI === 'cli' && rename($tmpName, $destination));
        clearstatcache(true, $destination);
        if (!$moved || !is_file($destination)) throw new \RuntimeException('El documento MIDAS no quedó guardado.');
        return (int) filesize($destination);
    }

    public static function writable(): bool
    {
        try { self::ensure(); } catch (\Throwable) { return false; }
        return is_writable(self::dir());
    }

    public static function limits(): array { return PdfFileStorage::limits(); }

    public static function uploadErrorMessage(int $code): string
    {
        return AppraisalMidasFileStorage::uploadErrorMessage($code);
    }

    private static function ensure(): void
    {
        $dir = self::dir();
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new \RuntimeException('No se pudo preparar la Biblioteca MIDAS.');
        }
    }
}
