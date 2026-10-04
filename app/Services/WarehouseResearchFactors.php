<?php
declare(strict_types=1);
namespace App\Services;
final class WarehouseResearchFactors
{
    public const RETIRED=['bathrooms','access','finishes','generator','security','accessible','restricted_access','covered_parking','independent_parking'];
    public static function keys(): array
    {
        return ['land','built','age','height','frontage','depth','mezzanine','finish_quality','vehicle_access','loading_access','loading_bays','power','floor_load','parking','destination'];
    }
    public static function preserve(array $catalog,array $saved): array
    {
        foreach (self::RETIRED as $key) if (!isset($saved[$key]) && empty($catalog[$key]['customized'])) unset($catalog[$key]);
        return $catalog;
    }
    public static function group(string $key): string
    {
        return in_array($key,self::RETIRED,true)?'Datos anteriores · revisar alcance':'Bodega';
    }
}
