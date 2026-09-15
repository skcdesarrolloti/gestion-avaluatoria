<?php
declare(strict_types=1);
namespace App\Services;
use App\Models\IfrsStandardRepository;

final class IfrsStandardFileImportService
{
    public function __construct(private IfrsStandardRepository $standards) {}

    public function importFor(array $files, array $standard): array
    {
        IfrsFileStorage::ensure();
        $file = $this->singleFile($files);
        $name = $this->cleanUploadName((string) $file['name']);
        if ((int) $file['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'message' => "$name " . IfrsFileStorage::uploadErrorMessage((int) $file['error'])];
        }
        if (!IfrsFileStorage::isPdf((string) $file['tmp_name'], $name)) {
            return ['ok' => false, 'message' => "$name no es un PDF válido."];
        }
        $storageName = (string) ($standard['storage_filename'] ?: $standard['slug'] . '.pdf');
        $destination = IfrsStandardRepository::storagePath($storageName);
        $bytes = (int) filesize((string) $file['tmp_name']);
        if (!$this->sameFile($destination, (string) $file['tmp_name'], $bytes)) {
            $bytes = IfrsFileStorage::storeUploaded((string) $file['tmp_name'], $destination);
        }
        $this->standards->saveImportedFile((string) $standard['slug'], $name, $storageName, $bytes);
        return ['ok' => true, 'copied' => [$name], 'skipped' => []];
    }

    private function singleFile(array $files): array
    {
        $names = $files['name'] ?? [];
        if (!is_array($names) || $names === []) throw new \RuntimeException('Selecciona el PDF de esta norma NIIF.');
        return ['name' => (string) ($files['name'][0] ?? ''), 'tmp_name' => (string) ($files['tmp_name'][0] ?? ''),
            'error' => (int) ($files['error'][0] ?? UPLOAD_ERR_NO_FILE)];
    }

    private function sameFile(string $destination, string $source, int $bytes): bool
    {
        return is_file($destination) && filesize($destination) === $bytes
            && hash_file('sha256', $destination) === hash_file('sha256', $source);
    }

    private function cleanUploadName(string $name): string { return basename(str_replace('\\', '/', $name)); }
}
