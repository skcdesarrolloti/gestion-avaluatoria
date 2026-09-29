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
}
