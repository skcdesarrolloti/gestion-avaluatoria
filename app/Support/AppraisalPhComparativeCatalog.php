<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalPhComparativeCatalog
{
    public static function commonAreaGroups(): array
    {
        return [
            'esenciales' => ['Bienes comunes esenciales', [
                'terreno_estructura' => 'Terreno, cimentación y estructura',
                'fachadas_cubiertas' => 'Fachadas, cubiertas y envolvente común',
                'circulaciones_esenciales' => 'Circulaciones peatonales indispensables',
                'escaleras' => 'Escaleras',
                'redes_servicios' => 'Redes generales de servicios públicos',
                'cuartos_tecnicos' => 'Cuartos técnicos',
                'equipos_hidrosanitarios' => 'Equipos hidrosanitarios',
                'tanques_bombeo' => 'Tanques y sistema de bombeo',
                'red_incendio' => 'Red contra incendio',
                'evacuacion_seguridad' => 'Zonas de evacuación y seguridad',
            ]],
            'no_esenciales' => ['Bienes comunes no esenciales y amenidades', [
                'lobby' => 'Lobby o recepción',
                'salon_social' => 'Salón social',
                'piscina' => 'Piscina',
                'gimnasio' => 'Gimnasio',
                'zonas_humedas' => 'Zonas húmedas',
                'coworking_salas' => 'Coworking, salas comunes o reuniones',
                'juegos' => 'Juegos infantiles o recreación',
                'zonas_verdes' => 'Zonas verdes o jardines',
                'plazoletas' => 'Plazoletas o áreas exteriores comunes',
                'banos_comunes' => 'Baños comunes',
                'areas_espera' => 'Áreas de atención o espera',
                'cafeteria_apoyo' => 'Cafetería o locales de apoyo',
                'canchas' => 'Canchas o zonas deportivas',
                'bbq' => 'Zona BBQ o terrazas sociales',
            ]],
            'uso_exclusivo' => ['Bienes comunes de uso exclusivo', [
                'parqueaderos_privados' => 'Parqueaderos privados o asignados',
                'depositos' => 'Depósitos o cuartos útiles',
                'terrazas_patios_exclusivos' => 'Terrazas o patios de uso exclusivo',
                'cubiertas_exclusivas' => 'Cubiertas de uso exclusivo',
                'areas_tecnicas_asignadas' => 'Zonas técnicas asignadas',
                'zonas_asignadas_sector' => 'Zonas asignadas a una unidad o sector',
            ]],
            'soporte_operativo' => ['Soporte operativo y técnico común', [
                'porteria' => 'Portería / acceso controlado',
                'vigilancia' => 'Vigilancia',
                'cctv_control' => 'CCTV y control de acceso',
                'administracion_sitio' => 'Administración en sitio',
                'parqueaderos_visitantes' => 'Parqueaderos de visitantes',
                'ascensores' => 'Ascensores',
                'vias_internas' => 'Vías internas',
                'patios_maniobra' => 'Patios de maniobra',
                'muelles_bahias' => 'Muelles, bahías y rampas',
                'bascula' => 'Báscula',
                'control_acceso_pesado' => 'Control de acceso pesado',
                'zona_espera_vehiculos' => 'Zona de espera de vehículos',
                'planta_electrica' => 'Planta eléctrica',
                'subestacion' => 'Subestación',
                'aseo_mantenimiento' => 'Aseo y mantenimiento común',
                'senalizacion' => 'Señalización interna',
                'basuras_residuos' => 'Manejo de basuras o residuos',
                'cerramiento' => 'Cerramiento y seguridad perimetral',
            ]],
        ];
    }

    public static function commonAreas(): array
    {
        $items = [];
        foreach (self::commonAreaGroups() as $group) $items += $group[1];
        return $items;
    }

    public static function typologyPriorities(): array
    {
        return [
            'residencial' => ['porteria', 'vigilancia', 'cctv_control', 'administracion_sitio', 'salon_social', 'piscina',
                'gimnasio', 'juegos', 'zonas_verdes', 'bbq', 'canchas', 'parqueaderos_visitantes', 'ascensores', 'aseo_mantenimiento'],
            'oficinas' => ['lobby', 'areas_espera', 'banos_comunes', 'coworking_salas', 'parqueaderos_visitantes',
                'ascensores', 'porteria', 'cctv_control', 'administracion_sitio', 'planta_electrica', 'tanques_bombeo',
                'subestacion', 'red_incendio', 'aseo_mantenimiento'],
            'comercio' => ['lobby', 'areas_espera', 'banos_comunes', 'parqueaderos_visitantes', 'cctv_control',
                'vigilancia', 'muelles_bahias', 'basuras_residuos', 'planta_electrica', 'tanques_bombeo', 'red_incendio',
                'senalizacion', 'cafeteria_apoyo'],
            'bodegas' => ['porteria', 'cctv_control', 'cerramiento', 'vias_internas', 'patios_maniobra', 'muelles_bahias',
                'bascula', 'control_acceso_pesado', 'zona_espera_vehiculos', 'red_incendio', 'subestacion', 'planta_electrica',
                'senalizacion', 'basuras_residuos'],
            'mixto' => ['lobby', 'porteria', 'cctv_control', 'parqueaderos_visitantes', 'ascensores', 'vias_internas',
                'muelles_bahias', 'zonas_asignadas_sector', 'administracion_sitio', 'planta_electrica', 'red_incendio',
                'basuras_residuos'],
        ];
    }

    public static function dotationLevels(): array
    {
        return [
            'basica' => 'Básica: soporte común mínimo para operar la copropiedad.',
            'estandar' => 'Estándar: dotación esperable para su tipología y mercado.',
            'superior' => 'Superior: varios elementos mejoran funcionalidad o deseabilidad.',
            'especializada' => 'Especializada: infraestructura técnica u operativa diferencial.',
            'sin_evidencia' => 'Sin evidencia: el documento no permite concluir.',
        ];
    }

    public static function commonAreaRules(): array
    {
        return [
            'terreno_estructura' => ['bienes comunes esenciales', 'terreno', 'cimientos', 'estructura', 'columnas', 'placas'],
            'fachadas_cubiertas' => ['fachada', 'fachadas', 'cubierta', 'cubiertas', 'tejado', 'envolvente'],
            'circulaciones_esenciales' => ['pasillos', 'circulaciones', 'circulacion peatonal'],
            'escaleras' => ['escalera', 'escaleras'],
            'redes_servicios' => ['redes generales', 'servicios publicos', 'acueducto', 'alcantarillado', 'instalaciones generales'],
            'cuartos_tecnicos' => ['cuarto tecnico', 'cuartos tecnicos', 'cuarto de maquinas'],
            'equipos_hidrosanitarios' => ['equipo hidroneumatico', 'equipos hidrosanitarios', 'bombas', 'bombeo'],
            'tanques_bombeo' => ['tanques', 'bombeo', 'tanque elevado', 'tanque subterraneo'],
            'red_incendio' => ['red contra incendio', 'hidrante', 'gabinete contra incendio', 'sistema contra incendio'],
            'evacuacion_seguridad' => ['evacuacion', 'ruta de evacuacion', 'salida de emergencia'],
            'lobby' => ['lobby', 'recepcion', 'vestibulo'],
            'salon_social' => ['salon social', 'sala comunal'],
            'piscina' => ['piscina'],
            'gimnasio' => ['gimnasio'],
            'zonas_humedas' => ['sauna', 'turco', 'zona humeda'],
            'coworking_salas' => ['coworking', 'sala de reuniones', 'salas comunes'],
            'juegos' => ['juegos infantiles', 'recreacion'],
            'zonas_verdes' => ['zonas verdes', 'jardines'],
            'plazoletas' => ['plazoleta', 'plazoletas'],
            'banos_comunes' => ['banos comunes', 'baños comunes'],
            'areas_espera' => ['sala de espera', 'areas de espera', 'atencion al publico'],
            'cafeteria_apoyo' => ['cafeteria', 'locales de apoyo'],
            'canchas' => ['cancha', 'canchas'],
            'bbq' => ['bbq', 'barbecue', 'asados'],
            'parqueaderos_privados' => ['parqueadero privado', 'garaje', 'estacionamiento privado'],
            'depositos' => ['deposito', 'cuarto util'],
            'terrazas_patios_exclusivos' => ['uso exclusivo', 'terraza', 'patio exclusivo'],
            'cubiertas_exclusivas' => ['cubierta de uso exclusivo'],
            'areas_tecnicas_asignadas' => ['zona tecnica asignada', 'area tecnica asignada'],
            'zonas_asignadas_sector' => ['asignado al sector', 'uso exclusivo del sector'],
            'porteria' => ['porteria', 'portería', 'acceso controlado'],
            'vigilancia' => ['vigilancia', 'seguridad permanente'],
            'cctv_control' => ['cctv', 'control de acceso', 'camaras', 'circuito cerrado'],
            'administracion_sitio' => ['administracion en sitio', 'oficina de administracion'],
            'parqueaderos_visitantes' => ['visitantes', 'parqueaderos de visitantes'],
            'ascensores' => ['ascensor', 'ascensores'],
            'vias_internas' => ['vias internas', 'vías internas', 'circulacion vehicular'],
            'patios_maniobra' => ['patio de maniobra', 'maniobra', 'radio de giro'],
            'muelles_bahias' => ['muelle', 'bahia', 'bahía', 'rampa'],
            'bascula' => ['bascula', 'báscula'],
            'control_acceso_pesado' => ['control de acceso pesado', 'tractomula', 'vehiculo pesado'],
            'zona_espera_vehiculos' => ['zona de espera', 'espera de vehiculos'],
            'planta_electrica' => ['planta electrica', 'planta eléctrica'],
            'subestacion' => ['subestacion', 'subestación'],
            'aseo_mantenimiento' => ['aseo', 'mantenimiento'],
            'senalizacion' => ['senalizacion', 'señalizacion', 'señalización'],
            'basuras_residuos' => ['basuras', 'residuos'],
            'cerramiento' => ['cerramiento', 'perimetral'],
        ];
    }


    public static function normativeAcademy(): array
    {
        return [
            ['src'=>'Ley 675 de 2001', 'asks'=>'Distinguir bienes privados, bienes comunes, coeficientes, expensas, administración y reglas del reglamento.', 'use'=>'La ficha separa identificación PH, unidad, coeficiente, bienes comunes, reglas, administración y salvedades para no mezclar soporte con conclusión.'],
            ['src'=>'Decreto 1420 de 1998', 'asks'=>'En inmuebles sometidos a PH, el avalúo debe considerar derechos, coeficientes, áreas, bienes comunes e incidencia propia de la copropiedad.', 'use'=>'El módulo no toma el primer dato del reglamento; vincula matrícula, unidad, tipología y soporte antes de construir el texto.'],
            ['src'=>'NTS S 03 / NTS I 01', 'asks'=>'El informe debe ser suficiente, trazable y claro sobre información examinada, soportes, salvedades y alcance.', 'use'=>'Los resúmenes depurados son el texto corto para informe; los extractos extensos quedan como soporte revisable.'],
            ['src'=>'NTS M 01', 'asks'=>'La metodología debe usar información pertinente y comparación contra bienes de características similares.', 'use'=>'La dotación común se compara por tipología: residencial, oficinas, comercio, bodegas o mixto; no contra cualquier PH.'],
            ['src'=>'IVS 104 / IVS 106', 'asks'=>'Los datos e insumos relevantes deben documentarse y el reporte debe permitir seguir el juicio profesional.', 'use'=>'Cada dato puede quedar como reglamento/documento, visita, criterio del analista, pendiente o alerta; esa fuente orienta la confiabilidad.'],
            ['src'=>'IVS 400', 'asks'=>'En derechos inmobiliarios se analiza el interés valuado, derechos asociados, restricciones, cargas y características del inmueble.', 'use'=>'La incidencia valuatoria resume si la PH aporta, limita o es neutra para funcionalidad, comercialización y comparabilidad.'],
            ['src'=>'Ayuda interna', 'asks'=>'Una mención documental no prueba por sí sola estado actual, funcionamiento ni vigencia administrativa.', 'use'=>'Reglamento y OCR alimentan la matriz; visita, fotos, paz y salvo, administración o certificado confirman lo que sea material.'],
        ];
    }

    public static function deliverableFilter(): array
    {
        return [
            ['type'=>'Va al entregable', 'tone'=>'ok', 'items'=>'Nombre y tipo de PH, vínculo con la unidad, coeficiente cuando aplique, tipología, bienes comunes relevantes, restricciones materiales, cargas vigentes, salvedades e incidencia valuatoria.'],
            ['type'=>'Queda como soporte', 'tone'=>'info', 'items'=>'Extractos largos del reglamento, páginas OCR, listados completos de amenidades sin efecto, trazabilidad de documentos, referencias de búsqueda y notas de lectura.'],
            ['type'=>'Requiere validar', 'tone'=>'warn', 'items'=>'Estado actual de zonas comunes, funcionamiento de equipos, paz y salvo, pólizas, cuotas extraordinarias, reformas, administración vigente y páginas con baja lectura.'],
            ['type'=>'No concluir automático', 'tone'=>'risk', 'items'=>'No afirmar conservación, operación, cumplimiento, seguridad o impacto en valor solo por aparecer en el reglamento; debe existir soporte suficiente o salvedad.'],
        ];
    }

    public static function normNotes(): array
    {
        return [
            'Bienes comunes esenciales y no esenciales: Ley 675 de 2001, arts. 3, 19, 20 y 24.',
            'Coeficientes, módulos y expensas comunes: Ley 675 de 2001, arts. 25 a 31.',
            'Incidencia en avalúos de inmuebles PH: Decreto 1420 de 1998, arts. 21 y 22.',
            'Trazabilidad, suficiencia y salvedades del informe: Resolución IGAC 941 de 2026 y anexo técnico.',
            'Juicio profesional, datos, insumos y limitaciones: IVS 2025, IVS 104, IVS 105, IVS 106 e IVS 400.',
        ];
    }
}
