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
        return [
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
            'finishes'=>$make('Acabados','categorical','categoría','functional_finish_quality','finish_quality','Definir clases observables; no atribuir pesos económicos.','construction',$options('functional_finish_quality')),
            'service'=>$make('Alcoba / baño de servicio','categorical','categoría','functional_service_room_bathroom','research_service','Distinguir alcoba, baño y ambos; no confundir dato desconocido.','construction',$options('functional_service_room_bathroom')),
            'height'=>$make('Altura libre','numeric','m','functional_clear_height_m','research_height','Investigar altura bajo un mismo punto de medición.'),
            'access'=>$make('Tipo de acceso','categorical','categoría','functional_access_type','research_access','Investigar accesibilidad y condiciones operativas.','construction',$options('functional_access_type')),
            'stratum'=>$make('Estrato','categorical','categoría','research_stratum','stratum','Puede delimitar el mercado; no presume distancia económica entre estratos.','tipologias',"1\n2\n3\n4\n5\n6"),
            'deposit'=>$make('Depósitos','numeric','cantidad','research_deposit','ph_deposit_count','Investigar composición y derechos; no liquida su valor separado.','tipologias'),
            'generator'=>$make('Planta eléctrica','ordinal','alcance','research_generator','research_generator','Distinguir ausencia, respaldo parcial y total; un Sí sin cobertura no acredita Total.','attributes',"No\nParcial\nTotal"),
            'destination'=>$make('Destinación / uso observado','categorical','categoría','research_destination','research_destination','Registrar el uso descrito; no sustituye el uso aprobado ni se deduce del tipo de anuncio.','tipologias',"Residencial\nComercial\nOficina\nIndustrial\nMixto\nRural\nDotacional\nOtro"),
        ] + ResearchFactorExtensions::all() + ApartmentResearchFactors::all() + HouseResearchFactors::all();
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
        $catalog=array_intersect_key(self::all(),array_flip($keys));
        if (in_array($type,['apartamento','casa'],true) && !$legacyOnly) {
            $catalog=array_replace(array_flip($keys),$catalog);
            foreach ($catalog as $key=>&$factor) $factor['group']=$type==='casa'?HouseResearchFactors::group($key):ApartmentResearchFactors::group($key);
            unset($factor);
        }
        if ($part==='terreno') $catalog=array_intersect_key($catalog,array_flip(array_merge(['land','access','stratum','house_access'],$legacyOnly?[]:ResearchFactorExtensions::keys('lote'))));
        if ($part==='construccion') foreach (['land','topography','slope','irrigation'] as $key) unset($catalog[$key]);
        return $catalog;
    }
}
