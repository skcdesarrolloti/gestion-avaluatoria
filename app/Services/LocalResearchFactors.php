<?php
declare(strict_types=1);
namespace App\Services;
final class LocalResearchFactors
{
    public const RETIRED=['bathrooms','deposit','floor','elevator','view','finishes','access','stratum','generator','security','accessible','air_conditioning','corner','covered_parking','independent_parking','ph_elevator','ph_generator','ph_security'];
    public static function all(): array
    {
        return ['commercial_strength'=>[
            'label'=>'Fuerza comercial','kind'=>'ordinal','unit'=>'nivel',
            'subject'=>'research_commercial_strength','sample'=>'research_commercial_strength',
            'why'=>'Clasificar con evidencia de flujo de clientes potenciales, visibilidad y actividad comercial del entorno. Baja: condiciones limitadas; Media: condiciones intermedias; Alta: condiciones favorables. Documentar la misma pauta y soporte para sujeto y comparables; si no se verifica, queda pendiente.',
            'section'=>'attributes','categories'=>"Baja\nMedia\nAlta",
        ]];
    }
    public static function keys(): array
    {
        return ['area','age','height','frontage','shopfront','mezzanine','finish_quality','commercial_strength','loading_access','parking','destination'];
    }
    public static function preserve(array $catalog,array $saved): array
    {
        foreach (self::RETIRED as $key) if (!isset($saved[$key]) && empty($catalog[$key]['customized'])) unset($catalog[$key]);
        return $catalog;
    }
    public static function group(string $key): string
    {
        return in_array($key,self::RETIRED,true)?'Datos anteriores · revisar alcance':'Local comercial';
    }
}
