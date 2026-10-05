<?php
declare(strict_types=1);
namespace App\Services;

final class ResearchFactorCatalog
{
    public static function all(): array
    {
        $definitions=\App\Support\AppraisalFunctionalVariableCatalog::definitions();
        $make=static fn($label,$kind,$unit,$subject,$sample,$why,$section='construction',$categories='')=>
            compact('label','kind','unit','subject','sample','why','section','categories');
        $options=static fn($key)=>implode("\n",array_values(array_filter($definitions[$key]['options'] ?? [],static fn($v,$k)=>!in_array($k,['','no_verificado'],true),ARRAY_FILTER_USE_BOTH)));
        return UserResearchFactors::merge([
            'area'=>$make('Área de comparación','numeric','m²','area_private_m2','private_built_m2','Comparar tamaño conservando la base del área.','surface'),
            'land'=>$make('Área de terreno','numeric','m²','area_adopted_m2','land_m2','Distinguir extensión de terreno y construcción.','surface'),
            'built'=>$make('Área construida','numeric','m²','built_area_adopted_m2','built_m2','Distinguir superficies construidas del terreno.','surface'),
            'bathrooms'=>$make('Baños','numeric','cantidad','functional_bathrooms_count','bathrooms','Examinar diferencias de dotación y distribución.'),
            'bedrooms'=>$make('Habitaciones','numeric','cantidad','functional_bedrooms_count','bedrooms','Examinar distribución; distinguir alcobas de ambientes.'),
            'parking'=>$make('Celdas de parqueo','numeric','cantidad','functional_parking_spaces_count','parking_spaces','Investigar composición, inclusión y derechos; no asigna valor al anexo.'),
            'age'=>$make('Edad','numeric','años','construction_age_years','age_years','Examinar antigüedad; intervalos no equivalen a edad exacta.'),
            'levels'=>$make('Niveles del inmueble','numeric','cantidad','construction_floors','research_levels','Distinguir niveles de la unidad y piso de ubicación.'),
            'floor'=>$make('Piso de ubicación','numeric','número','research_floor','floor_level','Examinar ubicación vertical, distinta de número de niveles.','attributes'),
            'elevator'=>$make('Ascensor','binary','sí/no','research_elevator','elevator','Comparar disponibilidad comprobada; desconocido no es ausencia.','attributes',"No\nSí"),
            'view'=>$make('Vista','ordinal','nivel','functional_view','view_quality','Registrar la vista predominante desde el espacio principal, con soporte. Misma jerarquía para sujeto y comparables.','construction',"Sin vista\nInterior\nExterior: calles y avenidas\nExterior: paisajística"),
            'finishes'=>$make('Acabados','ordinal','nivel','functional_finish_quality','finish_quality','Calificar calidad de acabados terminados con soporte. Obra gris es estado de ejecución y se registra por separado.','construction',"Básico / económico\nMedio\nBueno\nAlto\nSuperior / lujo"),
            'service'=>$make('Alcoba / baño de servicio','categorical','categoría','functional_service_room_bathroom','research_service','Distinguir alcoba, baño y ambos; no confundir dato desconocido.','construction',$options('functional_service_room_bathroom')),
            'height'=>$make('Altura libre','numeric','m','functional_clear_height_m','research_height','Investigar altura bajo un mismo punto de medición.'),
            'access'=>$make('Tipo de acceso','categorical','categoría','functional_access_type','research_access','Investigar accesibilidad y condiciones operativas.','construction',$options('functional_access_type')),
            'stratum'=>$make('Estrato','categorical','categoría','research_stratum','stratum','Puede delimitar el mercado; no presume distancia económica entre estratos.','tipologias',"1\n2\n3\n4\n5\n6"),
            'deposit'=>$make('Depósitos','numeric','cantidad','research_deposit','ph_deposit_count','Investigar composición y derechos; no liquida su valor separado.','tipologias'),
            'generator'=>$make('Planta eléctrica','ordinal','alcance','research_generator','research_generator','Distinguir ausencia, respaldo parcial y total; un Sí sin cobertura no acredita Total.','attributes',"No\nParcial\nTotal"),
            'destination'=>$make('Destinación / uso observado','categorical','categoría','research_destination','research_destination','Registrar el uso descrito; no sustituye el uso aprobado ni se deduce del tipo de anuncio.','tipologias',"Residencial\nComercial\nOficina\nIndustrial\nMixto\nRural\nDotacional\nOtro"),
        ] + ResearchFactorExtensions::all() + ApartmentResearchFactors::all() + HouseResearchFactors::all() + LocalResearchFactors::all() + LandResearchFactors::all() + ConsultingResearchFactors::all() + BuildingResearchFactors::all() + SubjectAttributeResearch::all());
    }
    public static function forInvestigation(string $type,string $part='',bool $historical=false): array
    {
        return SubjectAttributeResearch::catalog(self::forType($type,$part,false,$historical),$type,$part,$historical);
    }
    public static function forType(string $type,string $part='',bool $legacyOnly=false,bool $retainPreviousViewFactors=false): array
    {
        $keys=match($type) {
            'lote'=>['land','access'], 'parqueadero','deposito'=>['area','access'],
            'casa','finca'=>['land','built','bathrooms','bedrooms','parking','age','levels','view','finishes','service','access','stratum'],
            'apartamento'=>['area','bathrooms','bedrooms','parking','deposit','age','levels','floor','elevator','view','finishes','service','stratum'],
            'bodega'=>['land','built','bathrooms','parking','age','height','access','finishes'],
            'edificio','hotel'=>['land','built','bathrooms','bedrooms','parking','age','levels','elevator','access'],
            'oficina','consultorio','local'=>['area','bathrooms','parking','deposit','age','floor','elevator','view','finishes','access','stratum'],
            default=>[],
        };
        if ($keys!==[]) $keys[]='destination';
        if (in_array($type,['apartamento','casa','oficina','consultorio','local','bodega','edificio','hotel'],true)) $keys[]='generator';
        if (!$legacyOnly) $keys=array_unique(array_merge($keys,ResearchFactorExtensions::keys($type)));
        if (!$retainPreviousViewFactors) $keys=array_diff($keys,['landscape_view','panoramic_view']);
        if (!$legacyOnly && in_array($type,['edificio','hotel'],true)) $keys[]='view';
        if ($type==='apartamento' && !$legacyOnly) $keys=array_merge(ApartmentResearchFactors::keys(),$retainPreviousViewFactors?ApartmentResearchFactors::RETIRED:[]);
        if ($type==='casa' && !$legacyOnly) $keys=array_merge(HouseResearchFactors::keys(),$retainPreviousViewFactors?HouseResearchFactors::RETIRED:[]);
        if ($type==='oficina' && !$legacyOnly) $keys=array_merge(OfficeResearchFactors::keys(),$retainPreviousViewFactors?OfficeResearchFactors::RETIRED:[]);
        if ($type==='local' && !$legacyOnly) $keys=array_merge(LocalResearchFactors::keys(),$retainPreviousViewFactors?LocalResearchFactors::RETIRED:[]);
        if ($type==='bodega' && !$legacyOnly) $keys=array_merge(WarehouseResearchFactors::keys(),$retainPreviousViewFactors?WarehouseResearchFactors::RETIRED:[]);
        if ($type==='lote' && !$legacyOnly) $keys=array_merge(LandResearchFactors::keys(),$retainPreviousViewFactors?LandResearchFactors::RETIRED:[]);
        if ($type==='consultorio' && !$legacyOnly) $keys=array_merge(ConsultingResearchFactors::keys(),$retainPreviousViewFactors?OfficeResearchFactors::RETIRED:[]);
        if ($type==='edificio' && !$legacyOnly) $keys=array_merge(BuildingResearchFactors::keys(),$retainPreviousViewFactors?BuildingResearchFactors::RETIRED:[]);
        $catalog=array_intersect_key(self::all(),array_flip($keys));
        if ($type==='local' && !$legacyOnly && empty($catalog['frontage']['customized'])) {
            $catalog['frontage']['label']='Frente comercial';
            $catalog['frontage']['why']='Medir en metros el frente del local hacia la circulación comercial; no confundir con frente del lote ni longitud de vitrina.';
        }
        if (in_array($type,['local','bodega','edificio'],true) && !$legacyOnly && empty($catalog['parking']['customized'])) $catalog['parking']['why']='Registrar cantidad de celdas vinculadas al inmueble y, en su soporte, inclusión, derechos y características cubierto/independiente; sin factores adicionales de parqueo.';
        if ($type==='edificio' && !$legacyOnly) {
            if (empty($catalog['levels']['customized'])) $catalog['levels']['label']='Número de pisos';
            if (empty($catalog['access_ramp']['customized'])) $catalog['access_ramp']['why']='Verificar si una rampa forma parte del recorrido de entrada al edificio. 0 No; 1 Sí. Desconocido queda pendiente; no acredita por sí sola accesibilidad completa.';
        }
        if (in_array($type,['apartamento','casa','oficina','local','bodega','lote','consultorio','edificio'],true) && !$legacyOnly) {
            $catalog=array_replace(array_flip($keys),$catalog);
            foreach ($catalog as $key=>&$factor) $factor['group']=match($type) { 'casa'=>HouseResearchFactors::group($key),'oficina'=>OfficeResearchFactors::group($key),'local'=>LocalResearchFactors::group($key),'bodega'=>WarehouseResearchFactors::group($key),'lote'=>LandResearchFactors::group($key),'consultorio'=>ConsultingResearchFactors::group($key),'edificio'=>BuildingResearchFactors::group($key),default=>ApartmentResearchFactors::group($key) };
            unset($factor);
        }
        if ($part==='terreno') $catalog=array_intersect_key($catalog,array_flip(array_merge(['land','access','stratum','house_access'],$legacyOnly?[]:array_merge(ResearchFactorExtensions::keys('lote'),['front_exposure','public_services']))));
        if ($part==='construccion') foreach (['land','topography','slope','irrigation'] as $key) unset($catalog[$key]);
        return $legacyOnly?$catalog:UserResearchFactors::scope($catalog,$type,$retainPreviousViewFactors,$part);
    }
}
