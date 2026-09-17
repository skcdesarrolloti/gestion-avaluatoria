<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalSectorAdvancedPrefill
{
    public static function sections(array $subject, array $sector): array
    {
        $place = self::join([$subject['neighborhood_name'] ?? '', $subject['locality_name'] ?? '',
            $subject['commune_ucg'] ?? '', $subject['city_name'] ?? '']);
        $source = self::pick($sector['sector_source'] ?? '', $place ? 'Bien sujeto y maestro barrial.' : '');
        $map = self::pick($sector['sector_map_url'] ?? '', self::mapUrl($subject));
        $location = self::join([$sector['influence_area'] ?? '', $sector['sector_boundaries'] ?? ''], ' ');
        return [
            '01' => ['microsector' => self::pick($sector['sector_microsector'] ?? '', $subject['zone_sector'] ?? ''),
                'fuente_base_delimitacion' => $source, 'fuente_base_satelital' => $map,
                'observacion_localizacion' => $location],
            '02' => ['mapa_delimitacion_url' => $map, 'imagen_satelital_url' => $map,
                'medicion_source' => $source, 'cartografia_status' => $map ? 'MANUAL' : 'PENDIENTE'],
            '03' => ['fuente_servicios' => $source, 'acueducto' => self::yesNo($subject['water_service'] ?? ''),
                'alcantarillado' => self::yesNo($subject['sewer_service'] ?? ''),
                'energia' => self::yesNo($subject['energy_service'] ?? ''),
                'gas' => self::yesNo($subject['gas_service'] ?? ''),
                'internet_operadores' => self::internet($subject['internet_service'] ?? ''),
                'aguas_lluvias_detalle' => (string) ($sector['infrastructure_notes'] ?? '')],
            '04' => ['descripcion_general_sector' => (string) ($sector['daily_dynamics'] ?? ''),
                'uso_predominante' => self::titleOption($sector['predominant_use'] ?? ''),
                'actividad_economica_predominante' => self::titleOption($sector['predominant_use'] ?? '')],
            '05' => ['norma_base' => (string) ($sector['urban_norm'] ?? ''),
                'fuente_normativa' => $source, 'midas_lectura_manual' => (string) ($sector['urban_treatment'] ?? '')],
            '06' => ['via_principal' => (string) ($sector['road_hierarchy'] ?? ''),
                'vias_detalle' => (string) ($sector['access_roads'] ?? ''),
                'comentario_vias_senalizacion' => (string) ($sector['mobility_notes'] ?? '')],
            '08' => ['estrato_predominante' => (string) ($subject['stratum'] ?? ''),
                'comentario_estratificacion' => (string) ($sector['socioeconomic_profile'] ?? '')],
            '11' => ['detalle_rutas_transporte' => (string) ($sector['public_transport'] ?? ''),
                'comentario_transporte' => (string) ($sector['mobility_notes'] ?? '')],
            '13' => ['observacion_externalidades' => self::join([$sector['positive_externalities'] ?? '',
                $sector['negative_externalities'] ?? '', $sector['sector_risks'] ?? ''], ' ')],
            '14' => ['soportes_fotograficos_plan' => (string) ($sector['field_sources'] ?? '')],
            '15' => ['dinamica_sectorial' => (string) ($sector['consolidation_level'] ?? ''),
                'comentario_conclusion_sectorial' => (string) ($sector['sector_conclusion'] ?? '')],
            '16' => ['literal_a_localizacion' => $location,
                'literal_b_vecindario' => (string) ($sector['sector_report_text'] ?? ''),
                'literal_c_accesibilidad' => (string) ($sector['mobility_notes'] ?? ''),
                'literal_g_servicios' => (string) ($sector['infrastructure_notes'] ?? ''),
                'literal_h_uso_suelo' => (string) ($sector['predominant_use'] ?? '')],
        ];
    }

    public static function merge(array $defaults, array $stored): array
    {
        return array_replace($defaults, array_filter($stored,
            static fn (mixed $value): bool => is_array($value) ? $value !== [] : trim((string) $value) !== ''));
    }

    private static function pick(mixed ...$values): string
    {
        foreach ($values as $value) {
            $text = trim((string) $value);
            if ($text !== '') return $text;
        }
        return '';
    }

    private static function join(array $parts, string $separator = ', '): string
    {
        return implode($separator, array_values(array_filter(array_map('trim', $parts))));
    }

    private static function mapUrl(array $subject): string
    {
        $place = self::join([$subject['neighborhood_name'] ?? '', $subject['city_name'] ?? 'Cartagena de Indias',
            $subject['department_name'] ?? 'Bolívar', 'Colombia']);
        return $place ? 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($place) : '';
    }

    private static function yesNo(mixed $value): string
    {
        $key = mb_strtolower(trim((string) $value));
        return ['si' => 'SI', 'no' => 'NO', 'no_verificado' => 'PENDIENTE', '' => ''][$key] ?? 'PENDIENTE';
    }

    private static function internet(mixed $value): array
    {
        return (string) $value === 'si' ? ['No verificado'] : [];
    }

    private static function titleOption(mixed $value): string
    {
        $text = trim((string) $value);
        return $text === '' ? '' : mb_convert_case($text, MB_CASE_TITLE, 'UTF-8');
    }
}
