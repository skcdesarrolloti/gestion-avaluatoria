<?php
declare(strict_types=1);
namespace App\Services;
final class BuildingResearchFactors
{
    public const RETIRED=['bathrooms','bedrooms','elevator','access','accessible','air_conditioning','loading_bays','vehicle_access','covered_parking','view','panoramic_view'];
    public static function all(): array
    {
        return ['elevator_count'=>[
            'label'=>'Ascensores operativos','kind'=>'numeric','unit'=>'cantidad',
            'subject'=>'research_elevator_count','sample'=>'research_elevator_count',
            'why'=>'Contar ascensores comprobados en operación en el edificio completo. La presencia de ascensor no acredita su cantidad ni operación; desconocido queda pendiente.',
            'section'=>'attributes','categories'=>'',
        ]];
    }
    public static function keys(): array
    {
        return ['land','built','age','levels','units_count','finish_quality','elevator_count','parking','generator','security','access_ramp','destination'];
    }
    public static function preserve(array $catalog,array $saved): array
    {
        foreach (self::RETIRED as $key) if (!isset($saved[$key]) && empty($catalog[$key]['customized'])) unset($catalog[$key]);
        return $catalog;
    }
    public static function group(string $key): string
    {
        return in_array($key,self::RETIRED,true)?'Datos anteriores · revisar alcance':'Edificio completo';
    }
}
