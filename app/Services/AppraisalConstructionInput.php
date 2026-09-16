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
}
