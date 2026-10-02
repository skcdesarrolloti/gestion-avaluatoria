<?php
declare(strict_types=1);
namespace App\Support;

final class UnitMethodStructure
{
    public static function options(): array
    {
        return ['' => 'Selecciona estructura de esta unidad', 'area_privada' => 'Solo área privada',
            'lote_construccion' => 'Lote + construcción', 'solo_terreno' => 'Solo terreno',
            'solo_construccion' => 'Solo construcción o mejora', 'por_definir' => 'Por definir'];
    }
}
