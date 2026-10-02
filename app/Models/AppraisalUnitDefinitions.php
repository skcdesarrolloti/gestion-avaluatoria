<?php
declare(strict_types=1);
namespace App\Models;
use PDO;

final class AppraisalUnitDefinitions
{
    public static function save(PDO $db, string $id, int $owner, array $units): void
    {
        if ($units === []) return;
        $now = gmdate('Y-m-d H:i:s');
        $query = $db->prepare('UPDATE appraisal_units SET label = ?, property_type = ?, construction_type = ?, valuation_treatment = ?, method_structure = COALESCE(?, method_structure), igac_category = ?, igac_typology_hint = ?, notes = ?, updated_at = ? WHERE appraisal_id = ? AND owner_id = ? AND unit_kind = ? AND unit_index = ?');
        foreach ($units as $unit) {
            $label = (string) ($unit['label'] ?: (($unit['unit_kind'] === 'annex' ? 'Anexo ' : 'Unidad ') . (int) $unit['unit_index']));
            $query->execute([$label, $unit['property_type'], $unit['construction_type'], $unit['valuation_treatment'],
                $unit['method_structure'] ?? null, $unit['igac_category'], $unit['igac_typology_hint'], $unit['notes'],
                $now, $id, $owner, $unit['unit_kind'], $unit['unit_index']]);
        }
    }
}
