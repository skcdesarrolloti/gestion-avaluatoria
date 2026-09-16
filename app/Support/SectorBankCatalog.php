<?php
declare(strict_types=1);
namespace App\Support;

final class SectorBankCatalog
{
    public static function sections(): array
    {
        return [
            '01' => ['Identificación y localización', ['sector_name', 'influence_area', 'sector_boundaries']],
            '02' => ['Localización y soporte cartográfico', ['sector_source']],
            '03' => ['Servicios públicos', ['services_status', 'infrastructure_notes']],
            '04' => ['Uso predominante', ['predominant_use', 'daily_dynamics']],
            '05' => ['Normatividad urbanística', ['urban_norm', 'urban_treatment', 'development_level']],
            '06' => ['Vías y señalización vial', ['road_hierarchy', 'access_roads']],
            '07' => ['Amoblamiento urbano', ['public_space_state']],
            '08' => ['Estratificación socioeconómica', ['socioeconomic_profile']],
            '09' => ['Legalidad de la construcción', ['support_notes']],
            '10' => ['Topografía', ['mitigation_notes']],
            '11' => ['Transporte', ['public_transport', 'connectivity', 'mobility_notes']],
            '12' => ['Edificaciones importantes', ['nearby_facilities', 'activity_anchors']],
            '13' => ['Externalidades', ['positive_externalities', 'negative_externalities', 'sector_risks']],
            '14' => ['Soporte gráfico', ['field_sources']],
            '15' => ['Conclusión sectorial', ['sector_conclusion']],
            '16' => ['Consideraciones generales del sector', ['sector_report_text']],
            '17' => ['Impresión del documento', ['sector_report_text']],
        ];
    }

    public static function sources(): array
    {
        return [
            ['barrios_cartagena', 'Base territorial', 'Datos Abiertos Cartagena - Barrios', 'Distrito de Cartagena',
                'https://datosabiertos.cartagena.gov.co/dataset/listado-de-barrios-del-distrito-de-cartagena', 'SI', '2026-04-24'],
            ['midas_normatividad', 'Normatividad', 'MIDAS Cartagena', 'Secretaría de Planeación Distrital',
                'https://midas.cartagena.gov.co/Content/Switcher', 'PARCIAL', '2026-06-02'],
            ['pot_usos', 'Normatividad', 'Matriz POT de usos', 'POT Cartagena',
                'https://seguimientopot.cartagena.gov.co/', 'PARCIAL', '2026-06-02'],
            ['campo_analista', 'Campo', 'Validación del analista', 'Perito / analista',
                '', 'NO', null],
            ['registro_fotografico', 'Soporte', 'Registro fotográfico de campo', 'Perito / analista',
                '', 'NO', null],
        ];
    }

    public static function defaultData(string $code, array $subject, array $sector): array
    {
        $data = ['barrio' => (string) ($subject['neighborhood_name'] ?? ''),
            'localidad' => (string) ($subject['locality_name'] ?? ''),
            'comuna_ucg' => (string) ($subject['commune_ucg'] ?? ''),
            'ciudad' => (string) ($subject['city_name'] ?? '')];
        foreach (self::sections()[$code][1] ?? [] as $field) {
            $data[$field] = (string) ($sector[$field] ?? '');
        }
        if ($code === '01' && ($data['mapa_barrio_url'] ?? '') === '' && $data['barrio'] !== '') {
            $data['mapa_barrio_url'] = 'https://www.google.com/maps/search/?api=1&query='
                . rawurlencode($data['barrio'] . ', ' . ($data['ciudad'] ?: 'Cartagena') . ', Colombia');
        }
        return array_filter($data, static fn (string $value): bool => $value !== '');
    }

    public static function defaultText(string $code, array $data): string
    {
        $barrio = (string) ($data['barrio'] ?? 'el barrio seleccionado');
        return match ($code) {
            '01' => 'Ficha base del sector ' . $barrio . ', con identificación territorial, localidad, UCG y delimitación pendiente de validación de campo.',
            '03' => 'Lectura de servicios públicos del sector ' . $barrio . ', preparada para verificar cobertura, continuidad y soporte operativo.',
            '05' => 'Normatividad urbanística del sector ' . $barrio . ', sujeta a contraste con MIDAS, POT vigente y consulta específica cuando aplique.',
            '14' => 'Soporte gráfico requerido para documentar localización, accesos, equipamientos, externalidades y evidencias de campo.',
            '16' => 'Texto técnico consolidado del sector ' . $barrio . ' para alimentar el entregable, con revisión final del analista.',
            default => 'Sección sectorial de ' . $barrio . ' preparada para completar con fuentes, observación de campo y evidencia.',
        };
    }
}
