<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\HttpException;
use PDO;

final class JudicialExpertRepository
{
    public function __construct(private PDO $db) {}
    public function profile(string $id, int $owner): array { return $this->read('judicial_expert_profiles', 'appraiser_id', $id, $owner); }
    public function dossier(string $id, int $owner): array { return $this->read('appraisal_judicial_records', 'appraisal_id', $id, $owner); }
    private function read(string $table, string $key, string $id, int $owner): array
    {
        $q = $this->db->prepare("SELECT * FROM $table WHERE $key = ? AND owner_id = ?");
        $q->execute([$id, $owner]); $row = $q->fetch();
        if (!$row) return ['data' => [], 'version' => 0, 'presented_on' => null];
        $row['data'] = json_decode($row['payload'], true, 32, JSON_THROW_ON_ERROR);
        return $row;
    }
    public function saveProfile(string $id, int $owner, array $data, int $version): int
    { return $this->save('judicial_expert_profiles', 'appraiser_id', $id, $owner, $data, $version); }
    public function saveDossier(string $id, int $owner, string $expert, array $data, int $version): int
    { return $this->save('appraisal_judicial_records', 'appraisal_id', $id, $owner, $data, $version, $expert); }
    private function save(string $table, string $key, string $id, int $owner, array $data, int $version, ?string $expert = null): int
    {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR); $now = gmdate('Y-m-d H:i:s');
        try {
            if ($version === 0) {
                $extra = $expert === null ? '' : ', appraiser_id'; $marks = $expert === null ? '' : ', ?';
                $values = [$id, $owner, $json, $now]; if ($expert !== null) $values[] = $expert;
                $q = $this->db->prepare("INSERT INTO $table ($key, owner_id, payload, updated_at $extra) VALUES (?, ?, ?, ? $marks)");
                $q->execute($values);
            } else {
                $guard = $expert === null ? '' : ' AND appraiser_id = ? AND presented_on IS NULL';
                $values = [$json, $now, $id, $owner, $version]; if ($expert !== null) $values[] = $expert;
                $q = $this->db->prepare("UPDATE $table SET payload = ?, updated_at = ?, version = version + 1
                    WHERE $key = ? AND owner_id = ? AND version = ? $guard");
                $q->execute($values);
                if ($q->rowCount() !== 1) throw new HttpException(409, 'Cambió la versión, el perito o ya se registró la presentación. Conserva tus cambios y recarga.');
            }
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000') throw new HttpException(409, 'El registro cambió en otra pestaña. Recarga antes de continuar.');
            throw $e;
        }
        return $version + 1;
    }
    public function history(string $expert, int $owner, string $exclude = ''): array
    {
        $q = $this->db->prepare('SELECT appraisal_id, presented_on, snapshot FROM appraisal_judicial_records
            WHERE appraiser_id = ? AND owner_id = ? AND presented_on IS NOT NULL AND appraisal_id <> ? ORDER BY presented_on DESC');
        $q->execute([$expert, $owner, $exclude]);
        return array_map(static function ($r) { $s = json_decode($r['snapshot'], true, 32, JSON_THROW_ON_ERROR);
            return $s['case'] + ['date' => $r['presented_on'], 'appraisal_id' => $r['appraisal_id']]; }, $q->fetchAll());
    }
    public function present(string $id, int $owner, string $expert, int $version, string $date, array $snapshot): void
    {
        $q = $this->db->prepare('UPDATE appraisal_judicial_records SET presented_on = ?, snapshot = ?, version = version + 1,
            updated_at = ? WHERE appraisal_id = ? AND owner_id = ? AND appraiser_id = ? AND version = ? AND presented_on IS NULL');
        $q->execute([$date, json_encode($snapshot, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR), gmdate('Y-m-d H:i:s'), $id, $owner, $expert, $version]);
        if ($q->rowCount() !== 1) throw new HttpException(409, 'La presentación ya fue registrada o cambió el borrador. No se duplicó el historial.');
    }
}
