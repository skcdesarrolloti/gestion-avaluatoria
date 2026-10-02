<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\HttpException;
use PDO;

final class MarketSubjectEvidenceRepository
{
    public function __construct(private PDO $db) {}

    public function save(string $appraisalId, int $owner, string $unitId, int $version, array $data): int
    {
        $repo = new AppraisalRepository($this->db);
        $repo->find($appraisalId, $owner);
        if (!in_array($unitId, array_column($repo->units($appraisalId, $owner), 'id'), true)) {
            throw new HttpException(404, 'No se encontró una unidad activa del expediente.');
        }
        if ($version < 0) throw new HttpException(422, 'Falta la versión del soporte de Mercado.');
        $query = $this->db->prepare('UPDATE appraisal_units SET market_evidence_json = ?,
            market_evidence_version = market_evidence_version + 1, updated_at = ?
            WHERE id = ? AND appraisal_id = ? AND owner_id = ? AND market_evidence_version = ?');
        $query->execute([json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            gmdate('Y-m-d H:i:s'), $unitId, $appraisalId, $owner, $version]);
        if ($query->rowCount() !== 1) {
            $exists = $this->db->prepare('SELECT id FROM appraisal_units WHERE id = ? AND appraisal_id = ? AND owner_id = ?');
            $exists->execute([$unitId, $appraisalId, $owner]);
            if (!$exists->fetchColumn()) throw new HttpException(404, 'No se encontró esta unidad del expediente.');
            throw new HttpException(409, 'El soporte de Mercado cambió en otra pestaña. Revisa antes de guardar.');
        }
        return $version + 1;
    }
}
