<?php
declare(strict_types=1);
namespace App\Services;

final class AppraiserRaaStorage
{
    private const ENV_KEY = 'APPRAISER_RAA_STORAGE_DIR';
    private const DEFAULT_DIR = '/storage/peritos-raa';

    public static function dir(): string { return PdfFileStorage::dir(self::ENV_KEY, self::DEFAULT_DIR); }

    public static function path(string $filename): string { return PdfFileStorage::path(self::dir(), $filename); }

    public static function ensure(): void { PdfFileStorage::ensure(self::dir()); }

    public static function isPdf(string $path, string $name): bool { return PdfFileStorage::isPdf($path, $name); }

    public static function storeUploaded(string $tmpName, string $destination): int
    { return PdfFileStorage::storeUploaded($tmpName, $destination); }

    public static function uploadErrorMessage(int $code): string
    { return PdfFileStorage::uploadErrorMessage($code); }
}
