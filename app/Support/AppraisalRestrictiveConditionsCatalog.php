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
                    'Registra estabilidad aparente, necesidad de estudio geotécnico, riesgo de inundación, deslizamiento o licuación si está soportado.'),
            ]),
            self::section('7.2', 'Impacto ambiental y salubridad', [
                self::select('environmental_incidence', 'Incidencia observada', self::incidence()),
                self::text('environmental_text', 'Texto para el entregable',
                    'Describe ruido, tráfico, residuos, aglomeraciones, salubridad o impactos ambientales verificables.'),
            ]),
            self::section('7.3', 'Servidumbres, cesiones y afectaciones viales', [
                self::select('easement_incidence', 'Incidencia observada', self::incidence()),
                self::text('easement_text', 'Texto para el entregable',
                    'Indica servidumbres, reservas, cesiones, proyectos viales o ausencia de afectaciones según documentos y visita.'),
            ]),
            self::section('7.4', 'Seguridad', [
                self::select('security_incidence', 'Incidencia observada', self::incidence()),
                self::text('security_text', 'Texto para el entregable',
                    'Registra percepción, evidencia o salvedad de seguridad sin afirmar más de lo observado o soportado.'),
            ]),
            self::section('7.5', 'Problemáticas socioeconómicas', [
                self::select('socioeconomic_incidence', 'Incidencia observada', self::incidence()),
                self::text('socioeconomic_text', 'Texto para el entregable',
                    'Describe condiciones sociales o económicas que puedan afectar comercialización, uso o valor.'),
            ]),
            self::section('7.6', 'Hipótesis especiales, inusuales o extraordinarias', [
                self::select('special_hypothesis_status', 'Estado', ['' => 'Selecciona una opción', 'no_aplica' => 'No aplica', 'aplica' => 'Aplica', 'pendiente' => 'Pendiente']),
                self::text('special_hypothesis_text', 'Texto para el entregable',
                    'Deja expresas hipótesis especiales o confirma que no se identificaron condiciones extraordinarias.'),
            ]),
            self::section('7.7', 'Problemas jurídicos', [
                self::select('legal_problem_incidence', 'Incidencia observada', self::incidence()),
                self::text('legal_problem_text', 'Texto para el entregable',
                    'Resume si el numeral jurídico dejó afectaciones activas, salvedades o ausencia de problemas evidenciados.'),
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
    private static function text(string $key, string $label, string $help, int $max = 2200): array
    { return ['key' => $key, 'label' => $label, 'type' => 'textarea', 'help' => $help, 'max' => $max]; }
    private static function select(string $key, string $label, array $options): array
    { return ['key' => $key, 'label' => $label, 'type' => 'select', 'options' => $options, 'max' => 80]; }
}
