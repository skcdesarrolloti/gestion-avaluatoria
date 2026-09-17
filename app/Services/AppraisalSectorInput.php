<?php
declare(strict_types=1);
namespace App\Services;
use App\Support\AppraisalSectorCatalog;

final class AppraisalSectorInput
{
    public static function data(array $input): array
    {
        $options = AppraisalSectorCatalog::options();
        $long = ['influence_area', 'sector_boundaries', 'infrastructure_notes', 'urban_norm',
            'access_roads', 'mobility_notes', 'nearby_facilities', 'activity_anchors',
            'daily_dynamics', 'positive_externalities', 'negative_externalities',
            'sector_risks', 'mitigation_notes', 'field_sources', 'support_notes', 'sector_map_url',
            'sector_conclusion', 'sector_report_text'];
        $limits = ['sector_name' => 160, 'sector_source' => 220, 'urban_treatment' => 100,
            'sector_north_boundary' => 220, 'sector_east_boundary' => 220,
            'sector_south_boundary' => 220, 'sector_west_boundary' => 220];
        $data = [];
        foreach (AppraisalSectorCatalog::keys() as $key) {
            $value = trim((string) ($input[$key] ?? ''));
            if (isset($options[$key])) {
                $data[$key] = array_key_exists($value, $options[$key]) ? $value : '';
                continue;
            }
            $data[$key] = mb_substr($value, 0, in_array($key, $long, true) ? 2400 : ($limits[$key] ?? 180));
        }
        return $data;
    }
}
