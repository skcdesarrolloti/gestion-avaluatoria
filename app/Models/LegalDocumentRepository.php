<?php
declare(strict_types=1);
namespace App\Models;
use PDO;

final class LegalDocumentRepository
{
    public function __construct(private PDO $db) {}

    public function categoriesWithDocuments(): array
    {
        $rows = $this->db->query("SELECT c.code category_code, c.name category_name, c.group_type,
            d.slug, d.document_code, d.title, d.document_type, d.status, d.issued_at,
            d.repealed_at, d.source_reference, d.summary, d.sort_order document_sort
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
            ];
            if ($row['slug'] !== null) {
                $categories[$code]['documents'][] = $this->hydrate($row);
            }
        }
        return array_values($categories);
    }

    public function stats(array $categories): array
    {
        $stats = ['total' => 0, 'vigente' => 0, 'derogada' => 0, 'historica' => 0];
        foreach ($categories as $category) {
            foreach ($category['documents'] as $document) {
                $stats['total']++;
                $status = (string) $document['status'];
                $stats[$status] = ($stats[$status] ?? 0) + 1;
            }
        }
        return $stats;
    }

    private function hydrate(array $row): array
    {
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
        ];
    }
}
