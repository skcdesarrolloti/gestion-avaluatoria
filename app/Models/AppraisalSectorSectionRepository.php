<?php
declare(strict_types=1);
namespace App\Models;
use App\Support\AppraisalSectorAdvancedCatalog;
use PDO;

final class AppraisalSectorSectionRepository
{
    public function __construct(private PDO $db) {}

    public function sections(string $appraisalId, int $owner): array
    {
        $query = $this->db->prepare('SELECT * FROM appraisal_sector_profile_sections
            WHERE appraisal_id = ? AND owner_id = ? ORDER BY section_code');
        $query->execute([$appraisalId, $owner]);
        return array_column($query->fetchAll() ?: [], null, 'section_code');
    }

    public function saveAll(string $appraisalId, int $owner, string $neighborhoodId, array $sections): void
    {
        if ($this->db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite') {
            $this->saveAllSqlite($appraisalId, $owner, $neighborhoodId, $sections);
            return;
        }
        $now = gmdate('Y-m-d H:i:s');
        $sql = 'INSERT INTO appraisal_sector_profile_sections
            (appraisal_id, owner_id, neighborhood_id, section_code, section_title, status, data_json,
             content_text, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE neighborhood_id = VALUES(neighborhood_id),
                section_title = VALUES(section_title), status = VALUES(status),
                data_json = VALUES(data_json), content_text = VALUES(content_text),
                version = version + 1, updated_at = VALUES(updated_at)';
        $query = $this->db->prepare($sql);
        foreach (AppraisalSectorAdvancedCatalog::sections() as $code => [$title]) {
            $code = (string) $code;
            $data = $sections[$code] ?? [];
            $query->execute([$appraisalId, $owner, $neighborhoodId ?: null, $code, $title,
                self::status($data), self::json($data), self::summary($title, $data), $now]);
        }
    }

    private function saveAllSqlite(string $appraisalId, int $owner, string $neighborhoodId, array $sections): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $update = $this->db->prepare('UPDATE appraisal_sector_profile_sections SET neighborhood_id = ?,
            section_title = ?, status = ?, data_json = ?, content_text = ?, version = version + 1,
            updated_at = ? WHERE appraisal_id = ? AND section_code = ? AND owner_id = ?');
        $insert = $this->db->prepare('INSERT INTO appraisal_sector_profile_sections
            (appraisal_id, owner_id, neighborhood_id, section_code, section_title, status, data_json,
             content_text, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        foreach (AppraisalSectorAdvancedCatalog::sections() as $code => [$title]) {
            $code = (string) $code;
            $data = $sections[$code] ?? [];
            $json = self::json($data);
            $summary = self::summary($title, $data);
            $status = self::status($data);
            $update->execute([$neighborhoodId ?: null, $title, $status, $json, $summary, $now,
                $appraisalId, $code, $owner]);
            if ($update->rowCount() === 0) {
                $insert->execute([$appraisalId, $owner, $neighborhoodId ?: null, $code, $title,
                    $status, $json, $summary, $now]);
            }
        }
    }

    public static function summary(string $title, array $data): string
    {
        $parts = [];
        foreach ($data as $value) {
            if (is_array($value)) $value = implode(', ', $value);
            $value = trim((string) $value);
            if ($value !== '') $parts[] = $value;
            if (count($parts) >= 3) break;
        }
        return $parts === [] ? $title . ' pendiente de completar.' : $title . ': ' . implode(' | ', $parts);
    }

    private static function status(array $data): string
    {
        foreach ($data as $value) {
            if (is_array($value) ? $value !== [] : trim((string) $value) !== '') return 'En construcción';
        }
        return 'Pendiente';
    }

    private static function json(array $data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }
}
