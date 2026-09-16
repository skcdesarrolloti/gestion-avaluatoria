<?php
declare(strict_types=1);
namespace App\Models;
use App\Support\AppraisalSectorCatalog;
use PDO;

final class NeighborhoodSectorRepository
{
    public function __construct(private PDO $db) {}

    public function find(string $neighborhoodId): ?array
    {
        if ($neighborhoodId === '') return null;
        $query = $this->db->prepare('SELECT * FROM master_sector_profiles WHERE neighborhood_id = ?');
        $query->execute([$neighborhoodId]);
        $row = $query->fetch();
        return $row ? array_replace(AppraisalSectorCatalog::defaults(), $row) : null;
    }

    public function save(string $neighborhoodId, int $owner, string $appraisalId, array $data): void
    {
        if ($neighborhoodId === '') return;
        $now = gmdate('Y-m-d H:i:s');
        $this->find($neighborhoodId)
            ? $this->update($neighborhoodId, $owner, $appraisalId, $data, $now)
            : $this->insert($neighborhoodId, $owner, $appraisalId, $data, $now);
    }

    private function insert(string $neighborhoodId, int $owner, string $appraisalId, array $data, string $now): void
    {
        $columns = AppraisalSectorCatalog::keys();
        $marks = implode(', ', array_fill(0, count($columns) + 4, '?'));
        $sql = 'INSERT INTO master_sector_profiles (neighborhood_id, source_appraisal_id, updated_by_owner_id, '
            . implode(', ', $columns) . ', updated_at) VALUES (' . $marks . ')';
        $this->db->prepare($sql)->execute([$neighborhoodId, $appraisalId, $owner,
            ...array_map(static fn (string $key): string => $data[$key] ?? '', $columns), $now]);
    }

    private function update(string $neighborhoodId, int $owner, string $appraisalId, array $data, string $now): void
    {
        $columns = AppraisalSectorCatalog::keys();
        $set = implode(', ', array_map(static fn (string $key): string => $key . ' = ?', $columns));
        $query = $this->db->prepare('UPDATE master_sector_profiles SET source_appraisal_id = ?,
            updated_by_owner_id = ?, version = version + 1, ' . $set . ',
            updated_at = ? WHERE neighborhood_id = ?');
        $query->execute([$appraisalId, $owner,
            ...array_map(static fn (string $key): string => $data[$key] ?? '', $columns),
            $now, $neighborhoodId]);
    }
}
