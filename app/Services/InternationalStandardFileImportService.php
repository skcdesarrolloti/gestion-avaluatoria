<?php
declare(strict_types=1);
namespace App\Services;
use App\Models\InternationalStandardRepository;

final class InternationalStandardFileImportService
{
    public function __construct(private InternationalStandardRepository $standards) {}

    public function importFor(array $files, array $standard): array
    {
        InternationalFileStorage::ensure();
        $file = $this->singleFile($files);
        $name = $this->cleanUploadName((string) $file['name']);
        if ((int) $file['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'message' => "$name " . InternationalFileStorage::uploadErrorMessage((int) $file['error'])];
        }
        if (!InternationalFileStorage::isPdf((string) $file['tmp_name'], $name)) {
            return ['ok' => false, 'message' => "$name no es un PDF válido."];
        }
        $storageName = (string) ($standard['storage_filename'] ?: $standard['slug'] . '.pdf');
        $destination = InternationalStandardRepository::storagePath($storageName);
        $bytes = (int) filesize((string) $file['tmp_name']);
        if (!$this->sameFile($destination, (string) $file['tmp_name'], $bytes)) {
            $bytes = InternationalFileStorage::storeUploaded((string) $file['tmp_name'], $destination);
        }
        $this->standards->saveImportedFile((string) $standard['slug'], $name, $storageName, $bytes, $this->pdfBlob($destination));
        return ['ok' => true, 'copied' => [$name], 'skipped' => []];
    }

    private function singleFile(array $files): array
    {
        $names = $files['name'] ?? [];
        if (!is_array($names) || $names === []) throw new \RuntimeException('Selecciona el PDF de esta norma IVS.');
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
        if (!is_string($blob)) {
            throw new \RuntimeException('El PDF se guardó en disco, pero no se pudo crear el respaldo interno.');
        }
        return $blob;
    }
}
