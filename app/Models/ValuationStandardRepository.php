<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Env;
use App\Core\HttpException;
use PDO;

final class ValuationStandardRepository
{
    public function __construct(private PDO $db) {}

    public static function storageDir(): string
    {
        $configured = trim(str_replace('\\', '/', Env::get('NTS_STORAGE_DIR')));
        if ($configured === '') return BASE_PATH . '/storage/normas-tecnicas-sectoriales';
        $absolute = str_starts_with($configured, '/') || preg_match('/^[A-Z]:\//i', $configured);
        return rtrim($absolute ? $configured : BASE_PATH . '/' . trim($configured, '/'), '/');
    }

    public static function storagePath(string $filename): string { return self::storageDir() . '/' . basename($filename); }

    public function categoriesWithStandards(): array
    {
        $rows = $this->db->query("SELECT c.code category_code, c.name category_name, c.group_type,
            c.sort_order category_sort, s.slug, s.standard_code, s.title, s.kind, s.sector_code,
            s.source_filename, s.storage_filename, s.file_size_bytes, s.imported_at, s.sort_order standard_sort
            FROM valuation_standard_categories c
            LEFT JOIN valuation_standards s ON s.category_code = c.code
            ORDER BY c.sort_order ASC, s.sort_order ASC")->fetchAll();
        $categories = [];
        foreach ($rows as $row) {
            $code = (string) $row['category_code'];
            $categories[$code] ??= [
                'code' => $code,
                'name' => $row['category_name'],
                'group_type' => $row['group_type'],
                'standards' => [],
            ];
            if ($row['slug'] !== null) {
                $categories[$code]['standards'][] = $this->hydrateStandard($row);
            }
        }
        return array_values($categories);
    }

    public function find(string $slug): array
    {
        $query = $this->db->prepare("SELECT s.*, c.code category_code, c.name category_name
            FROM valuation_standards s
            INNER JOIN valuation_standard_categories c ON c.code = s.category_code
            WHERE s.slug = ?");
        $query->execute([$slug]);
        $row = $query->fetch();
        if (!$row) throw new HttpException(404, 'No se encontró la norma técnica.');
        return $this->hydrateStandard($row);
    }

    public function importFrom(string $sourceDirectory): array
    {
        $sourceRoot = realpath($sourceDirectory);
        if ($sourceRoot === false || !is_dir($sourceRoot)) {
            throw new \RuntimeException('La carpeta de origen no existe o no es accesible.');
        }
        if (!is_dir(self::storageDir()) && !mkdir(self::storageDir(), 0775, true) && !is_dir(self::storageDir())) {
            throw new \RuntimeException('No se pudo preparar el almacenamiento privado.');
        }
        $summary = ['copied' => [], 'skipped' => [], 'missing' => []];
        foreach ($this->allStandards() as $standard) {
            $source = $sourceRoot . DIRECTORY_SEPARATOR . $standard['source_filename'];
            if (!is_file($source)) {
                $summary['missing'][] = $standard['source_filename'];
                continue;
            }
            $destination = self::storagePath($standard['storage_filename']);
            if (is_file($destination) && filesize($destination) === filesize($source)) {
                $this->markImported($standard['slug'], (int) filesize($destination));
                $summary['skipped'][] = $standard['source_filename'];
                continue;
            }
            if (!copy($source, $destination)) {
                throw new \RuntimeException('No se pudo copiar ' . $standard['source_filename']);
            }
            $this->markImported($standard['slug'], (int) filesize($destination));
            $summary['copied'][] = $standard['source_filename'];
        }
        return $summary;
    }

    public function importUploaded(array $files): array
    {
        $this->ensureStorageDir();
        $standards = $this->allStandards();
        $byName = [];
        foreach ($standards as $standard) {
            $byName[$this->filenameKey($standard['source_filename'])] = $standard;
        }
        $result = ['ok' => true, 'copied' => [], 'skipped' => [], 'unknown' => [], 'errors' => [], 'missing' => []];
        foreach ($this->uploadedFiles($files) as $file) {
            $name = $this->cleanUploadName($file['name']);
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $result['errors'][] = "$name no se pudo recibir.";
                continue;
            }
            if (!$this->isPdf($file['tmp_name'], $name)) {
                $result['errors'][] = "$name no es un PDF válido.";
                continue;
            }
            $standard = $byName[$this->filenameKey($name)] ?? null;
            if (!$standard) {
                $result['unknown'][] = $name;
                continue;
            }
            $destination = self::storagePath($standard['storage_filename']);
            $bytes = (int) filesize($file['tmp_name']);
            $sameFile = is_file($destination) && filesize($destination) === $bytes
                && hash_file('sha256', $destination) === hash_file('sha256', $file['tmp_name']);
            if ($sameFile) {
                $this->markImported($standard['slug'], $bytes);
                $result['skipped'][] = $standard['source_filename'];
                continue;
            }
            $moved = move_uploaded_file($file['tmp_name'], $destination)
                || (PHP_SAPI === 'cli' && rename($file['tmp_name'], $destination));
            if (!$moved) {
                $result['errors'][] = "$name no se pudo guardar.";
                continue;
            }
            $this->markImported($standard['slug'], (int) filesize($destination));
            $result['copied'][] = $standard['source_filename'];
        }
        foreach ($standards as $standard) {
            if (!is_file(self::storagePath($standard['storage_filename']))) {
                $result['missing'][] = $standard['source_filename'];
            }
        }
        return $result;
    }

    private function allStandards(): array
    {
        return $this->db->query('SELECT slug, source_filename, storage_filename FROM valuation_standards ORDER BY sort_order')
            ->fetchAll();
    }

    private function ensureStorageDir(): void
    {
        if (!is_dir(self::storageDir()) && !mkdir(self::storageDir(), 0775, true) && !is_dir(self::storageDir())) {
            throw new \RuntimeException('No se pudo preparar el almacenamiento privado.');
        }
    }

    private function uploadedFiles(array $files): array
    {
        $names = $files['name'] ?? [];
        if (!is_array($names) || $names === []) {
            throw new \RuntimeException('Selecciona al menos un PDF para importar.');
        }
        $uploads = [];
        foreach (array_keys($names) as $index) {
            $uploads[] = ['name' => (string) ($files['name'][$index] ?? ''),
                'tmp_name' => (string) ($files['tmp_name'][$index] ?? ''),
                'error' => (int) ($files['error'][$index] ?? UPLOAD_ERR_NO_FILE)];
        }
        return $uploads;
    }

    private function cleanUploadName(string $name): string { return basename(str_replace('\\', '/', $name)); }

    private function filenameKey(string $name): string
    {
        return mb_strtolower((string) preg_replace('/\s+/', ' ', trim($this->cleanUploadName($name))));
    }

    private function isPdf(string $path, string $name): bool
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

    private function markImported(string $slug, int $bytes): void
    {
        $query = $this->db->prepare('UPDATE valuation_standards
            SET file_size_bytes = ?, imported_at = ?, updated_at = ? WHERE slug = ?');
        $now = gmdate('Y-m-d H:i:s');
        $query->execute([$bytes, $now, $now, $slug]);
    }

    private function hydrateStandard(array $row): array
    {
        $filename = (string) $row['storage_filename'];
        $path = self::storagePath($filename);
        return [
            'slug' => $row['slug'],
            'category_code' => $row['category_code'],
            'category_name' => $row['category_name'],
            'standard_code' => $row['standard_code'],
            'title' => $row['title'],
            'kind' => $row['kind'],
            'sector_code' => $row['sector_code'],
            'source_filename' => $row['source_filename'],
            'storage_filename' => $filename,
            'summary' => $row['summary'] ?? '',
            'file_size_bytes' => $row['file_size_bytes'] !== null ? (int) $row['file_size_bytes'] : null,
            'imported_at' => $row['imported_at'] ?? null,
            'has_file' => is_file($path),
            'file_path' => $path,
        ];
    }
}
