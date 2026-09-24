<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\HttpException;
use App\Services\UrbanNormFileStorage;
use PDO;

final class UrbanNormativeRepository
{
    public function __construct(private PDO $db) {}

    public static function storageDir(): string { return UrbanNormFileStorage::dir(); }

    public static function storagePath(string $filename): string { return UrbanNormFileStorage::path($filename); }

    public function documentsWithTables(): array
    {
        $rows = $this->db->query('SELECT d.*, t.slug table_slug, t.table_code, t.title table_title,
            t.scope, t.page_start, t.page_end FROM urban_norm_documents d
            LEFT JOIN urban_norm_tables t ON t.document_slug = d.slug
            ORDER BY d.sort_order ASC, t.sort_order ASC')->fetchAll();
        $documents = [];
        foreach ($rows as $row) {
            $slug = (string) $row['slug'];
            $documents[$slug] ??= $this->hydrate($row) + ['tables' => []];
            if ($row['table_slug'] !== null) {
                $documents[$slug]['tables'][] = ['slug' => $row['table_slug'],
                    'table_code' => $row['table_code'], 'title' => $row['table_title'],
                    'scope' => $row['scope'], 'page_start' => $row['page_start'], 'page_end' => $row['page_end']];
            }
        }
        return array_values($documents);
    }

    public function documents(): array
    {
        return array_map(fn (array $row): array => $this->hydrate($row), $this->db->query(
            'SELECT * FROM urban_norm_documents ORDER BY sort_order ASC, title ASC')->fetchAll());
    }

    public function find(string $slug): array
    {
        $query = $this->db->prepare('SELECT * FROM urban_norm_documents WHERE slug = ?');
        $query->execute([$slug]);
        $row = $query->fetch();
        if (!$row) throw new HttpException(404, 'No se encontró el documento de normatividad urbana.');
        return $this->hydrate($row);
    }

    public function categoriesForTable(string $tableSlug): array
    {
        $query = $this->db->prepare('SELECT * FROM urban_norm_use_categories
            WHERE table_slug = ? ORDER BY sort_order ASC');
        $query->execute([$tableSlug]);
        return $query->fetchAll();
    }

    public function categories(): array
    {
        return $this->db->query('SELECT c.*, t.table_code, t.title table_title
            FROM urban_norm_use_categories c JOIN urban_norm_tables t ON t.slug = c.table_slug
            ORDER BY t.sort_order ASC, c.sort_order ASC')->fetchAll();
    }

    public function categoryWithRules(string $slug): array
    {
        $query = $this->db->prepare('SELECT c.*, t.table_code, t.title table_title, d.title document_title
            FROM urban_norm_use_categories c JOIN urban_norm_tables t ON t.slug = c.table_slug
            JOIN urban_norm_documents d ON d.slug = t.document_slug WHERE c.slug = ?');
        $query->execute([$slug]);
        $category = $query->fetch();
        if (!$category) throw new HttpException(404, 'No se encontró la categoría normativa urbana.');
        $rules = $this->db->prepare('SELECT rule_type, content FROM urban_norm_use_rules
            WHERE category_slug = ? ORDER BY sort_order ASC');
        $rules->execute([$slug]);
        $category['rules'] = [];
        foreach ($rules->fetchAll() as $rule) $category['rules'][(string) $rule['rule_type']] = (string) $rule['content'];
        return $category;
    }

    public function stats(?array $documents = null): array
    {
        if ($documents !== null) return ['documents' => count($documents),
            'available' => count(array_filter($documents, fn ($d): bool => (bool) $d['has_file'])),
            'missing' => count(array_filter($documents, fn ($d): bool => !(bool) $d['has_file']))];
        return ['documents' => (int) $this->db->query('SELECT COUNT(*) FROM urban_norm_documents')->fetchColumn(),
            'tables' => (int) $this->db->query('SELECT COUNT(*) FROM urban_norm_tables')->fetchColumn(),
            'categories' => (int) $this->db->query('SELECT COUNT(*) FROM urban_norm_use_categories')->fetchColumn(),
            'rules' => (int) $this->db->query('SELECT COUNT(*) FROM urban_norm_use_rules')->fetchColumn()];
    }

    public function storageReport(): array
    {
        $documents = $this->documents();
        $markedMissing = count(array_filter($documents, fn ($d): bool => !$d['has_file'] && $d['file_size_bytes'] !== null));
        return ['dir' => self::storageDir(), 'configured' => UrbanNormFileStorage::configured(),
            'writable' => UrbanNormFileStorage::writable(), 'present' => count(array_filter($documents, fn ($d): bool => $d['has_file'])),
            'total' => count($documents), 'marked_missing' => $markedMissing, 'limits' => UrbanNormFileStorage::limits()];
    }

    public function saveImportedFile(string $slug, string $sourceName, string $storageName, int $bytes, string $blob): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $query = $this->db->prepare('UPDATE urban_norm_documents SET source_filename = ?,
            storage_filename = ?, file_size_bytes = ?, pdf_blob = ?, imported_at = ?, updated_at = ? WHERE slug = ?');
        $query->execute([$sourceName, $storageName, $bytes, $blob, $now, $now, $slug]);
    }

    private function hydrate(array $row): array
    {
        $filename = (string) ($row['storage_filename'] ?? '');
        $path = $filename !== '' ? self::storagePath($filename) : '';
        $hasBlob = is_string($row['pdf_blob'] ?? null) && $row['pdf_blob'] !== '';
        return $row + ['has_blob' => $hasBlob, 'has_file' => ($path !== '' && is_file($path)) || $hasBlob,
            'file_path' => $path, 'storage_filename' => $filename, 'source_filename' => $row['source_filename'] ?? ''];
    }
}
