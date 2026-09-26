<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;

final class AppraisalConstructionInput
{
    public static function lifeData(array $unit): array
    {
        return [
            'construction_apparent_age_years' => self::smallIntOrNull($unit['construction_apparent_age_years'] ?? null),
            'construction_useful_life_years' => self::smallIntOrNull($unit['construction_useful_life_years'] ?? null),
            'construction_remaining_life_years' => self::smallIntOrNull($unit['construction_remaining_life_years'] ?? null),
            'construction_rentable_units' => self::smallIntOrNull($unit['construction_rentable_units'] ?? null),
        ];
    }

    public static function conservationData(array $unit, ?int $actorId): array
    {
        if (isset($unit['conservation_items']) || isset($unit['conservation_summary'])) {
            return AppraisalConservationNarrator::normalize(
                is_array($unit['conservation_items'] ?? null) ? $unit['conservation_items'] : [],
                is_array($unit['conservation_summary'] ?? null) ? $unit['conservation_summary'] : [],
                $actorId
            );
        }
        return ['legacy_json' => self::jsonMap($unit['conservation'] ?? []),
            'result_json' => '{}', 'generated_text' => '', 'approved_text' => ''];
    }

    private static function smallIntOrNull(mixed $value): ?int
    {
        $value = trim((string) $value);
        if ($value === '') return null;
        $number = filter_var($value, FILTER_VALIDATE_INT);
        if ($number === false || $number < 0 || $number > 500) {
            throw new HttpException(422, 'Los enteros deben estar entre 0 y 500.');
        }
        return $number;
    }

    private static function jsonMap(mixed $values): string
    {
        if (!is_array($values)) return '{}';
        $clean = [];
        foreach ($values as $key => $value) {
            if (is_string($key)) $clean[mb_substr($key, 0, 60)] = mb_substr(trim((string) $value), 0, 120);
        }
        return json_encode($clean, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }
}
