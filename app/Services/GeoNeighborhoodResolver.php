<?php
declare(strict_types=1);
namespace App\Services;

final class GeoNeighborhoodResolver
{
    public static function id(array $input, array $neighborhoods): string
    {
        $neighborhoodId = trim((string) ($input['neighborhood_id'] ?? ''));
        if ($neighborhoodId !== '') return mb_substr($neighborhoodId, 0, 80);

        $query = mb_strtolower(trim((string) ($input['neighborhood_query'] ?? '')));
        if ($query === '') return '';

        $matches = [];
        foreach ($neighborhoods as $item) {
            $label = self::label($item);
            $name = mb_strtolower((string) ($item['name'] ?? ''));
            if ($label === $query || $name === $query) return (string) $item['id'];
            if (str_contains($label, $query) || str_contains($name, $query)) {
                $matches[(string) $item['id']] = (string) $item['id'];
            }
        }
        return count($matches) === 1 ? array_key_first($matches) : '';
    }

    private static function label(array $item): string
    {
        return mb_strtolower(trim(implode(' · ', array_filter([
            $item['name'] ?? '',
            $item['locality_name'] ?? '',
            $item['commune_ucg'] ?? '',
            $item['city_name'] ?? '',
        ]))));
    }
}
