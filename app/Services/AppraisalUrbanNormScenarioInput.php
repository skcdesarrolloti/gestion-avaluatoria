<?php
declare(strict_types=1);
namespace App\Services;
use App\Support\UrbanNormativeScenarioCatalog;

final class AppraisalUrbanNormScenarioInput
{
    public static function normalize(array $input): string
    {
        if (!array_key_exists('normative_scenarios', $input) && is_string($input['normative_scenarios_json'] ?? null)) {
            $decoded = self::decode($input['normative_scenarios_json']);
            return json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}';
        }
        $posted = is_array($input['normative_scenarios'] ?? null) ? $input['normative_scenarios'] : [];
        $rows = [];
        foreach (UrbanNormativeScenarioCatalog::routes() as $key => $route) {
            $row = is_array($posted[$key] ?? null) ? $posted[$key] : [];
            $rows[$key] = [
                'enabled' => !empty($row['enabled']),
                'label' => $route['label'],
                'document_slug' => self::text($row, 'document_slug', 100),
                'table_slug' => self::text($row, 'table_slug', 120),
                'category_slug' => self::text($row, 'category_slug', 140),
                'activity' => self::text($row, 'activity', 180),
                'result' => self::result((string) ($row['result'] ?? '')),
                'parameters_summary' => self::text($row, 'parameters_summary', 5000),
                'observations' => self::text($row, 'observations', 5000),
            ];
        }
        return json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}';
    }

    public static function decode(mixed $json): array
    {
        $decoded = is_string($json) && $json !== '' ? json_decode($json, true) : [];
        $decoded = is_array($decoded) ? $decoded : [];
        $blank = UrbanNormativeScenarioCatalog::blankScenarios();
        foreach ($blank as $key => $row) {
            if (!is_array($decoded[$key] ?? null)) continue;
            $blank[$key] = array_replace($row, array_intersect_key($decoded[$key], $row));
            $blank[$key]['enabled'] = !empty($decoded[$key]['enabled']);
            $blank[$key]['result'] = self::result((string) ($decoded[$key]['result'] ?? ''));
        }
        return $blank;
    }

    private static function result(string $value): string
    {
        return array_key_exists($value, UrbanNormativeScenarioCatalog::results()) ? $value : 'pendiente';
    }

    private static function text(array $input, string $key, int $limit): string
    {
        return mb_substr(trim((string) ($input[$key] ?? '')), 0, $limit);
    }
}
