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
            if ($this->isZipName($name)) {
                $this->importZip($file['tmp_name'], $name, $category, $status, $result);
            } else {
                $this->importPdf($file['tmp_name'], $name, $category, $status, $result);
            }
        }
        return $result;
    }

    private function importZip(string $path, string $name, string $category, string $status, array &$result): void
    {
        if (!class_exists(\ZipArchive::class)) {
            $result['errors'][] = "$name no se pudo abrir porque PHP no tiene ZipArchive.";
            return;
        }
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            $result['errors'][] = "$name no es un ZIP válido.";
            return;
        }
        for ($index = 0; $index < $zip->numFiles; $index++) {
            $entry = $zip->getNameIndex($index);
            if (!is_string($entry) || str_ends_with($entry, '/') || !$this->isPdfName($entry)) continue;
            $stream = $zip->getStream($entry);
            if (!$stream) {
                $result['errors'][] = "$entry no se pudo leer dentro de $name.";
                continue;
            }
            $tmp = tempnam(sys_get_temp_dir(), 'ga_legal_zip_');
            $out = fopen($tmp, 'wb');
            if (!$out) {
                fclose($stream);
                $result['errors'][] = "$entry no se pudo preparar temporalmente.";
                continue;
            }
            stream_copy_to_stream($stream, $out);
            fclose($out);
            fclose($stream);
            $this->importPdf($tmp, $this->cleanUploadName($entry), $category, $status, $result, true);
        }
        $zip->close();
    }

    private function importPdf(
        string $tmpName,
        string $name,
        string $category,
        string $status,
        array &$result,
        bool $temporary = false
    ): void {
        try {
            if (!LegalFileStorage::isPdf($tmpName, $name)) {
                $result['errors'][] = "$name no es un PDF válido.";
                return;
            }
            $meta = $this->documentMeta($name, $category);
            $destination = LegalDocumentRepository::storagePath($meta['storage_filename']);
            $bytes = (int) filesize($tmpName);
            if ($this->sameFile($destination, $tmpName, $bytes)) {
                $this->documents->saveImportedDocument($meta, $status, $bytes, $this->pdfBlob($destination));
                $result['skipped'][] = $name;
                return;
            }
            $storedBytes = $temporary ? $this->copyTemporaryPdf($tmpName, $destination)
                : LegalFileStorage::storeUploaded($tmpName, $destination);
            $this->documents->saveImportedDocument($meta, $status, $storedBytes, $this->pdfBlob($destination));
            $result['copied'][] = $name;
        } catch (\Throwable $error) {
            $result['errors'][] = "$name no se pudo guardar: " . $error->getMessage();
        } finally {
            if ($temporary && is_file($tmpName)) unlink($tmpName);
        }
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
        $catalog = $this->catalogMatch($name, $category);
        if ($catalog !== null) {
            $filename = (string) ($catalog['storage_filename'] ?: $catalog['slug'] . '.pdf');
            return ['slug' => $catalog['slug'], 'category_code' => $category,
                'document_code' => $catalog['document_code'], 'title' => $catalog['title'],
                'document_type' => $catalog['document_type'], 'source_filename' => $name,
                'storage_filename' => $filename];
        }
        $base = trim((string) preg_replace('/\s+/', ' ', pathinfo($name, PATHINFO_FILENAME)));
        $title = mb_substr($base !== '' ? $base : 'Documento jurídico', 0, 240);
        $hash = substr(hash('sha1', $category . '|' . $name), 0, 10);
        $slug = $this->slug($category . '-' . $title . '-' . $hash);
        return ['slug' => $slug, 'category_code' => $category, 'document_code' => mb_substr($title, 0, 80),
            'title' => $title, 'document_type' => $this->inferType($title),
            'source_filename' => $name, 'storage_filename' => $slug . '.pdf'];
    }

    private function catalogMatch(string $name, string $category): ?array
    {
        $base = pathinfo($name, PATHINFO_FILENAME);
        $needle = $this->normalize($base);
        $needleToken = $this->legalToken($needle);
        foreach ($this->documents->catalogCandidates($category) as $candidate) {
            $title = $this->normalize((string) $candidate['title']);
            $haystack = $this->normalize($candidate['document_code'] . ' ' . $candidate['title']);
            if ($needle !== '' && ($needle === $title || str_contains($needle, $title) || str_contains($haystack, $needle))) {
                return $candidate;
            }
            if ($needleToken !== '' && $needleToken === $this->legalToken($haystack)) {
                return $candidate;
            }
        }
        return null;
    }

    private function normalize(string $value): string
    {
        $ascii = function_exists('iconv') ? iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) : false;
        $seed = strtolower($ascii !== false ? $ascii : $value);
        return (string) preg_replace('/[^a-z0-9]+/', '', $seed);
    }

    private function legalToken(string $value): string
    {
        if (preg_match('/(ley|decreto|resolucion)(?:igac)?(\d+)(?:de)?(\d{4})/', $value, $match)) {
            return $match[1] . $match[2] . $match[3];
        }
        return '';
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

    private function pdfBlob(string $path): string
    {
        $blob = file_get_contents($path);
        if (!is_string($blob)) {
            throw new \RuntimeException('El PDF se guardó en disco, pero no se pudo crear el respaldo interno.');
        }
        return $blob;
    }

    private function copyTemporaryPdf(string $tmpName, string $destination): int
    {
        if (!copy($tmpName, $destination)) {
            throw new \RuntimeException('No se pudo copiar el PDF extraído del ZIP.');
        }
        clearstatcache(true, $destination);
        if (!LegalFileStorage::isPdf($destination, $destination)) {
            throw new \RuntimeException('El PDF extraído no quedó verificado.');
        }
        return (int) filesize($destination);
    }

    private function isPdfName(string $name): bool { return mb_strtolower(pathinfo($name, PATHINFO_EXTENSION)) === 'pdf'; }

    private function isZipName(string $name): bool { return mb_strtolower(pathinfo($name, PATHINFO_EXTENSION)) === 'zip'; }

    private function cleanUploadName(string $name): string { return basename(str_replace('\\', '/', $name)); }
}
