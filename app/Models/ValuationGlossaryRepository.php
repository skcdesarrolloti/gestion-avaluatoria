<?php
declare(strict_types=1);
namespace App\Models;
use PDO;

final class ValuationGlossaryRepository
{
    public function __construct(private PDO $db) {}

    public function all(string $query = ''): array
    {
        $query = trim($query);
        if ($query === '') {
            return $this->db->query('SELECT * FROM valuation_glossary_terms
                WHERE active = 1 ORDER BY sort_order ASC, term ASC')->fetchAll();
        }
        $like = '%' . $query . '%';
        $stmt = $this->db->prepare('SELECT * FROM valuation_glossary_terms WHERE active = 1
            AND (term LIKE ? OR definition LIKE ? OR source_note LIKE ?)
            ORDER BY term ASC');
        $stmt->execute([$like, $like, $like]);
        return $stmt->fetchAll();
    }

    public function create(array $input, int $owner): string
    {
        $term = $this->text($input, 'term', 180);
        $definition = $this->text($input, 'definition', 5000);
        if ($term === '' || $definition === '') {
            throw new \InvalidArgumentException('Registra el factor o concepto y su descripción.');
        }
        $source = $this->text($input, 'source_note', 260) ?: 'Carga manual del analista';
        $slug = $this->uniqueSlug($term);
        $now = gmdate('Y-m-d H:i:s');
        $sort = (int) $this->db->query('SELECT COALESCE(MAX(sort_order), 0) + 10 FROM valuation_glossary_terms')->fetchColumn();
        $stmt = $this->db->prepare('INSERT INTO valuation_glossary_terms
            (slug, term, definition, source_note, created_by, sort_order, active, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, 1, ?, ?)');
        $stmt->execute([$slug, $term, $definition, $source, $owner, $sort, $now, $now]);
        return $slug;
    }

    public function stats(): array
    {
        return ['total' => (int) $this->db->query('SELECT COUNT(*) FROM valuation_glossary_terms WHERE active = 1')->fetchColumn(),
            'manual' => (int) $this->db->query("SELECT COUNT(*) FROM valuation_glossary_terms WHERE active = 1 AND source_note = 'Carga manual del analista'")->fetchColumn()];
    }

    private function uniqueSlug(string $term): string
    {
        $base = $this->slug($term);
        $slug = $base;
        $i = 2;
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM valuation_glossary_terms WHERE slug = ?');
        while (true) {
            $stmt->execute([$slug]);
            if ((int) $stmt->fetchColumn() === 0) return $slug;
            $suffix = '-' . $i++;
            $slug = substr($base, 0, 150 - strlen($suffix)) . $suffix;
        }
    }

    private function slug(string $term): string
    {
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', mb_strtolower($term)) ?: mb_strtolower($term);
        $slug = trim(preg_replace('/[^a-z0-9]+/', '-', $ascii) ?? '', '-');
        return $slug !== '' ? substr($slug, 0, 150) : substr(hash('sha256', $term), 0, 32);
    }

    private function text(array $input, string $key, int $limit): string
    {
        $value = trim((string) ($input[$key] ?? ''));
        return mb_substr($value, 0, $limit);
    }
}
