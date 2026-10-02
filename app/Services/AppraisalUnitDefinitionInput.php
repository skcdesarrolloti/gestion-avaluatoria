<?php
declare(strict_types=1);
namespace App\Services;

use App\Support\AppraisalCatalog;
use App\Support\AppraisalConstructionTypeCatalog;
use App\Support\AppraisalUnitValuationTreatmentCatalog;

final class AppraisalUnitDefinitionInput
{
    public static function rows(array $posted, ?array $composition = null): array
    {
        if (!is_array($posted)) return [];
        $rows = [];
        foreach ($posted as $key => $unit) {
            if (!is_string($key) || !is_array($unit)) continue;
            if (!preg_match('/^(property|annex)-([1-9][0-9]*)$/', $key, $match)) continue;
            if ($composition !== null) {
                $countKey = $match[1] === 'annex' ? 'igac_annex_units_count' : 'igac_property_units_count';
                if ((int) $match[2] > (int) ($composition[$countKey] ?? 0)) continue;
            }
            $propertyType = trim((string) ($unit['property_type'] ?? ''));
            if (!in_array($propertyType, AppraisalCatalog::allowedValues('tipo_inmueble'), true)) $propertyType = '';
            $constructionType = trim((string) ($unit['construction_type'] ?? ''));
            if (!in_array($constructionType, AppraisalConstructionTypeCatalog::allowed(), true)) $constructionType = '';
            $treatment = trim((string) ($unit['valuation_treatment'] ?? ''));
            if (!in_array($treatment, AppraisalUnitValuationTreatmentCatalog::allowed(), true)) {
                $treatment = AppraisalUnitValuationTreatmentCatalog::defaultFor($match[1], $constructionType);
            }
            $igacCategory = AppraisalConstructionTypeCatalog::categoryForUnit([
                'unit_kind' => $match[1], 'construction_type' => $constructionType,
                'igac_category' => $unit['igac_category'] ?? '',
                'igac_typology_hint' => $unit['igac_typology_hint'] ?? '',
            ]);
            $rows[] = [
                'method_structure' => self::structure($unit),
                'unit_kind' => $match[1],
                'unit_index' => (int) $match[2],
                'label' => mb_substr(trim((string) ($unit['label'] ?? '')), 0, 120),
                'property_type' => $propertyType,
                'construction_type' => $constructionType,
                'valuation_treatment' => $treatment,
                'igac_category' => $igacCategory,
                'igac_typology_hint' => mb_substr(trim((string) ($unit['igac_typology_hint'] ?? '')), 0, 190),
                'notes' => mb_substr(trim((string) ($unit['notes'] ?? '')), 0, 2000),
            ];
        }
        return $rows;
    }

    private static function structure(array $unit): ?string
    {
        if (!array_key_exists('method_structure', $unit)) return null;
        $value = $unit['method_structure'];
        if (!is_string($value) || !array_key_exists($value, \App\Support\UnitMethodStructure::options())) {
            throw new \InvalidArgumentException('Revisa la estructura del método de la unidad o anexo.');
        }
        return $value;
    }
}
