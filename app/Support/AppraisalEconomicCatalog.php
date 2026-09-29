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
                    'Describe si predominan obra nueva, remodelaciones, ampliaciones o baja dinámica constructiva.',
                    true, 2200, 'En la zona donde se encuentra el inmueble, no existen proyectos constructivos representativos; predominan la remodelación y ampliaciones de las edificaciones existentes. En general, la actividad edificadora es baja.'),
                self::text('building_activity_sources', 'Fuente o soporte', 'Visita, observación de campo, MIDAS, licencias o evidencia fotográfica.', false, 700),
            ]),
            self::section('6.2', 'Perspectivas de valorización', [
                self::select('valorization_level', 'Índice estimado', self::levels()),
                self::text('valorization_text', 'Texto para el entregable',
                    'Sustenta si la valorización esperada es baja, media o alta según dinámica urbana, accesibilidad, servicios y mercado.',
                    true, 2200, 'En el sector, estimamos un índice de valorización medio.'),
            ]),
            self::section('6.3', 'Oferta y demanda', [
                self::select('market_dynamics', 'Lectura del mercado', ['baja' => 'Baja', 'media' => 'Media', 'alta' => 'Alta', 'sin_datos' => 'Sin datos suficientes']),
                self::text('market_text', 'Texto para el entregable',
                    'Registra si hay ofertas, negociaciones, absorción lenta, escasez o actividad comercial verificable.',
                    true, 2200, 'La oferta y demanda de este tipo de inmuebles se puede considerar baja: se observan ofertas en el mercado, pero no se evidencian negociaciones suficientes que indiquen una dinámica alta de absorción.'),
            ]),
            self::section('6.4', 'Actividad económica de la zona', [
                self::select('predominant_activity', 'Actividad principal del sector', self::activities()),
                self::select('secondary_activity', 'Actividad secundaria o corredor relevante', self::activities()),
                self::select('tertiary_activity', 'Actividad complementaria', self::activities()),
                self::text('activity_corridor_text', 'Corredores, calles o focos puntuales',
                    'Aclara excepciones internas del barrio: ejes comerciales, clínicas, universidades, institucionales o servicios concentrados.',
                    true, 1200, 'Aunque el sector tenga una vocación predominante, pueden existir corredores o calles con una dinámica distinta que deben describirse de forma puntual, por ejemplo un eje comercial dentro de un barrio residencial.'),
                self::text('economic_activity_text', 'Texto para el entregable',
                    'Describe actividades visibles, anclas sectoriales y relación con el uso actual o potencial del inmueble.',
                    true, 2200, 'En el sector donde se ubica el predio objeto del avalúo, y tal como se mencionó anteriormente, predominan los negocios pertenecientes al sector salud, sin perjuicio de otros usos o actividades complementarias que deban verificarse en campo.'),
                self::text('economic_midas_support', 'Soporte MIDAS útil para revisar',
                    'Usa las descargas realmente disponibles: Educación sustenta equipamientos institucionales públicos y privados; Localidades y UCG ubican el sector; Cambio climático alimenta más el numeral 7.',
                    false, 900, 'Revisar Biblioteca MIDAS: Educación debe tenerse en cuenta para identificar colegios oficiales y privados como equipamientos institucionales del sector. Esto puede sustentar actividad institucional o educativa secundaria/complementaria, pero la intensidad económica se confirma con visita, mercado y soportes del caso.'),
            ]),
            self::section('6.5', 'Mercado objetivo', [
                self::text('target_market_text', 'Texto para el entregable',
                    'Define a qué tipo de comprador, usuario o actividad se orienta el inmueble por área, uso, ubicación y restricciones.',
                    true, 2200, 'Las características del inmueble objeto del estudio, especialmente por su área, hacen que el mercado objetivo esté orientado principalmente al sector salud.'),
            ]),
        ];
    }

    public static function defaults(): array { return AppraisalNarrativeChapterInput::defaults(self::sections()); }
    private static function section(string $code, string $title, array $fields): array { return compact('code', 'title', 'fields'); }
    private static function text(string $key, string $label, string $help, bool $report = true, int $max = 2200, string $prefill = ''): array
    { return ['key' => $key, 'label' => $label, 'type' => 'textarea', 'help' => $help, 'report' => $report, 'max' => $max, 'prefill' => $prefill]; }
    private static function select(string $key, string $label, array $options): array
    { return ['key' => $key, 'label' => $label, 'type' => 'select', 'options' => ['' => 'Selecciona una opción'] + $options, 'max' => 80]; }
    private static function levels(): array
    { return ['baja' => 'Baja', 'media' => 'Media', 'alta' => 'Alta', 'sin_datos' => 'Sin datos suficientes']; }
    private static function activities(): array
    {
        return [
            'salud' => 'Salud',
            'comercio' => 'Comercio',
            'residencial' => 'Residencial',
            'institucional' => 'Institucional',
            'institucional_3' => 'Institucional de tercer nivel',
            'educacion' => 'Educación',
            'servicios' => 'Servicios',
            'turistica' => 'Turística',
            'industrial' => 'Industrial',
            'mixta' => 'Mixta',
            'sin_datos' => 'Sin datos suficientes',
        ];
    }
}
