<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;

/** Valuation parts belong to a registered unit; they never create legal units. */
final class MethodologyValuationPlan
{
    public static function expand(array $record, array $components): array
    {
        $saved = MethodologyWorkflow::saved($record);
        $result = [];
        foreach ($components as $key => $component) {
            $result[$key] = $component;
            if (($saved[$key]['plan_parts'] ?? '') !== 'land_building') continue;
            $result[$key]['container'] = true;
            if (($record['regimen_ph'] ?? '')!=='no' || ($component['unit']['unit_kind'] ?? '')!=='property') {
                $result[$key]['plan_blocked'] = true;
                continue;
            }
            foreach (['terreno'=>'Terreno', 'construccion'=>'Construcción'] as $part=>$label) {
                $unit = $component['unit'];
                $unit['method_structure'] = $part === 'terreno' ? 'solo_terreno' : 'solo_construccion';
                if ($part === 'terreno') $unit['property_type'] = 'lote';
                $result[$key.':'.$part] = ['label'=>$component['label'].' · '.$label, 'unit'=>$unit,
                    'parent_key'=>$key, 'part'=>$part, 'parent_label'=>$component['label']];
            }
        }
        return $result;
    }

    public static function validateParts(array $record, array $component, string $parts): void
    {
        if (isset($component['parent_key'])) throw new HttpException(422, 'Organiza las partes desde la unidad original.');
        if ($parts === 'land_building' && (($record['regimen_ph'] ?? '') !== 'no'
            || ($component['unit']['unit_kind'] ?? '') !== 'property')) {
            throw new HttpException(422, 'Terreno y construcción requiere una unidad principal con régimen NPH confirmado. Para PH o anexos conserva su alcance propio.');
        }
    }

    public static function working(array $components): array
    {
        return array_filter($components, static fn($component)=>empty($component['container']));
    }

    public static function validateWorkKey(string $key, array $components): void
    {
        MethodologyWorkflow::validateKey($key, $components);
        if (!empty($components[$key]['container'])) throw new HttpException(422, 'Trabaja el terreno o la construcción; la unidad original agrupa sus resultados.');
    }
}
