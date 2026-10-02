<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalConstructionTypeCatalog
{
    public static function types(): array
    {
        return [
            '' => 'Selecciona tipo',
            'galpon' => 'Galpón / nave industrial',
            'bodega' => 'Bodega',
            'casa' => 'Casa',
            'apartamento' => 'Apartamento',
            'local' => 'Local comercial',
            'oficina' => 'Oficina',
            'deposito' => 'Depósito / cuarto útil',
            'kiosco' => 'Kiosco',
            'ramada' => 'Ramada',
            'mezanine' => 'Mezanine',
            'cubierta' => 'Cubierta / techo',
            'cerramiento' => 'Cerramiento',
            'muro' => 'Muro perimetral',
            'porton' => 'Portón / acceso vehicular',
            'placa' => 'Placa de concreto',
            'parqueo' => 'Garaje / parqueadero / celda de parqueo',
            'piscina' => 'Piscina',
            'otro' => 'Otro',
        ];
    }

    public static function allowed(): array
    {
        return array_keys(self::types());
    }

    public static function igacCategories(): array
    {
        return [
            'galpon' => 'INDUSTRIALES',
            'bodega' => 'INDUSTRIALES',
            'casa' => 'RESIDENCIALES',
            'apartamento' => 'RESIDENCIALES',
            'local' => 'COMERCIALES',
            'oficina' => 'COMERCIALES',
            'deposito' => 'ANEXOS',
            'kiosco' => 'ANEXOS',
            'ramada' => 'ANEXOS',
            'mezanine' => 'ANEXOS',
            'cubierta' => 'ANEXOS',
            'cerramiento' => 'ANEXOS',
            'muro' => 'ANEXOS',
            'porton' => 'ANEXOS',
            'placa' => 'ANEXOS',
            'parqueo' => 'ANEXOS',
            'piscina' => 'ANEXOS',
        ];
    }

    public static function igacCategoryFor(string $type): string
    {
        return self::igacCategories()[$type] ?? '';
    }

    public static function categoryForUnit(array $unit): string
    {
        $selected = trim((string) ($unit['igac_category'] ?? ''));
        if (($unit['construction_type'] ?? '') === 'oficina') {
            foreach ((new \App\Models\IgacTypologyRepository())->optionsByCategory() as $category => $options) {
                foreach ($options as $option) {
                    if ($option['value'] === ($unit['igac_typology_hint'] ?? '')) return $category;
                }
            }
            if (in_array($selected, ['COMERCIALES', 'EDIFICIOS'], true)) return $selected;
        }
        $suggested = self::igacCategoryFor((string) ($unit['construction_type'] ?? ''));
        if (($unit['unit_kind'] ?? '') === 'annex') return $suggested ?: 'ANEXOS';
        return in_array($selected, ['RESIDENCIALES', 'COMERCIALES', 'INDUSTRIALES', 'INSTITUCIONALES', 'EDIFICIOS', 'ANEXOS'], true)
            ? $selected : $suggested;
    }

    public static function optionsForUnit(array $unit, array $catalog): array
    {
        $category = self::categoryForUnit($unit);
        if (($unit['construction_type'] ?? '') !== 'oficina') {
            return IgacTypologyFilter::options($catalog[$category] ?? [], (string) ($unit['construction_type'] ?? ''),
                (string) ($unit['igac_typology_hint'] ?? ''));
        }
        $categories = array_unique(['COMERCIALES', 'EDIFICIOS', $category]);
        $options = [];
        foreach ($categories as $code) {
            foreach ($catalog[$code] ?? [] as $option) {
                $options[] = array_replace($option, ['label' => $option['label'] . ' · ' . $code]);
            }
        }
        return $options;
    }
}
