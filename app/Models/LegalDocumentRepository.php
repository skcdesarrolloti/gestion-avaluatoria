<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\HttpException;
use App\Services\LegalFileStorage;
use PDO;

final class LegalDocumentRepository
{
    public function __construct(private PDO $db) {}

    public static function storageDir(): string { return LegalFileStorage::dir(); }

    public static function storagePath(string $filename): string { return LegalFileStorage::path($filename); }

    public function categoriesWithDocuments(): array
    {
        $rows = $this->db->query("SELECT c.code category_code, c.name category_name, c.group_type,
            d.slug, d.document_code, d.title, d.document_type, d.status, d.issued_at,
            d.repealed_at, d.source_reference, d.summary, d.source_filename, d.storage_filename,
            d.file_size_bytes, d.imported_at, d.sort_order document_sort
            FROM valuation_legal_categories c
            LEFT JOIN valuation_legal_documents d ON d.category_code = c.code
            ORDER BY c.sort_order ASC, d.sort_order ASC")->fetchAll();
        $categories = [];
        foreach ($rows as $row) {
            $code = (string) $row['category_code'];
            $categories[$code] ??= [
                'code' => $code,
                'name' => $row['category_name'],
                'group_type' => $row['group_type'],
                'documents' => [],
                'articles' => [],
            ];
            if ($row['slug'] !== null) {
                $categories[$code]['documents'][] = $this->hydrate($row);
            }
        }
        foreach ($this->articles() as $article) {
            $code = (string) $article['category_code'];
            if (isset($categories[$code])) $categories[$code]['articles'][] = $article;
        }
        return array_values($categories);
    }

    public function find(string $slug): array
    {
        $query = $this->db->prepare("SELECT d.*, c.name category_name
            FROM valuation_legal_documents d
            INNER JOIN valuation_legal_categories c ON c.code = d.category_code
            WHERE d.slug = ?");
        $query->execute([$slug]);
        $row = $query->fetch();
        if (!$row) throw new HttpException(404, 'No se encontró el documento jurídico.');
        return $this->hydrate($row);
    }

    public function storageReport(): array
    {
        $documents = $this->documentsWithStorage();
        $present = $markedMissing = 0;
        foreach ($documents as $document) {
            $exists = $document['storage_filename'] !== ''
                && is_file(self::storagePath((string) $document['storage_filename']));
            $present += $exists ? 1 : 0;
            $markedMissing += (!$exists && $document['file_size_bytes'] !== null) ? 1 : 0;
        }
        return ['dir' => self::storageDir(), 'configured' => LegalFileStorage::configured(),
            'writable' => LegalFileStorage::writable(), 'present' => $present,
            'total' => count($documents), 'marked_missing' => $markedMissing,
            'limits' => LegalFileStorage::limits()];
    }

    public function stats(array $categories): array
    {
        $stats = ['total' => 0, 'articles' => 0, 'vigente' => 0, 'derogada' => 0, 'historica' => 0];
        foreach ($categories as $category) {
            foreach ($category['documents'] as $document) {
                $stats['total']++;
                $status = (string) $document['status'];
                $stats[$status] = ($stats[$status] ?? 0) + 1;
            }
            $stats['articles'] += count($category['articles']);
        }
        return $stats;
    }

    public function categoryCodeOrDefault(string $code): string
    {
        $code = preg_replace('/[^A-Za-z0-9]/', '', $code) ?: 'A';
        $query = $this->db->prepare('SELECT code FROM valuation_legal_categories WHERE code = ?');
        $query->execute([$code]);
        return (string) ($query->fetchColumn() ?: 'A');
    }

    public function saveImportedDocument(array $meta, string $status, int $bytes): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $values = [$meta['slug'], $meta['category_code'], $meta['document_code'], $meta['title'],
            $meta['document_type'], $status, $meta['source_filename'], $meta['storage_filename'],
            $bytes, $this->nextSortOrder($meta['category_code']), $now, $now];
        if ($this->db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite') {
            $sql = "INSERT INTO valuation_legal_documents
                (slug, category_code, document_code, title, document_type, status, source_filename,
                storage_filename, file_size_bytes, imported_at, sort_order, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON CONFLICT(slug) DO UPDATE SET status = excluded.status,
                file_size_bytes = excluded.file_size_bytes, imported_at = excluded.imported_at,
                updated_at = excluded.updated_at";
            $this->db->prepare($sql)->execute([...array_slice($values, 0, 9), $now, ...array_slice($values, 9)]);
            return;
        }
        $sql = "INSERT INTO valuation_legal_documents
            (slug, category_code, document_code, title, document_type, status, source_filename,
            storage_filename, file_size_bytes, imported_at, sort_order, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE status = VALUES(status), source_filename = VALUES(source_filename),
            storage_filename = VALUES(storage_filename), file_size_bytes = VALUES(file_size_bytes),
            imported_at = VALUES(imported_at), updated_at = VALUES(updated_at)";
        $this->db->prepare($sql)->execute([...array_slice($values, 0, 9), $now, ...array_slice($values, 9)]);
    }

    private function articles(): array
    {
        return $this->db->query("SELECT a.id, a.category_code, a.article_label, a.title, a.excerpt,
            a.applicability, a.status, d.document_code, d.title document_title, d.document_type
            FROM valuation_legal_articles a
            INNER JOIN valuation_legal_documents d ON d.slug = a.document_slug
            ORDER BY a.category_code ASC, a.sort_order ASC")->fetchAll();
    }

    private function documentsWithStorage(): array
    {
        return $this->db->query("SELECT slug, storage_filename, file_size_bytes
            FROM valuation_legal_documents WHERE storage_filename <> '' ORDER BY sort_order")->fetchAll();
    }

    private function nextSortOrder(string $category): int
    {
        $query = $this->db->prepare('SELECT COALESCE(MAX(sort_order), 0) + 1
            FROM valuation_legal_documents WHERE category_code = ?');
        $query->execute([$category]);
        return (int) $query->fetchColumn();
    }

    private function hydrate(array $row): array
    {
        $filename = (string) ($row['storage_filename'] ?? '');
        $path = $filename !== '' ? self::storagePath($filename) : '';
        return [
            'slug' => $row['slug'],
            'category_code' => $row['category_code'],
            'category_name' => $row['category_name'],
            'document_code' => $row['document_code'],
            'title' => $row['title'],
            'document_type' => $row['document_type'],
            'status' => $row['status'],
            'issued_at' => $row['issued_at'] ?? null,
            'repealed_at' => $row['repealed_at'] ?? null,
            'source_reference' => $row['source_reference'] ?? '',
            'summary' => $row['summary'] ?? '',
            'source_filename' => $row['source_filename'] ?? '',
            'storage_filename' => $filename,
            'file_size_bytes' => $row['file_size_bytes'] !== null ? (int) $row['file_size_bytes'] : null,
            'imported_at' => $row['imported_at'] ?? null,
            'has_file' => $path !== '' && is_file($path),
            'file_path' => $path,
        ];
    }
}
