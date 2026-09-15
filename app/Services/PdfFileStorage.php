<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\Env;

final class PdfFileStorage
{
    public static function configured(string $envKey): bool { return trim(Env::get($envKey)) !== ''; }

    public static function dir(string $envKey, string $default): string
    {
        $configured = trim(str_replace('\\', '/', Env::get($envKey)));
        if ($configured === '') return BASE_PATH . $default;
        $absolute = str_starts_with($configured, '/') || preg_match('/^[A-Z]:\//i', $configured);
        return rtrim($absolute ? $configured : BASE_PATH . '/' . trim($configured, '/'), '/');
    }

    public static function path(string $dir, string $filename): string { return $dir . '/' . basename($filename); }

    public static function ensure(string $dir): void
    {
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new \RuntimeException('No se pudo preparar el almacenamiento privado.');
        }
    }

    public static function writable(string $dir): bool
    {
        try {
            self::ensure($dir);
        } catch (\Throwable) {
            return false;
        }
        return is_writable($dir);
    }

    public static function isPdf(string $path, string $name): bool
    {
        if (!is_file($path) || mb_strtolower(pathinfo($name, PATHINFO_EXTENSION)) !== 'pdf') return false;
        $handle = fopen($path, 'rb');
        if (!$handle) return false;
        try {
            return fread($handle, 4) === '%PDF';
        } finally {
            fclose($handle);
        }
    }

    public static function storeUploaded(string $tmpName, string $destination): int
    {
        self::ensure(dirname($destination));
        $moved = move_uploaded_file($tmpName, $destination)
            || (PHP_SAPI === 'cli' && rename($tmpName, $destination));
        clearstatcache(true, $destination);
        if (!$moved || !is_file($destination) || !self::isPdf($destination, $destination)) {
            throw new \RuntimeException('El archivo no quedó verificado en almacenamiento privado.');
        }
        return (int) filesize($destination);
    }

    public static function uploadErrorMessage(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'supera el tamaño permitido por PHP.',
            UPLOAD_ERR_PARTIAL => 'llegó incompleto.',
            UPLOAD_ERR_NO_FILE => 'no llegó al servidor.',
            UPLOAD_ERR_NO_TMP_DIR => 'no hay carpeta temporal configurada.',
            UPLOAD_ERR_CANT_WRITE => 'PHP no pudo escribirlo en disco.',
            UPLOAD_ERR_EXTENSION => 'una extensión de PHP bloqueó la carga.',
            default => 'no se pudo recibir.',
        };
    }

    public static function limits(): array
    {
        return [
            'upload_max_filesize' => ini_get('upload_max_filesize') ?: '',
            'post_max_size' => ini_get('post_max_size') ?: '',
            'max_file_uploads' => ini_get('max_file_uploads') ?: '',
            'max_execution_time' => ini_get('max_execution_time') ?: '',
        ];
    }
}
