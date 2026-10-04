<?php
declare(strict_types=1);
namespace App\Services;

/** Observable candidates, not compulsory predictors or economic adjustment weights. */
final class ResearchFactorExtensions
{
    public static function all(): array
    {
        $out=[];
        $binary=[
            'balcony'=>['Balcón','Precisar si pertenece a la unidad; no sumar su superficie a la base privada construida.'],
            'terrace'=>['Terraza','Confirmar pertenencia, uso y derechos; presencia no acredita área privada.'],
            'pool'=>['Piscina disponible','Confirmar si es privada o común y si esta unidad tiene acceso.'],
            'gym'=>['Gimnasio disponible','Confirmar disponibilidad para la unidad, no sólo cercanía a un gimnasio.'],
            'security'=>['Vigilancia presencial','Presencia comprobada; registrar horario. Portería no acredita vigilancia 24 horas.'],
            'air_conditioning'=>['Aire acondicionado instalado','Distinguir equipo incluido de preinstalación o posibilidad de instalarlo.'],
            'accessible'=>['Ruta accesible sin escalones','Verificar continuidad desde el acceso hasta la unidad; un ascensor no basta.'],
            'vehicle_access'=>['Acceso vehicular','Confirmar acceso físico permitido al predio y restricciones de vehículo.'],
            'loading_access'=>['Acceso para cargue / descargue','Confirmar ingreso y operación permitidos; registrar dimensiones y restricciones.'],
            'restricted_access'=>['Restricción de acceso','0 = sin restricción comprobada; 1 = con restricción comprobada. No investigado queda pendiente; presencia no significa mejor.'],
            'corner'=>['Ubicación esquinera','Posición física de la unidad o lote; no es un nivel de vista.'],
            'landscape_view'=>['Vista paisajística','Confirmar paisaje visible desde la unidad; describirlo y conservar evidencia.'],
            'covered_parking'=>['Parqueo cubierto','Confirmar cobertura del espacio estudiado; no inferirla de parqueaderos del edificio.'],
            'independent_parking'=>['Parqueo sin bloqueo por otra celda','Independencia de maniobra, no matrícula independiente ni propiedad privada.'],
            'water'=>['Agua con servicio operativo','Red frente al lote no acredita conexión operativa; verificar fuente y continuidad.'],
            'electricity'=>['Energía con servicio operativo','Red cercana no acredita conexión; registrar condiciones comprobadas.'],
            'sewer'=>['Alcantarillado conectado','Distinguir conexión operativa de disponibilidad de red o sistema séptico.'],
            'irrigation'=>['Sistema de riego operativo','Distinguir instalación operativa de posibilidad de instalar; verificar suministro.'],
            'shopfront'=>['Vitrina comercial','Confirmar existencia y frente visible; no inferir flujo comercial por su presencia.'],
            'mezzanine'=>['Mezanine','Verificar existencia, área, altura y soporte; no duplicar superficie construida.'],
            'humidity'=>['Humedad visible','0 = sin humedad observada; 1 = con humedad. Ausencia de inspección queda pendiente.'],
        ];
        foreach ($binary as $key=>[$label,$why]) $out[$key]=self::make($key,$label,'binary','sí/no',$why,"No\nSí");
        $numeric=[
            'loading_bays'=>['Muelles / bahías de cargue','cantidad','Contar posiciones habilitadas; describir cuáles operan a nivel o desnivel.','functional_loading_bays_count'],
            'frontage'=>['Frente','m','Medir frente bajo la misma definición; no confundir longitud de vitrina con frente del lote.','research_frontage'],
            'depth'=>['Fondo','m','Medir profundidad del lote o unidad bajo la misma definición.','research_depth'],
            'power'=>['Potencia eléctrica instalada','kW','Verificar potencia instalada disponible; no mezclar kW, kVA y tensión.','research_power'],
            'floor_load'=>['Carga admisible del piso','kg/m²','Exigir soporte técnico; psi del concreto no equivale a carga admisible por m².','research_floor_load'],
            'slope'=>['Pendiente del terreno','%','Mayor número = mayor pendiente, no mejor terreno; registrar método y representatividad.','research_slope'],
            'units_count'=>['Unidades interiores','cantidad','Precisar si son apartamentos, oficinas o locales; no mezclar habitaciones hoteleras.','research_units_count'],
            'guest_capacity'=>['Capacidad de huéspedes','personas','Capacidad habilitada de alojamiento; no inferirla multiplicando habitaciones por una ocupación supuesta.','research_guest_capacity'],
        ];
        foreach ($numeric as $key=>[$label,$unit,$why,$subject]) $out[$key]=self::make($key,$label,'numeric',$unit,$why,'',$subject);
        $out['topography']=self::make('topography','Topografía descrita','categorical','clase','Clases de relieve; mixto no constituye un nivel superior.',"Plana\nOndulada\nEscarpada\nMixta");
        $out['finish_quality']=self::make('finish_quality','Calidad de acabados terminados','ordinal','nivel','Menor a mayor calidad declarada: justificar materiales y ejecución con una misma pauta. Obra gris es estado de ejecución y queda pendiente aquí.',"Básico / económico\nMedio\nBueno\nAlto\nSuperior / lujo",'functional_finish_quality','finish_quality');
        return $out;
    }
    private static function make(string $key,string $label,string $kind,string $unit,string $why,string $categories='',string $subject='',string $sample=''): array
    {
        return ['label'=>$label,'kind'=>$kind,'unit'=>$unit,'subject'=>$subject?:'research_'.$key,
            'sample'=>$sample?:'research_'.$key,'why'=>$why,'section'=>'attributes','categories'=>$categories];
    }
    public static function keys(string $type): array
    {
        $common=['security','accessible'];
        $residential=['balcony','terrace','pool','gym','air_conditioning','corner','landscape_view','covered_parking','independent_parking','finish_quality'];
        $terrain=['frontage','depth','topography','slope','vehicle_access','restricted_access','water','electricity','sewer','corner'];
        return match($type) {
            'apartamento'=>array_merge($common,$residential),
            'casa'=>array_merge($common,$residential,['vehicle_access','restricted_access']),
            'oficina','consultorio'=>array_merge($common,['air_conditioning','corner','landscape_view','covered_parking','independent_parking','finish_quality']),
            'local'=>array_merge($common,['frontage','shopfront','mezzanine','height','air_conditioning','corner','loading_access','finish_quality']),
            'bodega'=>array_merge($common,['frontage','depth','loading_access','vehicle_access','restricted_access','loading_bays','power','floor_load','mezzanine','finish_quality']),
            'lote'=>$terrain,
            'finca'=>array_merge($terrain,['irrigation','pool','terrace','landscape_view','finish_quality']),
            'edificio'=>array_merge($common,['units_count','air_conditioning','loading_bays','vehicle_access','covered_parking','finish_quality']),
            'hotel'=>array_merge($common,$residential,['guest_capacity']),
            'parqueadero'=>['security','vehicle_access','restricted_access','covered_parking','independent_parking','frontage','depth','height'],
            'deposito'=>['security','accessible','vehicle_access','restricted_access','humidity','height'],
            default=>[],
        };
    }
}
