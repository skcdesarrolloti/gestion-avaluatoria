<?php
declare(strict_types=1);
namespace App\Services;

final class ComparablePhCapture
{
    public static function fields(): array
    {
        return [
            'ph_parking_presence' => ['Garaje / parqueadero / celda de parqueo', 'choice', 'ph'],
            'ph_parking_in_price' => ['Parqueadero incluido en el precio', 'choice', 'ph'],
            'ph_parking_nature' => ['Naturaleza jurídica del parqueadero', 'choice', 'ph'],
            'ph_parking_area_m2' => ['Área total de parqueaderos (m²)', 'number', 'ph'],
            'ph_deposit_presence' => ['Depósito / cuarto útil', 'choice', 'ph'],
            'ph_deposit_count' => ['Cantidad de depósitos', 'integer', 'ph'],
            'ph_deposit_in_price' => ['Depósito incluido en el precio', 'choice', 'ph'],
            'ph_deposit_nature' => ['Naturaleza jurídica del depósito', 'choice', 'ph'],
            'ph_deposit_area_m2' => ['Área total de depósitos (m²)', 'number', 'ph'],
            'ph_other_components' => ['Áreas libres y otros componentes incluidos: descripción y áreas', 'text', 'ph'],
            'ph_components_source' => ['Soporte de componentes: aviso, contacto, fecha y documento', 'text', 'ph'],
        ];
    }

    public static function options(string $field): array
    {
        if (str_ends_with($field, '_nature')) return ['' => 'Por verificar',
            'privado_independiente' => 'Privado con matrícula independiente',
            'privado_integrado' => 'Privado en la misma matrícula',
            'comun_exclusivo' => 'Común de uso exclusivo', 'comun' => 'Común sin uso exclusivo'];
        return ['' => 'No publicado / por verificar', 'si' => 'Sí, informado', 'no' => 'No, confirmado'];
    }
}
