<?php
declare(strict_types=1);
namespace App\Services;
final class LandResearchFactors
{
    public const RETIRED=['access','topography','corner','restricted_access','water','electricity','sewer'];
    public static function all(): array
    {
        return [
            'front_exposure'=>self::make('front_exposure','Exposición por frentes',"Medianero\nEsquinero\nTres frentes",'Verificar los frentes del lote: 0 medianero, 1 esquinero, 2 tres frentes. Es una clasificación de exposición, no un coeficiente de precio. No deducir Tres frentes únicamente de una descripción esquinera.'),
            'public_services'=>self::make('public_services','Servicios públicos',"Sin servicios\nParciales\nCompletos",'Verificar agua, energía y alcantarillado. 0 ninguno operativo; 1 alguno, pero no todos; 2 los tres operativos. Registrar en el soporte cuáles están verificados. Red cercana no acredita conexión operativa. Si falta verificación, queda pendiente; no deducir una clasificación nueva de los campos históricos.'),
        ];
    }
    private static function make(string $key,string $label,string $categories,string $why): array
    {
        return ['label'=>$label,'kind'=>'ordinal','unit'=>'nivel','subject'=>'research_'.$key,
            'sample'=>'research_'.$key,'why'=>$why,'section'=>'attributes','categories'=>$categories,'group'=>'Terreno'];
    }
    public static function keys(): array
    {
        return ['land','frontage','depth','slope','front_exposure','vehicle_access','public_services','destination'];
    }
    public static function preserve(array $catalog,array $saved): array
    {
        foreach (self::RETIRED as $key) if (!isset($saved[$key]) && empty($catalog[$key]['customized'])) unset($catalog[$key]);
        return $catalog;
    }
    public static function group(string $key): string
    {
        return in_array($key,self::RETIRED,true)?'Datos anteriores · revisar alcance':'Terreno';
    }
}
