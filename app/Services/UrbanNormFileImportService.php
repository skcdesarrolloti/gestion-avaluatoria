<?php
declare(strict_types=1);
namespace App\Services;
use App\Models\UrbanNormativeRepository;

final class UrbanNormFileImportService
{
    public function __construct(private UrbanNormativeRepository $documents) {}

    public function importFor(array $files, array $document): array
    {
        UrbanNormFileStorage::ensure();
        $file = $this->singleFile($files);
        $name = $this->cleanUploadName((string) $file['name']);
        if ((int) $file['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'message' => "$name " . UrbanNormFileStorage::uploadErrorMessage((int) $file['error'])];
        }
        if (!UrbanNormFileStorage::isPdf((string) $file['tmp_name'], $name)) {
            return ['ok' => false, 'message' => "$name no es un PDF válido."];
        }
        $storageName = (string) ($document['storage_filename'] ?: $document['slug'] . '.pdf');
        $destination = UrbanNormativeRepository::storagePath($storageName);
        $bytes = (int) filesize((string) $file['tmp_name']);
        if (!$this->sameFile($destination, (string) $file['tmp_name'], $bytes)) {
            $bytes = UrbanNormFileStorage::storeUploaded((string) $file['tmp_name'], $destination);
        }
        $this->documents->saveImportedFile((string) $document['slug'], $name, $storageName, $bytes, $this->pdfBlob($destination));
        return ['ok' => true, 'copied' => [$name], 'skipped' => []];
    }

    private function singleFile(array $files): array
    {
        $names = $files['name'] ?? [];
        if (!is_array($names) || $names === []) throw new \RuntimeException('Selecciona el PDF de esta norma urbana.');
        return ['name' => (string) ($files['name'][0] ?? ''), 'tmp_name' => (string) ($files['tmp_name'][0] ?? ''),
            'error' => (int) ($files['error'][0] ?? UPLOAD_ERR_NO_FILE)];
    }

    private function sameFile(string $destination, string $source, int $bytes): bool
    {
        return is_file($destination) && filesize($destination) === $bytes
            && hash_file('sha256', $destination) === hash_file('sha256', $source);
    }

    private function cleanUploadName(string $name): string { return basename(str_replace('\\', '/', $name)); }

    private function pdfBlob(string $path): string
    {
        $blob = file_get_contents($path);
        if (!is_string($blob)) throw new \RuntimeException('El PDF se guardó, pero no se pudo crear el respaldo interno.');
        return $blob;
    }
}
