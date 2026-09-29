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
            'parqueo' => 'Parqueo',
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
}
