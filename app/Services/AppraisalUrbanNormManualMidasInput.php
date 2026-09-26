<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalUrbanNormManualMidasInput
{
    private UrbanOccupancyIndexEstimator $occupancy;

    public function __construct()
    {
        $this->occupancy = new UrbanOccupancyIndexEstimator();
    }

    public function apply(array &$data, mixed $manual, array $limits): void
    {
        if (!is_array($manual)) return;
        foreach ($this->keys() as $key) {
            $value = mb_substr(trim((string) ($manual[$key] ?? '')), 0, $limits[$key] ?? 5000);
            if ($value !== '') $data[$key] = $value;
        }
        [$ratio, $source] = $this->occupancy->fromTexts((string) ($data['norm_free_area_text'] ?? ''),
            (string) ($data['occupancy_index'] ?: ($data['norm_other_potential_text'] ?? '')),
            (string) ($data['construction_index'] ?: ($data['norm_construction_index_text'] ?? '')),
            (string) ($data['max_floors'] ?: ($data['norm_max_height_text'] ?? '')));
        if ($ratio !== '') $data['occupancy_index'] = $ratio;
        if ($source !== '') $data['norm_other_potential_text'] = trim((string) ($data['norm_other_potential_text'] ?? '')
            . "\n\nÍndice de ocupación " . $source . ': ' . $ratio);
    }

    private function keys(): array
    {
        return ['use_regulation_table', 'use_principal_text', 'use_compatible_text',
            'use_complementary_text', 'use_restricted_text', 'use_prohibited_text',
            'norm_unit_basic_text', 'norm_free_area_text', 'norm_min_lot_front_text',
            'norm_max_height_text', 'norm_construction_index_text', 'norm_isolation_text',
            'norm_other_potential_text', 'land_area_normative_m2', 'actual_built_area_m2',
            'lot_front_normative_m', 'occupancy_index', 'max_floors', 'construction_index'];
    }

}
