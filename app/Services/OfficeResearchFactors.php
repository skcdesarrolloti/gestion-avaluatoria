<?php
declare(strict_types=1);
namespace App\Services;
final class OfficeResearchFactors
{
    public const RETIRED=['stratum','access','air_conditioning','accessible','finishes','elevator','generator','security','landscape_view','panoramic_view'];
    public static function keys(): array
    {
        return ['area','bathrooms','deposit','age','floor','view','finish_quality','corner','parking','covered_parking','independent_parking',
            'ph_elevator','ph_generator','ph_security','destination'];
    }
    public static function preserve(array $catalog,array $saved): array
    {
        foreach (self::RETIRED as $key) if (!isset($saved[$key]) && empty($catalog[$key]['customized'])) unset($catalog[$key]);
        return $catalog;
    }
    public static function group(string $key): string
    {
        if (in_array($key,self::RETIRED,true)) return 'Datos anteriores · revisar alcance';
        if (str_starts_with($key,'ph_')) return 'Copropiedad PH';
        return in_array($key,['parking','covered_parking','independent_parking'],true)?'Celdas de parqueo':'Unidad privada';
    }
}
