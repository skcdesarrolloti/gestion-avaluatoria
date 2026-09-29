<?php
declare(strict_types=1);
namespace App\Support;
use App\Services\AppraisalNarrativeChapterInput;

final class AppraisalEconomicCatalog
{
    public static function sections(): array
    {
        return [
            self::section('6.1', 'Actividad edificadora', [
                self::select('building_activity_level', 'Nivel observado', self::levels()),
                self::text('building_activity_text', 'Texto para el entregable',
                    'Describe si predominan obra nueva, remodelaciones, ampliaciones o baja dinámica constructiva.'),
                self::text('building_activity_sources', 'Fuente o soporte', 'Visita, observación de campo, MIDAS, licencias o evidencia fotográfica.', false, 700),
            ]),
            self::section('6.2', 'Perspectivas de valorización', [
                self::select('valorization_level', 'Índice estimado', self::levels()),
                self::text('valorization_text', 'Texto para el entregable',
                    'Sustenta si la valorización esperada es baja, media o alta según dinámica urbana, accesibilidad, servicios y mercado.'),
            ]),
            self::section('6.3', 'Oferta y demanda', [
                self::select('market_dynamics', 'Lectura del mercado', ['baja' => 'Baja', 'media' => 'Media', 'alta' => 'Alta', 'sin_datos' => 'Sin datos suficientes']),
                self::text('market_text', 'Texto para el entregable',
                    'Registra si hay ofertas, negociaciones, absorción lenta, escasez o actividad comercial verificable.'),
            ]),
            self::section('6.4', 'Actividad económica de la zona', [
                self::select('predominant_activity', 'Actividad predominante', [
                    'salud' => 'Salud', 'comercio' => 'Comercio', 'residencial' => 'Residencial',
                    'institucional' => 'Institucional', 'servicios' => 'Servicios', 'mixta' => 'Mixta',
                ]),
                self::text('economic_activity_text', 'Texto para el entregable',
                    'Describe actividades visibles, anclas sectoriales y relación con el uso actual o potencial del inmueble.'),
            ]),
            self::section('6.5', 'Mercado objetivo', [
                self::text('target_market_text', 'Texto para el entregable',
                    'Define a qué tipo de comprador, usuario o actividad se orienta el inmueble por área, uso, ubicación y restricciones.'),
            ]),
        ];
    }

    public static function defaults(): array { return AppraisalNarrativeChapterInput::defaults(self::sections()); }
    private static function section(string $code, string $title, array $fields): array { return compact('code', 'title', 'fields'); }
    private static function text(string $key, string $label, string $help, bool $report = true, int $max = 2200): array
    { return ['key' => $key, 'label' => $label, 'type' => 'textarea', 'help' => $help, 'report' => $report, 'max' => $max]; }
    private static function select(string $key, string $label, array $options): array
    { return ['key' => $key, 'label' => $label, 'type' => 'select', 'options' => ['' => 'Selecciona una opción'] + $options, 'max' => 80]; }
    private static function levels(): array
    { return ['baja' => 'Baja', 'media' => 'Media', 'alta' => 'Alta', 'sin_datos' => 'Sin datos suficientes']; }
}
