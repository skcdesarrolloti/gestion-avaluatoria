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


    public static function feasibilities(): array
    {
        return [
            'pendiente' => 'Pendiente de evaluar',
            'viable' => 'Viable con soporte básico',
            'condicionado' => 'Viable condicionado',
            'limitado' => 'Limitado por norma o condición física',
            'no_viable' => 'No viable',
            'requiere_especialista' => 'Requiere arquitecto o concepto oficial',
        ];
    }

    public static function blankScenarios(): array
    {
        $blank = [];
        foreach (array_keys(self::routes()) as $key) {
            $blank[$key] = ['enabled' => false, 'document_slug' => '', 'table_slug' => '',
                'category_slug' => '', 'activity' => '', 'result' => 'pendiente',
                'land_area_m2' => '', 'net_land_area_m2' => '', 'occupancy_index' => '', 'max_floors' => '',
                'construction_index' => '', 'max_built_area_m2' => '', 'existing_built_area_m2' => '',
                'potential_area_m2' => '', 'sellable_factor' => '', 'sellable_area_m2' => '',
                'feasibility' => 'pendiente', 'parameters_summary' => '', 'observations' => ''];
        }
        return $blank;
    }
}
