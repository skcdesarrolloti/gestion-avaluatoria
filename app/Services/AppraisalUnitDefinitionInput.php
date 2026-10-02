<?php
declare(strict_types=1);
namespace App\Services;

use App\Support\AppraisalCatalog;
use App\Support\AppraisalConstructionTypeCatalog;
use App\Support\AppraisalUnitValuationTreatmentCatalog;

final class AppraisalUnitDefinitionInput
{
    public static function rows(array $posted): array
    {
        if (!is_array($posted)) return [];
        $rows = [];
        foreach ($posted as $key => $unit) {
            if (!is_string($key) || !is_array($unit)) continue;
            if (!preg_match('/^(property|annex)-([1-9][0-9]*)$/', $key, $match)) continue;
            $propertyType = trim((string) ($unit['property_type'] ?? ''));
            if (!in_array($propertyType, AppraisalCatalog::allowedValues('tipo_inmueble'), true)) $propertyType = '';
            $constructionType = trim((string) ($unit['construction_type'] ?? ''));
            if (!in_array($constructionType, AppraisalConstructionTypeCatalog::allowed(), true)) $constructionType = '';
            $treatment = trim((string) ($unit['valuation_treatment'] ?? ''));
            if (!in_array($treatment, AppraisalUnitValuationTreatmentCatalog::allowed(), true)) {
                $treatment = AppraisalUnitValuationTreatmentCatalog::defaultFor($match[1], $constructionType);
            }
            $igacCategory = AppraisalConstructionTypeCatalog::igacCategoryFor($constructionType)
                ?: mb_substr(trim((string) ($unit['igac_category'] ?? '')), 0, 40);
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
