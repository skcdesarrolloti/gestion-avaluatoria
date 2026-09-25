<?php
declare(strict_types=1);
namespace App\Support;

final class UrbanNormativeScenarioCatalog
{
    public static function routes(): array
    {
        return [
            'residencial' => ['label' => 'Residencial', 'hint' => 'Vivienda y sus modalidades cuando el POT las permita.'],
            'comercial' => ['label' => 'Comercial', 'hint' => 'Comercio, servicios al público o actividad económica.'],
            'institucional' => ['label' => 'Institucional', 'hint' => 'Equipamientos, servicios públicos, educación, salud o gobierno.'],
            'industrial' => ['label' => 'Industrial / logística', 'hint' => 'Industria, bodegaje, operación logística o portuaria.'],
            'dotacional' => ['label' => 'Dotacional / equipamientos', 'hint' => 'Dotaciones urbanas, equipamientos colectivos o usos especiales.'],
            'mixto' => ['label' => 'Mixto', 'hint' => 'Combinación de usos admitida por área de actividad o cuadro.'],
            'otro' => ['label' => 'Otro / instrumento especial', 'hint' => 'Plan parcial, resolución, licencia, concepto o instrumento específico.'],
        ];
    }

    public static function results(): array
    {
        return [
            'pendiente' => 'Pendiente de cruce',
            'principal' => 'Principal',
            'compatible' => 'Compatible',
            'complementario' => 'Complementario',
            'restringido' => 'Restringido',
            'prohibido' => 'Prohibido',
            'no_aplica' => 'No aplica',
        ];
    }

    public static function blankScenarios(): array
    {
        $blank = [];
        foreach (array_keys(self::routes()) as $key) {
            $blank[$key] = ['enabled' => false, 'document_slug' => '', 'table_slug' => '',
                'category_slug' => '', 'activity' => '', 'result' => 'pendiente',
                'parameters_summary' => '', 'observations' => ''];
        }
        return $blank;
    }
}
