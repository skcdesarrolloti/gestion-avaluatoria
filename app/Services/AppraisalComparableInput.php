<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalComparableInput
{
    private const FIELDS = [
        'id', 'active', 'status', 'source_type', 'source_name', 'source_url', 'query_used',
        'operation', 'property_type', 'neighborhood', 'address_hint', 'project_name',
        'price_amount', 'price_unit', 'area_m2', 'admin_fee', 'vat_applies', 'bedrooms',
        'bathrooms', 'parking_spaces', 'floor_level', 'contact_name', 'contact_phone',
        'listing_code', 'listing_date', 'consulted_at', 'comparability_notes', 'rejection_reason',
    ];

    public static function rows(array $posted): array
    {
        $items = $posted['comparables'] ?? [];
        if (!is_array($items)) return [];
        $rows = [];
        foreach ($items as $item) {
            if (!is_array($item)) continue;
            $row = [];
            foreach (self::FIELDS as $field) $row[$field] = $item[$field] ?? '';
            if (self::meaningful($row)) $rows[] = $row;
        }
        return $rows;
    }

    private static function meaningful(array $row): bool
    {
        foreach (['source_name', 'source_url', 'price_amount', 'area_m2', 'neighborhood', 'project_name', 'comparability_notes'] as $field) {
            if (trim((string) ($row[$field] ?? '')) !== '') return true;
        }
        return false;
    }
}
