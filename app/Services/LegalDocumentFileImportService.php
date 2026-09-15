<?php
declare(strict_types=1);
namespace App\Services;
use App\Models\LegalDocumentRepository;

final class LegalDocumentFileImportService
{
    public function __construct(private LegalDocumentRepository $documents) {}

    public function importFor(array $files, array $document): array
    {
        LegalFileStorage::ensure();
        $file = $this->singleFile($files);
        $name = $this->cleanUploadName((string) $file['name']);
        if ((int) $file['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'message' => "$name " . LegalFileStorage::uploadErrorMessage((int) $file['error'])];
        }
        if (!LegalFileStorage::isPdf((string) $file['tmp_name'], $name)) {
            return ['ok' => false, 'message' => "$name no es un PDF válido."];
        }
        $storageName = (string) ($document['storage_filename'] ?: $document['slug'] . '.pdf');
        $destination = LegalDocumentRepository::storagePath($storageName);
        $bytes = (int) filesize((string) $file['tmp_name']);
        if (!$this->sameFile($destination, (string) $file['tmp_name'], $bytes)) {
            $bytes = LegalFileStorage::storeUploaded((string) $file['tmp_name'], $destination);
        }
        $this->documents->saveImportedDocument([
            'slug' => $document['slug'],
            'category_code' => $document['category_code'],
            'document_code' => $document['document_code'],
            'title' => $document['title'],
            'document_type' => $document['document_type'],
            'source_filename' => $name,
            'storage_filename' => $storageName,
        ], (string) $document['status'], $bytes);
        return ['ok' => true, 'copied' => [$name], 'skipped' => []];
    }

    private function singleFile(array $files): array
    {
        $names = $files['name'] ?? [];
        if (!is_array($names) || $names === []) {
            throw new \RuntimeException('Selecciona el PDF de este documento.');
        }
        return ['name' => (string) ($files['name'][0] ?? ''),
            'tmp_name' => (string) ($files['tmp_name'][0] ?? ''),
            'error' => (int) ($files['error'][0] ?? UPLOAD_ERR_NO_FILE)];
    }

    private function sameFile(string $destination, string $source, int $bytes): bool
    {
        return is_file($destination) && filesize($destination) === $bytes
            && hash_file('sha256', $destination) === hash_file('sha256', $source);
    }

    private function cleanUploadName(string $name): string { return basename(str_replace('\\', '/', $name)); }
}
