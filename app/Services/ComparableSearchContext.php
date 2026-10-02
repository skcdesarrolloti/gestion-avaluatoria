<?php
declare(strict_types=1);
namespace App\Services;

use App\Support\AppraisalCatalog;

final class ComparableSearchContext
{
    public static function record(array $record, array $units, string $componentKey): array
    {
        if ($componentKey === '') return $record;
        $components = MethodologyWorkflow::components($record, $units);
        MethodologyWorkflow::validateKey($componentKey, $components);
        $unit = $components[$componentKey]['unit'];
        $type = self::type((string) ($unit['property_type'] ?? ''));
        $principals = array_filter($units, static fn (array $item): bool => ($item['unit_kind'] ?? '') === 'property');
        // The sole principal may inherit the chapter 1 type; never apply it to an annex or ambiguous unit.
        if ($type === '' && ($unit['unit_kind'] ?? '') === 'property' && count($principals) === 1) {
            $type = self::type((string) ($record['tipo_inmueble'] ?? ''));
        }
        $record['tipo_inmueble'] = $type;
        return $record;
    }

    public static function type(string $value): string
    {
        $value = mb_strtolower(trim($value));
        foreach (AppraisalCatalog::selectFields()['tipo_inmueble'][4] as $key => $label) {
            if ($value === $key || $value === mb_strtolower($label)) return $key;
        }
        return '';
    }
}
