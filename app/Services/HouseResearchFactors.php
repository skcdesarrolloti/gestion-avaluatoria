<?php
declare(strict_types=1);
namespace App\Services;

/** Approved house observations, with private and common amenities kept distinct. */
final class HouseResearchFactors
{
    public const RETIRED=['finishes','service','pool','gym','generator','security','air_conditioning','accessible','vehicle_access','access','landscape_view','panoramic_view'];
    public static function all(): array
    {
        $out=[];
        foreach ([
            'house_pool'=>['Piscina propia','Piscina perteneciente a la casa; una piscina común del conjunto se registra en Copropiedad PH.'],
            'house_jacuzzi'=>['Jacuzzi propio','Jacuzzi perteneciente a la casa, con existencia y funcionamiento documentados; no inferirlo por una tina.'],
            'house_gym'=>['Gimnasio propio','Gimnasio perteneciente a la casa; no incluye gimnasio común ni instalaciones cercanas.'],
            'house_security'=>['Vigilancia exclusiva de la casa','Vigilancia exclusiva de esta casa; registrar horario y soporte. La vigilancia del conjunto se registra aparte.'],
        ] as $key=>[$label,$why]) $out[$key]=[
            'label'=>$label,'why'=>$why,'kind'=>'binary','unit'=>'sí/no','categories'=>"No\nSí",
            'subject'=>'research_'.$key,'sample'=>'research_'.$key,'section'=>'attributes','group'=>'Unidad privada',
        ];
        $out['house_generator']=[
            'label'=>'Planta eléctrica propia','why'=>'Respaldo propio de la casa: 0 = No, 1 = Parcial, 2 = Total. Un Sí sin cobertura no acredita Total; el respaldo común se registra aparte.',
            'kind'=>'ordinal','unit'=>'alcance','categories'=>"No\nParcial\nTotal",'subject'=>'research_house_generator','sample'=>'research_house_generator','section'=>'attributes','group'=>'Unidad privada',
        ];
        $out['house_access']=[
            'label'=>'Tipo de acceso a la casa','why'=>'Modalidad de acceso: peatonal, vehicular o mixto. No tiene jerarquía; las restricciones se registran por separado.',
            'kind'=>'categorical','unit'=>'clase','categories'=>"Peatonal\nVehicular\nMixto",'subject'=>'research_house_access','sample'=>'research_house_access','section'=>'attributes','group'=>'Unidad privada',
        ];
        return $out;
    }
    public static function keys(): array
    {
        return ['land','built','bedrooms','bathrooms','age','levels','service_room','deposit','stratum','view','finish_quality','balcony','terrace',
            'house_pool','house_jacuzzi','house_gym','corner','house_generator','house_security','house_access','restricted_access',
            'parking','covered_parking','independent_parking','ph_elevator','ph_pool','ph_gym','ph_security','ph_generator','destination'];
    }
    public static function preserve(array $catalog,array $saved): array
    {
        foreach (array_merge(self::RETIRED,['access']) as $key) if (!isset($saved[$key])) unset($catalog[$key]);
        return $catalog;
    }
    public static function group(string $key): string
    {
        if (str_starts_with($key,'ph_')) return 'Copropiedad PH';
        if (in_array($key,['parking','covered_parking','independent_parking'],true)) return 'Celdas de parqueo';
        return in_array($key,array_merge(self::RETIRED,['access']),true)?'Datos anteriores · revisar alcance':'Unidad privada';
    }
}
