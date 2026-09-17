<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalSectorAdvancedPrefill
{
    public static function sections(array $subject, array $sector): array
    {
        $place = self::join([$subject['neighborhood_name'] ?? '', $subject['locality_name'] ?? '',
            $subject['commune_ucg'] ?? '', $subject['city_name'] ?? '']);
        $source = self::sourceText((string) ($sector['sector_source'] ?? ''), $place);
        $map = self::pick($sector['sector_map_url'] ?? '', self::mapUrl($subject));
        $location = self::locationText($place, $sector);
        $city = self::pick($subject['city_name'] ?? '', $sector['sector_city'] ?? '', 'Cartagena de Indias');
        $department = self::pick($subject['department_name'] ?? '', $sector['sector_department'] ?? '', 'Bolívar');
        $neighborhood = self::pick($subject['neighborhood_name'] ?? '', $sector['sector_neighborhood'] ?? '');
        return [
            '01' => ['pais' => self::pick($sector['sector_country'] ?? '', 'Colombia'),
                'departamento' => $department, 'municipio_distrito' => $city, 'barrio' => $neighborhood,
                'localidad' => self::pick($subject['locality_name'] ?? '', $sector['sector_locality'] ?? ''),
                'comuna' => self::pick($subject['commune_ucg'] ?? '', $sector['sector_commune'] ?? ''),
                'microsector' => self::pick($sector['sector_microsector'] ?? '', $subject['zone_sector'] ?? ''),
                'mapa_barrio_url' => $map, 'fuente_base_delimitacion' => $source,
                'fuente_base_satelital' => self::pick($sector['satellite_source'] ?? '', $map),
                'latitud_centro' => self::pick($sector['sector_latitude'] ?? '', $subject['latitude'] ?? ''),
                'longitud_centro' => self::pick($sector['sector_longitude'] ?? '', $subject['longitude'] ?? ''),
                'area_hectareas' => (string) ($sector['sector_area_ha'] ?? ''),
                'perimetro_metros' => (string) ($sector['sector_perimeter_m'] ?? ''),
                'norte' => (string) ($sector['sector_north_boundary'] ?? ''),
                'sur' => (string) ($sector['sector_south_boundary'] ?? ''),
                'este' => (string) ($sector['sector_east_boundary'] ?? ''),
                'oeste' => (string) ($sector['sector_west_boundary'] ?? ''),
                'observacion_localizacion' => $location],
            '02' => ['mapa_delimitacion_url' => $map, 'imagen_satelital_url' => $map,
                'medicion_source' => $source, 'cartografia_status' => $map ? 'MANUAL' : 'PENDIENTE'],
            '03' => ['fuente_servicios' => $source, 'acueducto' => self::yesNo($subject['water_service'] ?? ''),
                'acueducto_detalle' => self::serviceDetail('acueducto', $city),
                'alcantarillado' => self::yesNo($subject['sewer_service'] ?? ''),
                'alcantarillado_detalle' => self::serviceDetail('alcantarillado', $city),
                'energia' => self::yesNo($subject['energy_service'] ?? ''),
                'energia_detalle' => self::serviceDetail('energia', $city),
                'gas' => self::yesNo($subject['gas_service'] ?? ''),
                'gas_detalle' => self::serviceDetail('gas', $city),
                'internet_operadores' => self::internet($subject['internet_service'] ?? ''),
                'aseo_prestadores' => ['No verificado'],
                'aguas_lluvias_detalle' => (string) ($sector['infrastructure_notes'] ?? ''),
                'aseo_detalle' => self::serviceDetail('aseo', $city)],
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

    private static function sourceText(string $value, string $place): string
    {
        $text = trim($value);
        if ($text !== '' && !str_starts_with($text, 'Precarga desde')) return $text;
        return $place === '' ? '' : 'Fuente interna inicial: Bien sujeto y maestro de barrios SuCasa. '
            . 'Confirmar con cartografía oficial, geoportal, POT/MIDAS o visita de campo.';
    }

    private static function locationText(string $place, array $sector): string
    {
        $stored = self::join([$sector['influence_area'] ?? '', $sector['sector_boundaries'] ?? ''], ' ');
        if ($stored !== '' && !str_contains($stored, 'Validar alcance real')) return $stored;
        return $place === '' ? '' : 'El sector de influencia se toma inicialmente como ' . $place
            . '.';
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

    private static function serviceDetail(string $service, string $city): string
    {
        if (!str_contains(mb_strtolower($city), 'cartagena')) return '';
        return [
            'acueducto' => 'Aguas de Cartagena S.A. E.S.P. - Acuacar. Confirmar con recibo, empresa o visita.',
            'alcantarillado' => 'Aguas de Cartagena S.A. E.S.P. - Acuacar. Confirmar cobertura puntual.',
            'energia' => 'Afinia - Grupo EPM. Confirmar disponibilidad y continuidad en campo.',
            'gas' => 'Surtigas S.A. E.S.P. Confirmar acometida o cobertura en el inmueble.',
            'aseo' => 'Pacaribe o Veolia según microrruta. Consultar frecuencia por barrio y validar en visita.',
        ][$service] ?? '';
    }
}
