<?php
declare(strict_types=1);
namespace App\Services;

/** Apartment and PH observations stay separate; no statistical integration here. */
final class ApartmentResearchFactors
{
    public const RETIRED=['elevator','generator','service','finishes','pool','gym','security','air_conditioning','accessible','corner','landscape_view','panoramic_view'];
    public static function all(): array
    {
        $out=[];
        $definitions=[
            'service_room'=>['Alcoba / cuarto de servicio','Presencia de alcoba o cuarto de servicio dentro de la unidad privada. Un baño de servicio por sí solo no acredita alcoba.','binary',"No\nSí",'Unidad privada'],
            'ph_elevator'=>['Ascensor que sirve a la unidad · PH','Verificar que el ascensor del edificio sirve al piso de la unidad. Se registra separado del piso; su relación se estudiará en Análisis.','binary',"No\nSí",'Copropiedad PH'],
            'ph_pool'=>['Piscina común · PH','Disponibilidad de piscina de la copropiedad para esta unidad. No es piscina privada del apartamento.','binary',"No\nSí",'Copropiedad PH'],
            'ph_gym'=>['Gimnasio común · PH','Disponibilidad de gimnasio de la copropiedad para esta unidad. No es gimnasio privado del apartamento.','binary',"No\nSí",'Copropiedad PH'],
            'ph_security'=>['Vigilancia presencial común · PH','Vigilancia de la copropiedad; registrar horario y evidencia. Portería sola no acredita vigilancia 24 horas.','binary',"No\nSí",'Copropiedad PH'],
            'ph_generator'=>['Planta eléctrica común · PH','Registrar cobertura efectiva del respaldo común para esta unidad: No, Parcial o Total. Un Sí sin alcance queda por verificar.','ordinal',"No\nParcial\nTotal",'Copropiedad PH'],
        ];
        foreach ($definitions as $key=>[$label,$why,$kind,$categories,$group]) $out[$key]=[
            'label'=>$label,'why'=>$why,'kind'=>$kind,'categories'=>$categories,'group'=>$group,
            'unit'=>$kind==='ordinal'?'alcance':'sí/no','subject'=>'research_'.$key,'sample'=>'research_'.$key,'section'=>'attributes',
        ];
        return $out;
    }
    public static function keys(): array
    {
        return ['area','bathrooms','bedrooms','deposit','age','levels','floor','view','stratum','service_room','balcony','terrace','finish_quality',
            'parking','covered_parking','independent_parking','ph_elevator','ph_pool','ph_gym','ph_security','ph_generator','destination'];
    }
    public static function preserve(array $catalog,array $saved): array
    {
        foreach (self::RETIRED as $key) if (!isset($saved[$key]) && empty($catalog[$key]['customized'])) unset($catalog[$key]);
        return $catalog;
    }
    public static function group(string $key): string
    {
        if (str_starts_with($key,'ph_')) return 'Copropiedad PH';
        if (in_array($key,['parking','covered_parking','independent_parking'],true)) return 'Celdas de parqueo';
        return in_array($key,self::RETIRED,true)?'Datos anteriores · revisar alcance':'Unidad privada';
    }
}
