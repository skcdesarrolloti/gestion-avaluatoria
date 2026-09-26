<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalUrbanNormManualMidasInput
{
    public function apply(array &$data, mixed $manual, array $limits): void
    {
        if (!is_array($manual)) return;
        foreach ($this->keys() as $key) {
            $value = mb_substr(trim((string) ($manual[$key] ?? '')), 0, $limits[$key] ?? 5000);
            if ($key === 'occupancy_index') $value = $this->ratioText($value);
            if ($value !== '') $data[$key] = $value;
        }
        if (($data['norm_other_potential_text'] ?? '') !== '' && ($data['occupancy_index'] ?? '') === '') {
            $data['occupancy_index'] = $this->ratioText($data['norm_other_potential_text']);
        }
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

    private function ratioText(string $value): string
    {
        if (!preg_match('/([0-9]+(?:[,.][0-9]+)?)\s*%?/u', $value, $match)) return '';
        $number = (float) str_replace(',', '.', $match[1]);
        if ($number > 1) $number /= 100;
        return rtrim(rtrim(number_format($number, 4, '.', ''), '0'), '.');
    }
}
