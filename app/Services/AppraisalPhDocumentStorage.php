<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\Env;

final class AppraisalPhDocumentStorage
{
    private const ENV_KEY = 'APPRAISAL_PH_DOCUMENT_DIR';
    private const DEFAULT_DIR = '/storage/propiedad-horizontal';
    private const MAX_FILE_BYTES = 52428800;
    private const MAX_ARCHIVE_BYTES = 314572800;
    private const EXTENSIONS = ['zip' => 'application/zip', 'rar' => 'application/vnd.rar', 'pdf' => 'application/pdf',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'txt' => 'text/plain', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
        'png' => 'image/png', 'webp' => 'image/webp', 'tif' => 'image/tiff', 'tiff' => 'image/tiff'];

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
        $ext = mb_strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!is_file($path) || !isset(self::EXTENSIONS[$ext])) {
            throw new \InvalidArgumentException('Sube ZIP, RAR, PDF, DOCX, TXT o imagen JPG, PNG, WEBP o TIFF.');
        }
        $size = (int) filesize($path);
        if ($size <= 0) {
            throw new \InvalidArgumentException('El soporte PH llegó vacío. Selecciona nuevamente el archivo o súbelo dentro de un ZIP/RAR válido.');
        }
        $archive = in_array($ext, ['zip', 'rar'], true);
        $limit = $archive ? self::MAX_ARCHIVE_BYTES : self::MAX_FILE_BYTES;
        if ($size > $limit) {
            throw new \InvalidArgumentException($archive
                ? 'Cada ZIP/RAR PH debe pesar máximo 300 MB.'
                : 'Cada soporte PH suelto debe pesar máximo 50 MB.');
        }
        if ($ext === 'zip' && !self::startsWith($path, "PK\x03\x04")) throw new \InvalidArgumentException('El ZIP no parece válido.');
        if ($ext === 'rar' && !self::isRar($path)) throw new \InvalidArgumentException('El RAR no parece válido.');
        return ['extension' => $ext === 'jpeg' ? 'jpg' : $ext, 'mime' => self::EXTENSIONS[$ext], 'bytes' => $size];
    }

    public static function storeUploaded(string $tmpName, string $destination): int
    {
        self::ensure();
        $moved = move_uploaded_file($tmpName, $destination) || (PHP_SAPI === 'cli' && rename($tmpName, $destination));
        clearstatcache(true, $destination);
        if (!$moved || !is_file($destination)) throw new \RuntimeException('El soporte PH no quedó guardado.');
        return (int) filesize($destination);
    }

    public static function storeFile(string $source, string $destination): int
    {
        self::ensure();
        if (!copy($source, $destination)) throw new \RuntimeException('El soporte PH no quedó guardado.');
        clearstatcache(true, $destination);
        return (int) filesize($destination);
    }

    public static function uploadErrorMessage(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'supera el tamaño permitido por PHP.',
            UPLOAD_ERR_PARTIAL => 'llegó incompleto.', UPLOAD_ERR_NO_FILE => 'no llegó al servidor.',
            UPLOAD_ERR_NO_TMP_DIR => 'no hay carpeta temporal configurada.',
            UPLOAD_ERR_CANT_WRITE => 'PHP no pudo escribirlo en disco.',
            UPLOAD_ERR_EXTENSION => 'una extensión de PHP bloqueó la carga.', default => 'no se pudo recibir.',
        };
    }

    private static function ensure(): void
    {
        $dir = self::dir();
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new \RuntimeException('No se pudo preparar el almacenamiento PH.');
        }
    }

    private static function startsWith(string $path, string $signature): bool
    {
        $handle = fopen($path, 'rb');
        if (!$handle) return false;
        try { return fread($handle, strlen($signature)) === $signature; }
        finally { fclose($handle); }
    }

    private static function isRar(string $path): bool
    {
        return self::startsWith($path, "Rar!\x1A\x07\x00") || self::startsWith($path, "Rar!\x1A\x07\x01\x00");
    }
}
