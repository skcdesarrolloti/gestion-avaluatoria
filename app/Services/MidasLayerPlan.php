<?php
declare(strict_types=1);
namespace App\Services;

use App\Support\AppraisalSectorAdvancedCatalog;

final class MidasLayerPlan
{
    public static function components(): array
    {
        return [
            'territorio' => [
                'label' => 'Territorio, barrio y POT',
                'layers' => ['Barrios', 'Sectores', 'Clasificacion', 'Modelo', 'Tratamiento',
                    'Uso de Suelo - Documento Técnico de Soporte'],
                'fields' => [
                    '01' => [
                        'localidad' => 'Pendiente leer capa Barrios de MIDAS para localidad.',
                        'comuna' => 'Pendiente leer capa Barrios de MIDAS para UCG/comuna.',
                        'fuente_base_delimitacion' => 'Pendiente registrar capa territorial usada como fuente.',
                        'area_hectareas' => 'Pendiente leer área del polígono barrial en MIDAS.',
                        'perimetro_metros' => 'Pendiente leer perímetro del polígono barrial en MIDAS.',
                        'norte' => 'Pendiente confirmar lindero norte con mapa o visita.',
                        'sur' => 'Pendiente confirmar lindero sur con mapa o visita.',
                        'este' => 'Pendiente confirmar lindero este con mapa o visita.',
                        'oeste' => 'Pendiente confirmar lindero oeste con mapa o visita.',
                    ],
                    '05' => [
                        'clasificacion_suelo' => 'Pendiente leer clasificación del suelo en MIDAS/POT.',
                        'norma_base' => 'Pendiente leer norma base POT o acto aplicable.',
                        'fuente_normativa' => 'Pendiente registrar fuente normativa MIDAS/POT.',
                        'midas_lectura_manual' => 'Pendiente consolidar lectura normativa de MIDAS.',
                    ],
                ],
            ],
            'servicios' => [
                'label' => 'Servicios públicos',
                'layers' => ['Área de Prestación Servicio de Acueducto', 'Área de Prestación de Servicio de Alcantarillado',
                    'Área Prestación Servicio Gas', 'Cobertura de Alumbrado Público',
                    'Recolección Residuos Sólidos', 'Manzanas DANE - Servicio de Recolección de Basuras'],
                'fields' => [
                    '03' => [
                        'fuente_servicios' => 'Pendiente registrar capas MIDAS de servicios consultadas.',
                        'acueducto_detalle' => 'Pendiente leer cobertura o prestador de acueducto.',
                        'alcantarillado_detalle' => 'Pendiente leer cobertura o prestador de alcantarillado.',
                        'energia_detalle' => 'Pendiente leer cobertura eléctrica o alumbrado público.',
                        'gas_detalle' => 'Pendiente leer área de prestación o cobertura de gas.',
                        'aseo_prestadores' => 'Pendiente leer prestador de aseo del sector.',
                        'aseo_detalle' => 'Pendiente leer recolección de residuos y soporte de aseo.',
                        'aguas_lluvias_detalle' => 'Pendiente leer drenaje pluvial, estaciones o redes asociadas.',
                    ],
                    '16' => [
                        'literal_g_servicios' => 'Pendiente redactar disponibilidad de servicios con soporte MIDAS.',
                    ],
                ],
            ],
            'movilidad' => [
                'label' => 'Movilidad y transporte',
                'layers' => ['Sistema vial', 'Rutas', 'Paraderos', 'TRANSPORTE MASIVO'],
                'fields' => [
                    '06' => [
                        'via_principal' => 'Pendiente identificar vía principal desde MIDAS o visita.',
                        'vias_detalle' => 'Pendiente caracterizar vías, accesos y señalización.',
                    ],
                    '11' => [
                        'servicio_transporte_predominante' => 'Pendiente definir servicio de transporte predominante.',
                        'tipos_transporte_identificados' => 'Pendiente leer tipos de transporte disponibles.',
                        'detalle_rutas_transporte' => 'Pendiente leer rutas que sirven al barrio.',
                        'detalle_paraderos_transporte' => 'Pendiente leer paraderos asociados al barrio.',
                    ],
                    '16' => [
                        'literal_c_accesibilidad' => 'Pendiente redactar accesibilidad con rutas, paraderos y vías.',
                    ],
                ],
            ],
            'equipamientos' => [
                'label' => 'Equipamientos y amoblamiento',
                'layers' => ['Instituciones de Educación Básica y Media', 'Escenarios Deportivos del Distrito Cartagena de Indias',
                    'Prestadores de Servicios de Salud', 'Comando de Atención Inmediata CAI',
                    'Bibliotecas y centros culturales', 'Espacio Público y Zonas Verdes'],
                'fields' => [
                    '07' => [
                        'amoblamiento_seleccionado' => 'Pendiente leer parques, zonas verdes, mobiliario o espacio público.',
                        'equipamientos_seleccionados' => 'Pendiente clasificar equipamientos encontrados en MIDAS.',
                        'comentario_amoblamiento' => 'Pendiente consolidar equipamientos y amoblamiento del sector.',
                    ],
                    '12' => [
                        'categorias_edificaciones' => 'Pendiente clasificar hitos por educación, salud, deporte, cultura u otros.',
                        'edificaciones_ancla' => 'Pendiente listar equipamientos o hitos principales del barrio.',
                    ],
                    '13' => [
                        'externalidades_positivas' => 'Pendiente valorar externalidades positivas por equipamientos cercanos.',
                    ],
                ],
            ],
            'seguridad_ambiente' => [
                'label' => 'Seguridad, ambiente y riesgos',
                'layers' => ['Cuadrantes de la policia', 'Sistema de video vigilancia de la Policia',
                    'Amenazas Naturales', 'Riesgo', 'Proteccion', 'Inundación pluvial',
                    'Erosión costera', 'Determinantes Ambientales'],
                'fields' => [
                    '10' => [
                        'observacion_topografia' => 'Pendiente revisar condiciones físicas o ambientales del entorno.',
                        'comentario_topografia' => 'Pendiente sintetizar topografía o condición física relevante.',
                    ],
                    '13' => [
                        'externalidades_negativas' => 'Pendiente valorar riesgos, amenazas o impactos negativos.',
                        'observacion_externalidades' => 'Pendiente registrar riesgos, seguridad o ambiente leídos en MIDAS.',
                        'comentario_externalidades' => 'Pendiente redactar síntesis de externalidades.',
                    ],
                    '16' => [
                        'literal_e_amenazas' => 'Pendiente redactar amenazas o afectaciones verificadas.',
                    ],
                ],
            ],
        ];
    }

    public static function pendingReasons(): array
    {
        $pending = [];
        foreach (self::components() as $component) {
            foreach ($component['fields'] as $code => $fields) {
                foreach ($fields as $field => $reason) {
                    $pending[$code][$field] ??= $reason;
                }
            }
        }
        return $pending;
    }

    public static function missingModuleFields(): array
    {
        $valid = [];
        foreach (AppraisalSectorAdvancedCatalog::sections() as $code => [, $fields]) {
            foreach ($fields as $field) $valid[$code][$field[0]] = true;
        }
        $missing = [];
        foreach (self::pendingReasons() as $code => $fields) {
            foreach (array_keys($fields) as $field) {
                if (!isset($valid[$code][$field])) $missing[] = $code . '.' . $field;
            }
        }
        return $missing;
    }
}
