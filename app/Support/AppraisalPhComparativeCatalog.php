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
            ['src'=>'Ley 675 · arts. 3, 19, 20 y 24', 'says'=>'Define bienes privados y comunes; diferencia bienes comunes esenciales y no esenciales; recuerda que los comunes sirven a la existencia, seguridad, conservación, uso o disfrute de la copropiedad.', 'use'=>'Identificar cuáles bienes comunes son soporte real de la unidad y cuáles son solo amenidades o menciones documentales.', 'report'=>'Al entregable pasan los comunes relevantes por tipología; el listado completo queda como soporte.'],
            ['src'=>'Ley 675 · arts. 25 a 31', 'says'=>'Regula coeficientes, módulos de contribución y participación en expensas comunes según el reglamento.', 'use'=>'Vincular coeficiente, cuota, expensas, módulos o cargas con el bien sujeto; no tomar el primer coeficiente encontrado.', 'report'=>'Al entregable pasa el coeficiente o carga verificable; lo incierto queda como salvedad.'],
            ['src'=>'Decreto 1420 · arts. 21 y 22', 'says'=>'En bienes sometidos a PH, el avalúo debe considerar el derecho valuado y la información del régimen que incida en áreas, coeficientes, bienes comunes y condiciones propias del inmueble.', 'use'=>'Conectar unidad privada, matrícula, reglamento, áreas y comunes antes de concluir incidencia valuatoria.', 'report'=>'Al entregable pasa la incidencia PH: aporta, limita, es neutra o queda pendiente por validar.'],
            ['src'=>'NTS S 03 / NTS I 01', 'says'=>'El informe debe ser claro, suficiente y trazable: información usada, soporte revisado, metodología, análisis, salvedades y alcance.', 'use'=>'Convertir el OCR y el reglamento en resúmenes depurados; no copiar páginas largas al informe.', 'report'=>'Al entregable va texto corto; el extracto documental queda debajo como respaldo.'],
            ['src'=>'NTS M 01', 'says'=>'La metodología debe apoyarse en información pertinente, verificable y comparable según el tipo de bien y el mercado analizado.', 'use'=>'Comparar dotación común solo contra copropiedades de la misma tipología: oficinas con oficinas, bodegas con bodegas, etc.', 'report'=>'Al entregable pasa la lectura comparativa cuando incide en funcionalidad, deseabilidad o comercialización.'],
            ['src'=>'IVS 104 / IVS 106', 'says'=>'Los datos significativos deben ser relevantes y documentados; el reporte debe permitir seguir datos, supuestos, limitaciones y juicio profesional.', 'use'=>'Marcar cada dato como documento, visita, criterio, pendiente o alerta, según su confiabilidad.', 'report'=>'Al entregable pasan los datos confiables y las limitaciones necesarias; lo demás queda como trazabilidad.'],
            ['src'=>'IVS 400', 'says'=>'Para derechos inmobiliarios se analiza el interés valuado, derechos asociados, restricciones, cargas, características físicas, uso y circunstancias que afecten el valor.', 'use'=>'Revisar si la PH aporta seguridad, operación, imagen, restricciones, cargas o soporte común frente a comparables.', 'report'=>'Al entregable pasa la conclusión valuatoria, no el inventario completo del reglamento.'],
            ['src'=>'Ayuda interna', 'says'=>'La lectura automática no certifica existencia actual, funcionamiento, estado físico, cumplimiento ni vigencia administrativa.', 'use'=>'Usar reglamento y OCR como punto de partida; confirmar lo material con visita, fotos, administración, paz y salvo, pólizas o certificados.', 'report'=>'Si no está confirmado, va como pendiente o salvedad; no como afirmación cerrada.'],
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
