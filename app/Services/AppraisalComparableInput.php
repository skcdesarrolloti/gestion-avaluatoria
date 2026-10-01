<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalComparableInput
{
    private const FIELDS = [
        'ph_regime', 'id', 'active', 'status', 'source_type', 'source_name', 'source_url', 'query_used',
        'operation', 'property_type', 'neighborhood', 'address_hint', 'project_name',
        'price_amount', 'price_unit', 'area_m2', 'admin_fee', 'vat_applies', 'bedrooms',
        'bathrooms', 'parking_spaces', 'floor_level', 'contact_name', 'contact_phone',
        'listing_code', 'listing_date', 'consulted_at', 'comparability_notes', 'rejection_reason',
        'stratum', 'age_years', 'building_condition', 'conservation_state', 'view_quality',
        'finish_quality', 'elevator', 'amenities', 'security_features', 'power_plant',
        'parking_relation', 'balcony_terrace', 'noise_humidity_sun', 'legal_relation_notes',
        'analysis_factor', 'latitude', 'longitude', 'location_precision', 'map_notes',
    ];

    public static function rows(array $posted): array
    {
        $items = $posted['comparables'] ?? [];
        if (isset($posted['comparable_rows_json'])) {
            try {
                if (!is_string($posted['comparable_rows_json']) || strlen($posted['comparable_rows_json']) > 2000000) throw new \JsonException();
                $items = json_decode($posted['comparable_rows_json'], true, 8, JSON_THROW_ON_ERROR);
                if (!is_array($items) || !array_is_list($items)) throw new \JsonException();
            } catch (\JsonException) { throw new \App\Core\HttpException(422, 'La matriz recibida no es válida; no se guardaron cambios.'); }
        }
        if (!is_array($items)) return [];
        $rows = [];
        foreach ($items as $item) {
            if (!is_array($item)) continue;
            $row = [];
            foreach (self::FIELDS as $field) {
                $value = $item[$field] ?? '';
                if (!is_scalar($value) && $value !== null) throw new \App\Core\HttpException(422, 'Un campo de la matriz contiene un formato inválido.');
                $row[$field] = $value;
            }
            if (self::meaningful($row)) $rows[] = $row;
        }
        return $rows;
    }

    private static function meaningful(array $row): bool
    {
        foreach (['source_name', 'source_url', 'price_amount', 'area_m2', 'neighborhood', 'project_name', 'comparability_notes', 'analysis_factor', 'latitude', 'longitude'] as $field) {
            if (trim((string) ($row[$field] ?? '')) !== '') return true;
        }
        return false;
    }
}
