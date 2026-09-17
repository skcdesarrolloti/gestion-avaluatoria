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
            'perimetro' => '9.535,82',
            'norte' => 'Avenida Santander.', 'sur' => 'Calle 70 y sectores de Cabrero y Marbella.',
            'este' => 'Ciénaga de la Virgen.', 'oeste' => 'Mar Caribe.',
            'observacion' => $place . '. MIDAS reporta área 141,70 ha y perímetro 9.535,82 m. '
                . 'Confirmar linderos, accesos e influencia real frente al inmueble.',
            'midas' => 'Resultado Territorios: Barrio Crespo, categoría barrio, UCG 1, Localidad Histórica '
                . 'y del Caribe Norte, fuente POT/Acuerdo 006 de 2003.',
            'vias' => 'MIDAS reporta 2 rutas y 47 paraderos asociados a Crespo; validar jerarquía vial, '
                . 'señalización y accesos en visita.',
            'transporte' => ['Bus urbano', 'Taxi', 'Peatonal'],
            'paraderos' => 'MIDAS reporta 47 paraderos asociados a Crespo.',
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
            'perimetro' => '4.358,82',
            'norte' => 'Bocagrande y conexión vial hacia la península.',
            'sur' => 'Club Naval y frente de bahía de Cartagena.',
            'este' => 'Bahía de Cartagena.',
            'oeste' => 'Mar Caribe y playa de Castillogrande.',
            'observacion' => $place . '. MIDAS reporta fuente POT/Acuerdo 006 de 2003, área 41,96 ha '
                . 'y perímetro 4.358,82 m; confirmar en mapa el alcance real frente al inmueble.',
            'midas' => 'Resultado Territorios: Barrio Castillogrande, categoría barrio, UCG 1, Localidad '
                . 'Histórica y del Caribe Norte, fuente Decreto 0977 de 2001 (POT) - Acuerdo 006 de 2003.',
        ]);
    }

    private static function base(array $subject, array $data): array
    {
        $barrio = (string) $data['barrio'];
        $microsector = self::pick($subject['zone_sector'] ?? '', $data['microsector']);
        $suggestions = [
            '01' => [
                'pais' => 'Colombia', 'departamento' => 'Bolívar',
                'municipio_distrito' => 'Cartagena de Indias', 'barrio' => $barrio,
                'localidad' => $data['localidad'], 'comuna' => $data['comuna'], 'microsector' => $microsector,
                'mapa_barrio_url' => 'https://midas.cartagena.gov.co/#/home',
                'fuente_base_delimitacion' => 'MIDAS Cartagena: Territorios - Barrio ' . $barrio . '.',
                'fuente_base_satelital' => 'MIDAS Cartagena / Google Maps como apoyo visual.',
                'area_hectareas' => $data['area'], 'perimetro_metros' => $data['perimetro'],
                'norte' => $data['norte'], 'sur' => $data['sur'], 'este' => $data['este'],
                'oeste' => $data['oeste'], 'observacion_localizacion' => $data['observacion'],
            ],
            '02' => ['mapa_delimitacion_url' => 'https://midas.cartagena.gov.co/#/home',
                'imagen_satelital_url' => 'https://www.google.com/maps/search/?api=1&query='
                    . rawurlencode($barrio . ', Cartagena de Indias, Bolívar, Colombia'),
                'medicion_source' => 'MIDAS Cartagena / Secretaría de Planeación Distrital.',
                'cartografia_status' => 'AUTOMATICO'],
            '05' => ['norma_base' => 'Decreto 0977 de 2001 (POT) - Acuerdo 006 de 2003.',
                'fuente_normativa' => 'MIDAS Cartagena / Secretaría de Planeación Distrital.',
                'midas_lectura_manual' => $data['midas']],
            '16' => ['literal_a_localizacion' => $data['observacion'],
                'literal_h_uso_suelo' => $data['midas']],
        ];
        if (!empty($data['vias'])) {
            $suggestions['06'] = ['vias_detalle' => $data['vias'], 'comentario_vias_senalizacion' => $data['vias']];
            $suggestions['11'] = ['tipos_transporte_identificados' => $data['transporte'] ?? [],
                'detalle_paraderos_transporte' => (string) ($data['paraderos'] ?? '')];
            $suggestions['16']['literal_c_accesibilidad'] = $data['vias'];
        }
        if (!empty($data['equipamientos'])) {
            $suggestions['07'] = ['equipamientos_seleccionados' => $data['equipamientos'],
                'comentario_amoblamiento' => $data['amoblamiento'] ?? ''];
            $suggestions['12'] = ['categorias_edificaciones' => $data['edificaciones'] ?? [],
                'edificaciones_ancla' => $data['edificaciones_texto'] ?? ''];
        }
        if (!empty($data['externalidades'])) {
            $suggestions['13'] = ['externalidades_positivas' => ['Buena conectividad urbana',
                'Proximidad a equipamientos', 'Entorno residencial consolidado'],
                'observacion_externalidades' => $data['externalidades']];
        }
        if (!empty($data['dinamica'])) {
            $suggestions['04'] = ['descripcion_general_sector' => $data['dinamica'],
                'uso_predominante' => 'Residencial'];
            $suggestions['15'] = ['dinamica_sectorial' => $data['dinamica'],
                'fortalezas_sector' => 'Localización consolidada con soporte territorial MIDAS.',
                'condicionantes_sector' => (string) ($data['alertas'] ?? '')];
        }
        return $suggestions;
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
