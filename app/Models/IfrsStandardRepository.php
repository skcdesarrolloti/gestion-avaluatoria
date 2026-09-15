<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\HttpException;
use App\Services\IfrsFileStorage;
use PDO;

final class IfrsStandardRepository
{
    public function __construct(private PDO $db) {}

    public static function storageDir(): string { return IfrsFileStorage::dir(); }

    public static function storagePath(string $filename): string { return IfrsFileStorage::path($filename); }

    public function groupsWithStandards(): array
    {
        $rows = $this->db->query("SELECT g.code group_code, g.name group_name, s.slug,
            s.standard_code, s.title, s.applicable_categories, s.measurement_focus,
            s.summary, s.field_relevance, s.source_reference, s.status, s.source_filename,
            s.storage_filename, s.file_size_bytes, s.imported_at, s.sort_order standard_sort
            FROM valuation_ifrs_groups g LEFT JOIN valuation_ifrs_standards s ON s.group_code = g.code
            ORDER BY g.sort_order ASC, s.sort_order ASC")->fetchAll();
        $groups = [];
        foreach ($rows as $row) {
            $code = (string) $row['group_code'];
            $groups[$code] ??= ['code' => $code, 'name' => $row['group_name'], 'standards' => []];
            if ($row['slug'] !== null) $groups[$code]['standards'][] = $this->hydrate($row);
        }
        return array_values($groups);
    }

    public function considerations(): array
    {
        return $this->db->query("SELECT field_key, field_label, classification, normative_basis,
            operational_use, ifrs_relation FROM valuation_field_considerations
            ORDER BY sort_order ASC")->fetchAll();
    }

    public function find(string $slug): array
    {
        $query = $this->db->prepare("SELECT s.*, g.name group_name FROM valuation_ifrs_standards s
            INNER JOIN valuation_ifrs_groups g ON g.code = s.group_code WHERE s.slug = ?");
        $query->execute([$slug]);
        $row = $query->fetch();
        if (!$row) throw new HttpException(404, 'No se encontró la norma NIIF.');
        return $this->hydrate($row);
    }

    public function storageReport(): array
    {
        $standards = $this->standardsWithStorage();
        $present = $markedMissing = 0;
        foreach ($standards as $standard) {
            $exists = $standard['storage_filename'] !== ''
                && is_file(self::storagePath((string) $standard['storage_filename']));
            $present += $exists ? 1 : 0;
            $markedMissing += (!$exists && $standard['file_size_bytes'] !== null) ? 1 : 0;
        }
        return ['dir' => self::storageDir(), 'configured' => IfrsFileStorage::configured(),
            'writable' => IfrsFileStorage::writable(), 'present' => $present,
            'total' => count($standards), 'marked_missing' => $markedMissing,
            'limits' => IfrsFileStorage::limits()];
    }

    public function stats(array $groups): array
    {
        $stats = ['total' => 0, 'vigente' => 0, 'available' => 0, 'missing' => 0];
        foreach ($groups as $group) {
            foreach ($group['standards'] as $standard) {
                $stats['total']++;
                $stats[(string) $standard['status']] = ($stats[(string) $standard['status']] ?? 0) + 1;
                $stats[$standard['has_file'] ? 'available' : 'missing']++;
            }
        }
        return $stats;
    }

    public function saveImportedFile(string $slug, string $sourceName, string $storageName, int $bytes): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $query = $this->db->prepare("UPDATE valuation_ifrs_standards SET source_filename = ?,
            storage_filename = ?, file_size_bytes = ?, imported_at = ?, updated_at = ? WHERE slug = ?");
        $query->execute([$sourceName, $storageName, $bytes, $now, $now, $slug]);
    }

    private function standardsWithStorage(): array
    {
        return $this->db->query("SELECT slug, storage_filename, file_size_bytes
            FROM valuation_ifrs_standards WHERE storage_filename <> '' ORDER BY sort_order")->fetchAll();
    }

    private function hydrate(array $row): array
    {
        $filename = (string) ($row['storage_filename'] ?? '');
        $path = $filename !== '' ? self::storagePath($filename) : '';
        return ['slug' => $row['slug'], 'group_code' => $row['group_code'], 'group_name' => $row['group_name'],
            'standard_code' => $row['standard_code'], 'title' => $row['title'],
            'applicable_categories' => $row['applicable_categories'] ?? '',
            'measurement_focus' => $row['measurement_focus'] ?? '', 'summary' => $row['summary'] ?? '',
            'field_relevance' => $row['field_relevance'] ?? '', 'source_reference' => $row['source_reference'] ?? '',
            'status' => $row['status'], 'source_filename' => $row['source_filename'] ?? '',
            'storage_filename' => $filename, 'file_size_bytes' => $row['file_size_bytes'] !== null ? (int) $row['file_size_bytes'] : null,
            'imported_at' => $row['imported_at'] ?? null, 'has_file' => $path !== '' && is_file($path), 'file_path' => $path];
    }
}
