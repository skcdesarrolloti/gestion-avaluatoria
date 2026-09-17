<?php
declare(strict_types=1);
namespace App\Services;

final class MidasNeighborhoodProfile
{
    public static function forSubject(array $subject): ?array
    {
        return match (self::key((string) ($subject['neighborhood_name'] ?? ''))) {
            'crespo' => self::crespo($subject),
            'castillogrande' => self::castillogrande($subject),
            default => null,
        };
    }

    private static function crespo(array $subject): array
    {
        $place = 'Crespo, Histórica y del Caribe Norte, UCG 1, Cartagena de Indias';
        return self::base($subject, [
            'barrio' => 'Crespo', 'localidad' => 'Histórica y del Caribe Norte', 'comuna' => 'UCG 1',
            'microsector' => 'Residencial y servicios aeroportuarios', 'area' => '141,70',
            'perimetro' => '9.535,82', 'latitud' => '10.4329', 'longitud' => '-75.5197',
            'norte' => 'Avenida Santander.', 'sur' => 'Calle 70 y sectores de Cabrero y Marbella.',
            'este' => 'Ciénaga de la Virgen.', 'oeste' => 'Mar Caribe.',
            'observacion' => $place . '. MIDAS reporta área 141,70 ha, perímetro 9.535,82 m, '
                . '5.021 personas, 2.182 viviendas y 1.754 hogares según DANE 2018.',
            'midas' => 'Resultado Territorios: Barrio Crespo, categoría barrio, UCG 1, Localidad Histórica '
                . 'y del Caribe Norte, fuente POT/Acuerdo 006 de 2003.',
            'vias' => 'MIDAS reporta 2 rutas y 47 paraderos asociados a Crespo; validar jerarquía vial, '
                . 'señalización y accesos en visita.',
            'amoblamiento' => 'MIDAS muestra puestos de votación, una institución educativa y escenarios '
                . 'deportivos en resultados del sector.',
            'equipamientos' => ['Educativo', 'Recreativo', 'Deportivo', 'Institucional'],
            'edificaciones' => ['Educación', 'Recreativo / deportivo', 'Institucional'],
            'edificaciones_texto' => 'Institución educativa, escenarios deportivos, puestos de votación '
                . 'y equipamientos públicos identificables en MIDAS.',
            'externalidades' => 'MIDAS registra CAI, cuadrantes, videovigilancia, zonas verdes e indicadores '
                . 'de cobertura vegetal asociados al barrio.',
            'dinamica' => 'Residencial consolidada con servicios aeroportuarios y equipamientos urbanos.',
            'alertas' => 'Validar ruido aeroportuario, tráfico, accesos específicos, restricciones normativas '
                . 'y condiciones ambientales en campo.',
        ]);
    }

    private static function castillogrande(array $subject): array
    {
        $place = 'Castillogrande, Histórica y del Caribe Norte, UCG 1, Cartagena de Indias';
        return self::base($subject, [
            'barrio' => 'Castillogrande', 'localidad' => 'Histórica y del Caribe Norte', 'comuna' => 'UCG 1',
            'microsector' => 'Residencial de alta densidad', 'area' => '41,96',
            'perimetro' => '4.358,82', 'latitud' => '10.3939', 'longitud' => '-75.5450',
            'norte' => 'Bocagrande y conexión vial hacia la península.',
            'sur' => 'Club Naval y frente de bahía de Cartagena.',
            'este' => 'Bahía de Cartagena.',
            'oeste' => 'Mar Caribe y playa de Castillogrande.',
            'observacion' => $place . '. MIDAS reporta fuente POT/Acuerdo 006 de 2003, área 41,96 ha '
                . 'y perímetro 4.358,82 m; confirmar en mapa el alcance real frente al inmueble.',
            'midas' => 'Resultado Territorios: Barrio Castillogrande, categoría barrio, UCG 1, Localidad '
                . 'Histórica y del Caribe Norte, fuente Decreto 0977 de 2001 (POT) - Acuerdo 006 de 2003.',
            'vias' => 'Activar en MIDAS las capas de transporte y movilidad para identificar rutas, paraderos, '
                . 'jerarquía vial y accesos; por localización peninsular, validar congestión y accesibilidad real.',
            'amoblamiento' => 'MIDAS permite revisar equipamiento urbano; en la consulta del barrio se observan capas '
                . 'de instituciones educativas y equipamientos urbanos que deben marcarse si quedan dentro '
                . 'del área de influencia del inmueble.',
            'equipamientos' => ['Educativo', 'Institucional', 'Recreativo'],
            'edificaciones' => ['Educación', 'Institucional', 'Recreativo / deportivo'],
            'edificaciones_texto' => 'Instituciones educativas y equipamientos urbanos revisables en MIDAS; registrar '
                . 'solo los hitos presentes dentro del sector de influencia del inmueble.',
            'externalidades' => 'Entorno residencial de alta densidad con frente marítimo y de bahía; validar ruido, '
                . 'congestión, actividad turística y relación inmediata con comercio y servicios.',
            'dinamica' => 'Residencial de alta densidad con actividad turística y servicios complementarios.',
            'alertas' => 'Validar movilidad de acceso, presión turística, exposición costera, estacionamiento, '
                . 'condiciones ambientales y normas urbanísticas puntuales.',
        ]);
    }

