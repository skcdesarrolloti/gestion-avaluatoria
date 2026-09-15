<?php
declare(strict_types=1);
namespace App\Services;
use App\Models\LegalDocumentRepository;

final class LegalDocumentImportService
{
    public function __construct(private LegalDocumentRepository $documents) {}

    public function importUploaded(array $files, string $categoryCode, string $status): array
    {
        $category = $this->documents->categoryCodeOrDefault($categoryCode);
        $status = in_array($status, ['vigente', 'derogada', 'historica'], true) ? $status : 'vigente';
        LegalFileStorage::ensure();
        $result = ['ok' => true, 'copied' => [], 'skipped' => [], 'errors' => []];
        foreach ($this->uploadedFiles($files) as $file) {
            $name = $this->cleanUploadName($file['name']);
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $result['errors'][] = "$name " . LegalFileStorage::uploadErrorMessage($file['error']);
                continue;
            }
            if (!LegalFileStorage::isPdf($file['tmp_name'], $name)) {
                $result['errors'][] = "$name no es un PDF válido.";
                continue;
            }
            $meta = $this->documentMeta($name, $category);
            $destination = LegalDocumentRepository::storagePath($meta['storage_filename']);
            $bytes = (int) filesize($file['tmp_name']);
            if ($this->sameFile($destination, $file['tmp_name'], $bytes)) {
                $this->documents->saveImportedDocument($meta, $status, $bytes);
                $result['skipped'][] = $name;
                continue;
            }
            try {
                $storedBytes = LegalFileStorage::storeUploaded($file['tmp_name'], $destination);
            } catch (\Throwable $error) {
                $result['errors'][] = "$name no se pudo guardar: " . $error->getMessage();
                continue;
            }
            $this->documents->saveImportedDocument($meta, $status, $storedBytes);
            $result['copied'][] = $name;
        }
        return $result;
    }

    private function uploadedFiles(array $files): array
    {
        $names = $files['name'] ?? [];
        if (!is_array($names) || $names === []) {
            throw new \RuntimeException('Selecciona al menos un PDF jurídico para importar.');
        }
        $uploads = [];
        foreach (array_keys($names) as $index) {
            $uploads[] = ['name' => (string) ($files['name'][$index] ?? ''),
                'tmp_name' => (string) ($files['tmp_name'][$index] ?? ''),
                'error' => (int) ($files['error'][$index] ?? UPLOAD_ERR_NO_FILE)];
        }
        return $uploads;
    }

    private function documentMeta(string $name, string $category): array
    {
        $base = trim((string) preg_replace('/\s+/', ' ', pathinfo($name, PATHINFO_FILENAME)));
        $title = mb_substr($base !== '' ? $base : 'Documento jurídico', 0, 240);
        $hash = substr(hash('sha1', $category . '|' . $name), 0, 10);
        $slug = $this->slug($category . '-' . $title . '-' . $hash);
        return ['slug' => $slug, 'category_code' => $category, 'document_code' => mb_substr($title, 0, 80),
            'title' => $title, 'document_type' => $this->inferType($title),
            'source_filename' => $name, 'storage_filename' => $slug . '.pdf'];
    }

    private function slug(string $value): string
    {
        $ascii = function_exists('iconv') ? iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) : false;
        $seed = strtolower($ascii !== false ? $ascii : $value);
        $slug = trim((string) preg_replace('/[^a-z0-9]+/', '-', $seed), '-');
        return mb_substr($slug !== '' ? $slug : 'documento-juridico', 0, 95);
    }

    private function inferType(string $title): string
    {
        $lower = mb_strtolower($title);
        return match (true) {
            str_starts_with($lower, 'ley') => 'Ley',
            str_starts_with($lower, 'decreto') => 'Decreto',
            str_starts_with($lower, 'resolucion'), str_starts_with($lower, 'resolución') => 'Resolución',
            str_starts_with($lower, 'circular') => 'Circular',
            str_starts_with($lower, 'sentencia') => 'Sentencia',
            str_starts_with($lower, 'acuerdo') => 'Acuerdo',
            default => 'Documento',
        };
    }

    private function sameFile(string $destination, string $source, int $bytes): bool
    {
        return is_file($destination) && filesize($destination) === $bytes
            && hash_file('sha256', $destination) === hash_file('sha256', $source);
    }

    private function cleanUploadName(string $name): string { return basename(str_replace('\\', '/', $name)); }
}
