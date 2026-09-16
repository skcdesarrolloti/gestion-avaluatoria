<?php
declare(strict_types=1);
namespace App\Models;
use App\Support\AppraisalSectorCatalog;
use PDO;

final class AppraisalSectorRepository
{
    public function __construct(private PDO $db) {}

    public function find(string $appraisalId, int $owner): array
    {
        $query = $this->db->prepare('SELECT * FROM appraisal_sector_profiles
            WHERE appraisal_id = ? AND owner_id = ?');
        $query->execute([$appraisalId, $owner]);
        $row = $query->fetch();
        return $row ? array_replace(AppraisalSectorCatalog::defaults(), $row) : AppraisalSectorCatalog::defaults();
    }

    public function save(string $appraisalId, int $owner, array $data): void
    {
        $now = gmdate('Y-m-d H:i:s');
        if ($this->exists($appraisalId, $owner)) {
            $this->update($appraisalId, $owner, $data, $now);
            return;
        }
        $this->insert($appraisalId, $owner, $data, $now);
    }

    public function exists(string $appraisalId, int $owner): bool
    {
        $query = $this->db->prepare('SELECT COUNT(*) FROM appraisal_sector_profiles
            WHERE appraisal_id = ? AND owner_id = ?');
        $query->execute([$appraisalId, $owner]);
        return (int) $query->fetchColumn() === 1;
    }

    private function insert(string $appraisalId, int $owner, array $data, string $now): void
    {
        $columns = AppraisalSectorCatalog::keys();
        $marks = implode(', ', array_fill(0, count($columns) + 3, '?'));
        $sql = 'INSERT INTO appraisal_sector_profiles (appraisal_id, owner_id, '
            . implode(', ', $columns) . ', updated_at) VALUES (' . $marks . ')';
        $this->db->prepare($sql)->execute([$appraisalId, $owner,
            ...array_map(static fn (string $key): string => $data[$key], $columns), $now]);
    }

    private function update(string $appraisalId, int $owner, array $data, string $now): void
    {
        $columns = AppraisalSectorCatalog::keys();
        $set = implode(', ', array_map(static fn (string $key): string => $key . ' = ?', $columns));
        $query = $this->db->prepare('UPDATE appraisal_sector_profiles SET ' . $set . ',
            updated_at = ? WHERE appraisal_id = ? AND owner_id = ?');
        $query->execute([...array_map(static fn (string $key): string => $data[$key], $columns),
            $now, $appraisalId, $owner]);
    }
}