    private static function base(array $subject, array $data): array
    {
        $barrio = (string) $data['barrio'];
        $microsector = self::pick($subject['zone_sector'] ?? '', $data['microsector']);
        return [
            '01' => [
                'pais' => 'Colombia', 'departamento' => 'Bolívar',
                'municipio_distrito' => 'Cartagena de Indias', 'barrio' => $barrio,
                'localidad' => $data['localidad'], 'comuna' => $data['comuna'], 'microsector' => $microsector,
                'mapa_barrio_url' => 'https://midas.cartagena.gov.co/#/home',
                'fuente_base_delimitacion' => 'MIDAS Cartagena: Territorios - Barrio ' . $barrio . '.',
                'fuente_base_satelital' => 'MIDAS Cartagena / Google Maps como apoyo visual.',
                'latitud_centro' => $data['latitud'], 'longitud_centro' => $data['longitud'],
                'area_hectareas' => $data['area'], 'perimetro_metros' => $data['perimetro'],
                'norte' => $data['norte'], 'sur' => $data['sur'], 'este' => $data['este'],
                'oeste' => $data['oeste'], 'observacion_localizacion' => $data['observacion'],
            ],
            '02' => ['mapa_delimitacion_url' => 'https://midas.cartagena.gov.co/#/home',
                'imagen_satelital_url' => 'https://www.google.com/maps/search/?api=1&query='
                    . rawurlencode($barrio . ', Cartagena de Indias, Bolívar, Colombia'),
                'medicion_source' => 'MIDAS Cartagena / Secretaría de Planeación Distrital.',
                'cartografia_status' => 'AUTOMATICO'],
            '03' => self::services($barrio),
            '04' => ['descripcion_general_sector' => $data['dinamica'],
                'uso_predominante' => 'Residencial', 'usos_complementarios' => 'Servicios, comercio de soporte y turismo.',
                'actividad_economica_predominante' => 'Servicios'],
            '05' => ['norma_base' => 'Decreto 0977 de 2001 (POT) - Acuerdo 006 de 2003.',
                'fuente_normativa' => 'MIDAS Cartagena / Secretaría de Planeación Distrital.',
                'midas_lectura_manual' => $data['midas']],
            '06' => ['vias_detalle' => $data['vias'],
                'comentario_vias_senalizacion' => $data['vias']],
            '07' => ['equipamientos_seleccionados' => $data['equipamientos'],
                'comentario_amoblamiento' => $data['amoblamiento']],
            '08' => ['comentario_estratificacion' => 'Complementar con consulta oficial de estratificación; '
                . 'MIDAS aporta base territorial y capas urbanas para orientar la revisión.'],
            '11' => ['servicio_transporte_predominante' => 'Taxi y rutas complementarias',
                'tipos_transporte_identificados' => ['Bus urbano', 'Taxi', 'Peatonal'],
                'detalle_rutas_transporte' => 'Revisar en MIDAS capas de transporte y paraderos para el barrio.',
                'detalle_paraderos_transporte' => 'Identificar paraderos visibles en MIDAS y validar distancia al inmueble.'],
            '12' => ['categorias_edificaciones' => $data['edificaciones'],
                'edificaciones_ancla' => $data['edificaciones_texto']],
            '13' => ['externalidades_positivas' => ['Buena conectividad urbana', 'Proximidad a equipamientos',
                'Entorno residencial consolidado'], 'observacion_externalidades' => $data['externalidades']],
            '15' => ['dinamica_sectorial' => $data['dinamica'],
                'fortalezas_sector' => 'Soporte territorial MIDAS, localización consolidada, accesibilidad urbana '
                    . 'y presencia de equipamientos revisables por capas.',
                'condicionantes_sector' => $data['alertas']],
            '16' => ['literal_a_localizacion' => $data['observacion'],
                'literal_c_accesibilidad' => $data['vias'],
                'literal_g_servicios' => 'Servicios públicos urbanos disponibles a escala sectorial; confirmar '
                    . 'prestador, acometida, continuidad y microrruta del inmueble.',
                'literal_h_uso_suelo' => $data['midas']],
        ];
    }

    private static function services(string $barrio): array
    {
        return ['fuente_servicios' => 'MIDAS Cartagena: servicios públicos, alumbrado, aseo y drenaje urbano.',
            'acueducto' => 'SI',
            'acueducto_detalle' => 'Aguas de Cartagena S.A. E.S.P. - Acuacar. Confirmar cobertura puntual.',
            'alcantarillado' => 'SI',
            'alcantarillado_detalle' => 'Aguas de Cartagena S.A. E.S.P. - Acuacar. Confirmar cobertura puntual.',
            'energia' => 'SI', 'energia_detalle' => 'Afinia - Grupo EPM. Confirmar continuidad en campo.',
            'gas' => 'SI', 'gas_detalle' => 'Surtigas S.A. E.S.P. Confirmar acometida o cobertura.',
            'aseo_prestadores' => ['Pacaribe', 'Veolia', 'No verificado'],
            'aguas_lluvias_detalle' => 'Activar capas MIDAS de drenaje, alcantarillado pluvial, canales y estaciones '
                . 'si aplican al barrio; conservar solo lo verificable para el inmueble.',
            'aseo_detalle' => 'Para ' . $barrio . ', consultar empresa y microrruta de aseo en el prestador; '
                . 'registrar frecuencia y evidencia si el dato queda confirmado.'];
    }

    private static function key(string $value): string
    {
        $text = strtr(mb_strtolower(trim($value)), ['á' => 'a', 'é' => 'e', 'í' => 'i',
            'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n']);
        return preg_replace('/[^a-z0-9]+/', '', $text) ?? '';
    }

    private static function pick(mixed ...$values): string
    {
        foreach ($values as $value) {
            $text = trim((string) $value);
            if ($text !== '') return $text;
        }
        return '';
    }
}
