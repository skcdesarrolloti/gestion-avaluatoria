<?php
declare(strict_types=1);
namespace App\Services;
use App\Support\AppraisalSubjectCatalog;

final class AppraisalSectorPrefill
{
    public static function fromSubject(array $subject): array
    {
        $labels = self::subjectLabels();
        $place = self::join([$subject['neighborhood_name'] ?? '', $subject['locality_name'] ?? '',
            $subject['commune_ucg'] ?? '', $subject['city_name'] ?? '']);
        return array_filter([
            'sector_country' => 'Colombia',
            'sector_department' => (string) ($subject['department_name'] ?? ''),
            'sector_city' => (string) ($subject['city_name'] ?? ''),
            'sector_neighborhood' => (string) ($subject['neighborhood_name'] ?? ''),
            'sector_locality' => (string) ($subject['locality_name'] ?? ''),
            'sector_commune' => (string) ($subject['commune_ucg'] ?? ''),
            'sector_name' => self::join([$subject['neighborhood_name'] ?? '', $subject['zone_sector'] ?? ''], ' - '),
            'sector_microsector' => (string) ($subject['zone_sector'] ?? ''),
            'sector_map_url' => self::mapUrl($subject),
            'sector_latitude' => (string) ($subject['latitude'] ?? ''),
            'sector_longitude' => (string) ($subject['longitude'] ?? ''),
            'influence_area' => $place ? 'Área de influencia inicial asociada a ' . $place . '. Validar alcance real en visita y soporte sectorial.' : '',
            'sector_boundaries' => ($subject['neighborhood_name'] ?? '') !== '' ? 'Delimitación preliminar tomada del barrio o microsector seleccionado en Bien sujeto.' : '',
            'sector_source' => $place ? 'Precarga desde Bien sujeto y maestro de barrios/microsectores.' : '',
            'services_status' => self::servicesStatus($subject),
            'infrastructure_notes' => self::serviceText($subject, $labels),
            'road_hierarchy' => self::roadHierarchy((string) ($subject['road_condition'] ?? '')),
            'predominant_use' => self::sectorUse((string) ($subject['current_use'] ?? '')),
            'urban_norm' => self::normText($subject, $labels),
            'urban_treatment' => self::label($labels, 'urban_treatment', $subject['urban_treatment'] ?? ''),
            'access_roads' => self::mobilityText($subject, $labels),
            'public_transport' => self::transport((string) ($subject['transport_connectivity'] ?? '')),
            'connectivity' => self::connectivity((string) ($subject['transport_connectivity'] ?? '')),
            'mobility_notes' => self::mobilityText($subject, $labels),
            'commercial_activity' => self::commercialActivity((string) ($subject['current_use'] ?? '')),
            'consolidation_level' => self::consolidation((string) ($subject['centrality'] ?? '')),
            'socioeconomic_profile' => self::stratum((string) ($subject['stratum'] ?? '')),
            'field_sources' => $place ? 'Bien sujeto, maestro geográfico interno y validación pendiente de campo.' : '',
            'sector_conclusion' => $place ? 'Lectura sectorial preliminar para revisión del analista.' : '',
            'sector_report_text' => $place ? 'El inmueble se localiza en ' . $place . ', sector que debe verificarse en campo para confirmar usos, infraestructura, movilidad, externalidades y dinámica de mercado.' : '',
        ], static fn (string $value): bool => $value !== '');
    }

    private static function subjectLabels(): array
    {
        return array_map(static fn (array $field): array => $field[1], AppraisalSubjectCatalog::selects());
    }

    private static function label(array $labels, string $key, mixed $value): string
    {
        return (string) ($labels[$key][(string) $value] ?? '');
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

    private static function servicesStatus(array $subject): string
    {
        $values = array_map(static fn (string $key): string => (string) ($subject[$key] ?? ''),
            ['water_service', 'energy_service', 'sewer_service', 'internet_service']);
        if (count(array_filter($values, static fn (string $value): bool => $value === 'si')) >= 3) return 'completa';
        if (in_array('si', $values, true)) return 'parcial';
        return in_array('no', $values, true) ? 'deficiente' : 'no_verificado';
    }

    private static function serviceText(array $subject, array $labels): string
    {
        $items = [];
        foreach (['water_service', 'energy_service', 'gas_service', 'sewer_service',
            'internet_service', 'service_continuity'] as $key) {
            $text = self::label($labels, $key, $subject[$key] ?? '');
            if ($text !== '') $items[] = $text;
        }
        return $items ? 'Lectura preliminar de servicios: ' . implode(', ', $items) . '.' : '';
    }

    private static function normText(array $subject, array $labels): string
    {
        $use = self::label($labels, 'permitted_use', $subject['permitted_use'] ?? '');
        $treatment = self::label($labels, 'urban_treatment', $subject['urban_treatment'] ?? '');
        return self::join([$use ? 'Compatibilidad: ' . $use : '', $treatment ? 'Tratamiento: ' . $treatment : '']);
    }

    private static function mobilityText(array $subject, array $labels): string
    {
        return self::join([self::label($labels, 'road_condition', $subject['road_condition'] ?? ''),
            self::label($labels, 'access_facility', $subject['access_facility'] ?? ''),
            self::label($labels, 'transport_connectivity', $subject['transport_connectivity'] ?? '')]);
    }

    private static function roadHierarchy(string $value): string { return ['via_principal' => 'arterial', 'via_colectora' => 'colectora', 'via_secundaria' => 'local', 'via_local' => 'local', 'peatonal' => 'peatonal'][$value] ?? 'no_verificado'; }
    private static function sectorUse(string $value): string { return ['oficina' => 'comercial', 'consultorio' => 'comercial', 'logistico' => 'industrial', 'hotelero' => 'turistico', 'dotacional' => 'institucional'][$value] ?? $value; }
    private static function transport(string $value): string { return ['muy_alta' => 'amplio', 'alta' => 'amplio', 'media' => 'moderado', 'baja' => 'limitado', 'muy_baja' => 'limitado'][$value] ?? 'no_verificado'; }
    private static function connectivity(string $value): string { return ['muy_alta' => 'alta', 'alta' => 'alta', 'media' => 'media', 'baja' => 'baja', 'muy_baja' => 'baja'][$value] ?? 'no_verificado'; }
    private static function commercialActivity(string $value): string { return in_array($value, ['comercial', 'mixto', 'oficina', 'consultorio'], true) ? 'media' : 'baja'; }
    private static function consolidation(string $value): string { return ['muy_alta' => 'alta', 'alta' => 'alta', 'media' => 'media', 'baja' => 'baja', 'muy_baja' => 'baja'][$value] ?? ''; }
    private static function stratum(string $value): string { return in_array($value, ['5', '6'], true) ? 'alto' : (in_array($value, ['3', '4'], true) ? 'medio' : (in_array($value, ['1', '2'], true) ? 'bajo' : 'no_verificado')); }
}
