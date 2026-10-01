<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\HttpException;
use PDO;

final class AppraisalUnitReclassification
{
    public static function toAnnex(PDO $db, string $id, int $owner, string $unitId, int $version): void
    {
        $db->beginTransaction();
        try {
            $q = $db->prepare('SELECT version, igac_property_units_count, igac_annex_units_count FROM appraisals WHERE id = ? AND owner_id = ? FOR UPDATE');
            $q->execute([$id, $owner]); $record = $q->fetch();
            if (!$record) throw new HttpException(404, 'No se encontró el avalúo.');
            if ((int) $record['version'] !== $version) throw new HttpException(409, 'El expediente cambió. Recarga antes de reclasificar.');
            $q = $db->prepare('SELECT unit_index, construction_type, valuation_treatment FROM appraisal_units WHERE id = ? AND appraisal_id = ? AND owner_id = ? AND unit_kind = "property"');
            $q->execute([$unitId, $id, $owner]); $unit = $q->fetch(); $index = $unit['unit_index'] ?? false;
            if ($index === false || (int) $index > (int) $record['igac_property_units_count']) throw new HttpException(422, 'Selecciona una unidad principal activa.');
            $next = (int) $record['igac_annex_units_count'] + 1;
            if ($next > 50) throw new HttpException(422, 'Ya existen 50 anexos activos.');
            // Conserva también anexos inactivos; abre espacio sin reutilizar ni borrar sus IDs.
            $db->prepare('UPDATE appraisal_units SET unit_index = unit_index + 1 WHERE appraisal_id = ? AND owner_id = ? AND unit_kind = "annex" AND unit_index >= ? ORDER BY unit_index DESC')->execute([$id, $owner, $next]);
            $treatment = ($unit['valuation_treatment'] ?? '') === 'principal' || empty($unit['valuation_treatment'])
                ? \App\Support\AppraisalUnitValuationTreatmentCatalog::defaultFor('annex', (string) $unit['construction_type']) : $unit['valuation_treatment'];
            $db->prepare('UPDATE appraisal_units SET unit_kind = "annex", unit_index = ?, property_type = "", valuation_treatment = ?, updated_at = UTC_TIMESTAMP() WHERE id = ? AND appraisal_id = ? AND owner_id = ?')->execute([$next, $treatment, $unitId, $id, $owner]);
            $db->prepare('UPDATE appraisal_units SET unit_index = unit_index - 1 WHERE appraisal_id = ? AND owner_id = ? AND unit_kind = "property" AND unit_index > ? ORDER BY unit_index ASC')->execute([$id, $owner, $index]);
            $db->prepare('UPDATE appraisals SET igac_property_units_count = igac_property_units_count - 1, igac_annex_units_count = ?, version = version + 1, updated_at = UTC_TIMESTAMP() WHERE id = ? AND owner_id = ?')->execute([$next, $id, $owner]);
            $db->commit();
        } catch (\Throwable $error) { $db->rollBack(); throw $error; }
    }
}
