<?php
declare(strict_types=1);
namespace App\Models;
use PDO;

final class InternationalStandardRepository
{
    public function __construct(private PDO $db) {}

    public function groupsWithStandards(): array
    {
        $rows = $this->db->query("SELECT g.code group_code, g.name group_name, s.slug,
            s.standard_code, s.title, s.applicable_categories, s.summary, s.effective_from,
            s.status, s.source_reference, s.sort_order standard_sort
            FROM valuation_international_groups g
            LEFT JOIN valuation_international_standards s ON s.group_code = g.code
            ORDER BY g.sort_order ASC, s.sort_order ASC")->fetchAll();
        $groups = [];
        foreach ($rows as $row) {
            $code = (string) $row['group_code'];
            $groups[$code] ??= ['code' => $code, 'name' => $row['group_name'], 'standards' => []];
            if ($row['slug'] !== null) $groups[$code]['standards'][] = $this->hydrate($row);
        }
        return array_values($groups);
    }

    public function stats(array $groups): array
    {
        $stats = ['total' => 0, 'vigente' => 0, 'historica' => 0];
        foreach ($groups as $group) {
            foreach ($group['standards'] as $standard) {
                $stats['total']++;
                $status = (string) $standard['status'];
                $stats[$status] = ($stats[$status] ?? 0) + 1;
            }
        }
        return $stats;
    }

    private function hydrate(array $row): array
    {
        return [
            'slug' => $row['slug'],
            'group_code' => $row['group_code'],
            'group_name' => $row['group_name'],
            'standard_code' => $row['standard_code'],
            'title' => $row['title'],
            'applicable_categories' => $row['applicable_categories'] ?? '',
            'summary' => $row['summary'] ?? '',
            'effective_from' => $row['effective_from'] ?? null,
            'status' => $row['status'],
            'source_reference' => $row['source_reference'] ?? '',
        ];
    }
}
