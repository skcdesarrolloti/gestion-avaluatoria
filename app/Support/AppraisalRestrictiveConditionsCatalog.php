<?php
declare(strict_types=1);
namespace App\Support;
use App\Services\AppraisalNarrativeChapterInput;

final class AppraisalRestrictiveConditionsCatalog
{
    public static function sections(): array
    {
        return [
            self::section('7.1', 'Problemas de estabilidad y suelos', [
                self::select('soil_incidence', 'Incidencia observada', self::incidence()),
                self::text('soil_text', 'Texto para el entregable',
                    'Registra estabilidad aparente, necesidad de estudio geotécnico, riesgo de inundación, deslizamiento o licuación si está soportado.',
                    'Aunque el nivel de estabilidad y otras características del suelo solo se pueden determinar a través de un estudio geotécnico, la presencia de edificaciones sin problemas en su estructura indica que el suelo no presenta problemas aparentes de inestabilidad. De acuerdo con los planos de riesgos de la ciudad, se debe verificar si el predio se encuentra o no en zona inundable o susceptible a deslizamientos de tierra.'),
            ]),
            self::section('7.2', 'Impacto ambiental y salubridad', [
                self::select('environmental_incidence', 'Incidencia observada', self::incidence()),
                self::text('environmental_text', 'Texto para el entregable',
                    'Describe ruido, tráfico, residuos, aglomeraciones, salubridad o impactos ambientales verificables.',
                    'El sector donde se localiza el inmueble objeto de medición presenta una dinámica urbana que debe revisarse frente a tráfico, aglomeraciones, salubridad, ruido u otras condiciones ambientales observables. Cuando aplique, se debe dejar expresa la afectación principal y su relación con el uso del inmueble.'),
            ]),
            self::section('7.3', 'Servidumbres, cesiones y afectaciones viales', [
                self::select('easement_incidence', 'Incidencia observada', self::incidence()),
                self::text('easement_text', 'Texto para el entregable',
                    'Indica servidumbres, reservas, cesiones, proyectos viales o ausencia de afectaciones según documentos y visita.',
                    'De acuerdo con lo observado en la documentación suministrada y en la visita técnica, no se identifican servidumbres. Según la información disponible de Planeación Distrital, se debe confirmar si existen proyectos de infraestructura vial que afecten al predio o exigencias de cesiones para zonas verdes o vías locales.'),
            ]),
            self::section('7.4', 'Seguridad', [
                self::select('security_incidence', 'Incidencia observada', self::incidence()),
                self::text('security_text', 'Texto para el entregable',
                    'Registra percepción, evidencia o salvedad de seguridad sin afirmar más de lo observado o soportado.',
                    'El sector no padece importantes problemas de seguridad más allá de los que se presentan en la ciudad en general, salvo que la visita o soportes del caso indiquen una condición diferente. El factor seguridad no incide positiva ni negativamente en el valor de los inmuebles si no hay evidencia específica.'),
            ]),
            self::section('7.5', 'Problemáticas socioeconómicas', [
                self::select('socioeconomic_incidence', 'Incidencia observada', self::incidence()),
                self::text('socioeconomic_text', 'Texto para el entregable',
                    'Describe condiciones sociales o económicas que puedan afectar comercialización, uso o valor.',
                    'El sector no presenta problemas de tipo socioeconómico significativos que puedan afectar la comercialización o el valor del inmueble, salvo que se identifiquen condiciones puntuales que deban ser descritas por el analista.'),
            ]),
            self::section('7.6', 'Hipótesis especiales, inusuales o extraordinarias', [
                self::select('special_hypothesis_status', 'Estado', ['' => 'Selecciona una opción', 'no_aplica' => 'No aplica', 'aplica' => 'Aplica', 'pendiente' => 'Pendiente']),
                self::text('special_hypothesis_text', 'Texto para el entregable',
                    'Deja expresas hipótesis especiales o confirma que no se identificaron condiciones extraordinarias.',
                    'No existen grupos o asentamientos humanos cuya problemática social incida en el valor de los inmuebles del sector, salvo que el caso concreto evidencie hipótesis especiales, inusuales o extraordinarias que deban dejarse expresas.'),
            ]),
            self::section('7.7', 'Problemas jurídicos', [
                self::select('legal_problem_incidence', 'Incidencia observada', self::incidence()),
                self::text('legal_problem_text', 'Texto para el entregable',
                    'Resume si el numeral jurídico dejó afectaciones activas, salvedades o ausencia de problemas evidenciados.',
                    'No se evidenciaron problemas jurídicos. Esta conclusión debe ajustarse si el certificado, la lectura jurídica o los soportes del numeral 4 reportan afectaciones, limitaciones, medidas o salvedades activas.'),
            ]),
        ];
    }

    public static function defaults(): array { return AppraisalNarrativeChapterInput::defaults(self::sections()); }
    private static function incidence(): array
    {
        return ['' => 'Selecciona una opción', 'sin_evidencia' => 'Sin evidencia', 'no_incide' => 'No incide',
            'incidencia_leve' => 'Incidencia leve', 'incidencia_relevante' => 'Incidencia relevante', 'pendiente' => 'Pendiente de soporte'];
    }
    private static function section(string $code, string $title, array $fields): array { return compact('code', 'title', 'fields'); }
    private static function text(string $key, string $label, string $help, string $prefill = '', int $max = 2200): array
    { return ['key' => $key, 'label' => $label, 'type' => 'textarea', 'help' => $help, 'prefill' => $prefill, 'max' => $max]; }
    private static function select(string $key, string $label, array $options): array
    { return ['key' => $key, 'label' => $label, 'type' => 'select', 'options' => $options, 'max' => 80]; }
}
