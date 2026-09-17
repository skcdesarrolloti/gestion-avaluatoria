<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalMidasReview
{
    public static function suggestions(array $subject): array
    {
        $name = mb_strtoupper(trim((string) ($subject['neighborhood_name'] ?? '')));
        return str_contains($name, 'CRESPO') ? self::crespo($subject) : self::generic($subject);
    }

    public static function mergeEmpty(array $current, array $suggestions): array
    {
        foreach ($suggestions as $code => $fields) {
            foreach ($fields as $field => $value) {
                $actual = $current[$code][$field] ?? '';
                if ((is_array($actual) && $actual !== []) || (!is_array($actual) && trim((string) $actual) !== '')) {
                    continue;
                }
                $current[$code][$field] = $value;
            }
        }
        return $current;
    }

    public static function rows(array $current, array $suggestions): array
    {
        $rows = [];
        foreach ($suggestions as $code => $fields) {
            foreach ($fields as $field => $value) {
                $actual = $current[$code][$field] ?? '';
                $rows[] = ['code' => $code, 'field' => $field, 'value' => $value,
                    'mode' => trim(is_array($actual) ? implode(', ', $actual) : (string) $actual) === ''
                        ? 'Aplicable' : 'Conserva manual'];
            }
        }
        return $rows;
    }

    public static function stats(array $suggestions): array
    {
        return ['sections' => count($suggestions),
            'fields' => array_sum(array_map('count', $suggestions))];
    }

    private static function generic(array $subject): array
    {
        $barrio = trim((string) ($subject['neighborhood_name'] ?? 'el barrio'));
        return ['03' => ['fuente_servicios' => 'MIDAS Cartagena y empresas prestadoras: validar cobertura puntual.',
            'acueducto_detalle' => 'Aguas de Cartagena S.A. E.S.P. - Acuacar. Confirmar con recibo, empresa o visita.',
            'alcantarillado_detalle' => 'Aguas de Cartagena S.A. E.S.P. - Acuacar. Confirmar cobertura puntual.',
            'energia_detalle' => 'Afinia - Grupo EPM. Confirmar disponibilidad y continuidad en campo.',
            'gas_detalle' => 'Surtigas S.A. E.S.P. Confirmar acometida o cobertura en el inmueble.',
            'aseo_detalle' => 'Consultar empresa, frecuencia y microrruta de aseo por barrio; validar con prestador y visita.'],
            '05' => ['fuente_normativa' => 'MIDAS Cartagena - consulta pendiente de detalle.',
            'midas_lectura_manual' => 'Abrir MIDAS, buscar ' . ($barrio ?: 'el barrio')
                . ' y registrar tratamiento, usos, restricciones, áreas e indicadores disponibles.'],
            '14' => ['anexos_normativos' => 'Adjuntar captura o enlace de MIDAS usado como soporte.',
                'observacion_soporte_grafico' => 'La consulta automática queda preparada; requiere lectura puntual en MIDAS.']];
    }

    private static function crespo(array $subject): array
    {
        $place = 'Crespo, Histórica y del Caribe Norte, UCG 1, Cartagena de Indias';
        return [
            '01' => [
                'pais' => 'Colombia',
                'departamento' => 'Bolívar',
                'municipio_distrito' => 'Cartagena de Indias',
                'barrio' => 'Crespo',
                'localidad' => 'Histórica y del Caribe Norte',
                'comuna' => 'UCG 1',
                'microsector' => (string) ($subject['zone_sector'] ?? 'Residencial y servicios aeroportuarios'),
                'mapa_barrio_url' => 'https://midas.cartagena.gov.co/#/home',
                'fuente_base_delimitacion' => 'MIDAS Cartagena: Territorios - Barrio Crespo.',
                'fuente_base_satelital' => 'MIDAS Cartagena / Google Maps como apoyo visual.',
                'area_hectareas' => '141,70',
                'perimetro_metros' => '9.535,82',
                'norte' => 'Avenida Santander.',
                'sur' => 'Calle 70 y sectores de Cabrero y Marbella.',
                'este' => 'Ciénaga de la Virgen.',
                'oeste' => 'Mar Caribe.',
                'observacion_localizacion' => $place . '. MIDAS reporta área 141,70 ha, perímetro 9.535,82 m, 5.021 personas, 2.182 viviendas y 1.754 hogares según DANE 2018.',
            ],
            '02' => ['medicion_source' => 'MIDAS Cartagena / Secretaría de Planeación 2025.',
                'cartografia_status' => 'AUTOMATICO'],
            '03' => ['fuente_servicios' => 'MIDAS Cartagena: servicios públicos, alumbrado y estaciones de bombeo.',
                'acueducto' => 'SI',
                'acueducto_detalle' => 'Aguas de Cartagena S.A. E.S.P. - Acuacar. Confirmar cobertura puntual con recibo o visita.',
                'alcantarillado' => 'SI',
                'alcantarillado_detalle' => 'Aguas de Cartagena S.A. E.S.P. - Acuacar. Confirmar cobertura puntual.',
                'energia' => 'SI',
                'energia_detalle' => 'Afinia - Grupo EPM. Confirmar continuidad y disponibilidad en campo.',
                'gas' => 'SI',
                'gas_detalle' => 'Surtigas S.A. E.S.P. Confirmar acometida o cobertura en el inmueble.',
                'aseo_prestadores' => ['Pacaribe', 'Veolia', 'No verificado'],
                'aguas_lluvias_detalle' => 'MIDAS identifica estación de bombeo y modernización de alumbrado en resultados asociados al barrio. Validar cobertura puntual en campo.',
                'aseo_detalle' => 'La recolección domiciliaria debe verificarse por barrio y horario en el portal del prestador; registrar empresa, frecuencia y evidencia de consulta.'],
            '05' => ['norma_base' => 'Decreto 0977 de 2001 (POT) - Acuerdo 006 de 2003.',
                'fuente_normativa' => 'MIDAS Cartagena / Secretaría de Planeación Distrital.',
                'midas_lectura_manual' => 'Resultado Territorios: Barrio Crespo, categoría barrio, UCG 1, Localidad Histórica y del Caribe Norte, fuente POT/Acuerdo 006 de 2003.'],
            '06' => ['vias_detalle' => 'MIDAS reporta 2 rutas y 47 paraderos asociados a Crespo; validar jerarquía vial, señalización y accesos en visita.',
                'comentario_vias_senalizacion' => 'Sector con conectividad urbana soportada por rutas y paraderos identificados en MIDAS; la lectura final depende de visita y accesos reales del inmueble.'],
            '07' => ['equipamientos_seleccionados' => ['Educativo', 'Recreativo', 'Deportivo', 'Institucional'],
                'comentario_amoblamiento' => 'MIDAS muestra puestos de votación, una institución educativa y escenarios deportivos en resultados del sector.'],
            '08' => ['comentario_estratificacion' => 'Complementar con consulta oficial de estratificación; MIDAS aporta base poblacional y territorial del barrio.'],
            '11' => ['servicio_transporte_predominante' => 'Transporte público colectivo',
                'tipos_transporte_identificados' => ['Bus urbano', 'Taxi', 'Peatonal'],
                'detalle_paraderos_transporte' => 'MIDAS reporta 47 paraderos asociados a Crespo.'],
            '12' => ['categorias_edificaciones' => ['Educación', 'Recreativo / deportivo', 'Institucional'],
                'edificaciones_ancla' => 'Institución educativa, escenarios deportivos, puestos de votación y equipamientos públicos identificables en MIDAS.'],
            '13' => ['externalidades_positivas' => ['Buena conectividad urbana', 'Proximidad a equipamientos', 'Entorno residencial consolidado'],
                'observacion_externalidades' => 'MIDAS registra CAI, cuadrantes, videovigilancia, zonas verdes e indicadores de cobertura vegetal asociados al barrio.'],
            '15' => ['dinamica_sectorial' => 'Residencial consolidada con servicios aeroportuarios y equipamientos urbanos.',
                'fortalezas_sector' => 'Localización consolidada, conectividad, equipamientos, seguridad institucional y soporte territorial MIDAS.',
                'condicionantes_sector' => 'Validar ruido aeroportuario, tráfico, accesos específicos, restricciones normativas y condiciones ambientales en campo.'],
        ];
    }
}
