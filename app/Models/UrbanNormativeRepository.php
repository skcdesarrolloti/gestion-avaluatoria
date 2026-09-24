<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\HttpException;
use PDO;

final class UrbanNormativeRepository
{
    public function __construct(private PDO $db) {}

    public function documentsWithTables(): array
    {
        $rows = $this->db->query('SELECT d.slug document_slug, d.title document_title,
            d.document_type, d.issuer, d.normative_reference, d.status, d.source_filename,
            t.slug table_slug, t.table_code, t.title table_title, t.scope, t.page_start, t.page_end
            FROM urban_norm_documents d LEFT JOIN urban_norm_tables t ON t.document_slug = d.slug
            ORDER BY d.sort_order ASC, t.sort_order ASC')->fetchAll();
        $documents = [];
        foreach ($rows as $row) {
            $slug = (string) $row['document_slug'];
            $documents[$slug] ??= ['slug' => $slug, 'title' => $row['document_title'],
                'document_type' => $row['document_type'], 'issuer' => $row['issuer'],
                'normative_reference' => $row['normative_reference'], 'status' => $row['status'],
                'source_filename' => $row['source_filename'], 'tables' => []];
            if ($row['table_slug'] !== null) {
                $documents[$slug]['tables'][] = ['slug' => $row['table_slug'],
                    'table_code' => $row['table_code'], 'title' => $row['table_title'],
                    'scope' => $row['scope'], 'page_start' => $row['page_start'], 'page_end' => $row['page_end']];
            }
        }
        return array_values($documents);
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
            FROM urban_norm_use_categories c
            JOIN urban_norm_tables t ON t.slug = c.table_slug
            JOIN urban_norm_documents d ON d.slug = t.document_slug WHERE c.slug = ?');
        $query->execute([$slug]);
        $category = $query->fetch();
        if (!$category) throw new HttpException(404, 'No se encontró la categoría normativa urbana.');
        $rules = $this->db->prepare('SELECT rule_type, content FROM urban_norm_use_rules
            WHERE category_slug = ? ORDER BY sort_order ASC');
        $rules->execute([$slug]);
        $category['rules'] = [];
        foreach ($rules->fetchAll() as $rule) {
            $category['rules'][(string) $rule['rule_type']] = (string) $rule['content'];
        }
        return $category;
    }

    public function stats(): array
    {
        return [
            'documents' => (int) $this->db->query('SELECT COUNT(*) FROM urban_norm_documents')->fetchColumn(),
            'tables' => (int) $this->db->query('SELECT COUNT(*) FROM urban_norm_tables')->fetchColumn(),
            'categories' => (int) $this->db->query('SELECT COUNT(*) FROM urban_norm_use_categories')->fetchColumn(),
            'rules' => (int) $this->db->query('SELECT COUNT(*) FROM urban_norm_use_rules')->fetchColumn(),
        ];
    }
}
