<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\Env;

final class AppraisalLegalCertificateStorage
{
    private const ENV_KEY = 'APPRAISAL_LEGAL_CERTIFICATE_DIR';
    private const DEFAULT_DIR = '/storage/certificados-juridicos';
    private const MAX_BYTES = 26214400;
    private const EXTENSIONS = [
        'pdf' => 'application/pdf',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'txt' => 'text/plain',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'webp' => 'image/webp',
        'tif' => 'image/tiff',
        'tiff' => 'image/tiff',
    ];

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
            throw new \InvalidArgumentException('Sube el certificado en PDF, DOCX, TXT o imagen JPG, PNG, WEBP o TIFF.');
        }
        $size = (int) filesize($path);
        if ($size <= 0 || $size > self::MAX_BYTES) {
            throw new \InvalidArgumentException('El certificado debe pesar entre 1 byte y 25 MB.');
        }
        if ($ext === 'pdf' && !self::startsWith($path, '%PDF')) throw new \InvalidArgumentException('El PDF no parece válido.');
        if ($ext === 'docx' && !self::startsWith($path, "PK\x03\x04")) throw new \InvalidArgumentException('El DOCX no parece válido.');
        if (str_starts_with(self::EXTENSIONS[$ext], 'image/')) {
            $image = @getimagesize($path);
            if (!is_array($image) || (string) ($image['mime'] ?? '') !== self::EXTENSIONS[$ext]) {
                throw new \InvalidArgumentException('La imagen del certificado no parece válida.');
            }
        }
        return ['extension' => $ext === 'jpeg' ? 'jpg' : $ext, 'mime' => self::EXTENSIONS[$ext], 'bytes' => $size];
    }

    public static function storeUploaded(string $tmpName, string $destination): int
    {
        self::ensure();
        $moved = move_uploaded_file($tmpName, $destination)
            || (PHP_SAPI === 'cli' && rename($tmpName, $destination));
        clearstatcache(true, $destination);
        if (!$moved || !is_file($destination)) throw new \RuntimeException('El certificado no quedó guardado.');
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

    private static function ensure(): void
    {
        $dir = self::dir();
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new \RuntimeException('No se pudo preparar el almacenamiento jurídico.');
        }
    }

    private static function startsWith(string $path, string $signature): bool
    {
        $handle = fopen($path, 'rb');
        if (!$handle) return false;
        try { return fread($handle, strlen($signature)) === $signature; }
        finally { fclose($handle); }
    }
}
