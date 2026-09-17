<?php
declare(strict_types=1);
namespace App\Services;

final class MidasGeometry
{
    public static function bbox(array $geometry): ?array
    {
        $points = self::points($geometry);
        if ($points === []) return null;
        $xs = array_column($points, 0);
        $ys = array_column($points, 1);
        return [min($xs), min($ys), max($xs), max($ys)];
    }

    public static function intersectsFeature(array $feature, array $polygonFeature): bool
    {
        $geometry = is_array($feature['geometry'] ?? null) ? $feature['geometry'] : [];
        $polygon = is_array($polygonFeature['geometry'] ?? null) ? $polygonFeature['geometry'] : [];
        if ($geometry === [] || $polygon === []) return false;
        $box = self::bbox($geometry);
        $polygonBox = self::bbox($polygon);
        if (!$box || !$polygonBox || !self::boxIntersects($box, $polygonBox)) return false;
        $type = (string) ($geometry['type'] ?? '');
        foreach (self::points($geometry) as $point) {
            if (self::pointInAnyPolygon($point, $polygon)) return true;
        }
        if (str_contains($type, 'Polygon')) {
            foreach (self::points($polygon) as $point) {
                if (self::pointInAnyPolygon($point, $geometry)) return true;
            }
        }
        return str_contains($type, 'LineString') || str_contains($type, 'Polygon');
    }

    private static function points(array $geometry): array
    {
        $type = (string) ($geometry['type'] ?? '');
        $coordinates = $geometry['coordinates'] ?? [];
        return match ($type) {
            'Point' => self::isPoint($coordinates) ? [$coordinates] : [],
            'MultiPoint', 'LineString' => self::pointList($coordinates),
            'MultiLineString', 'Polygon' => self::nestedPoints($coordinates),
            'MultiPolygon' => self::deepPoints($coordinates),
            default => [],
        };
    }

    private static function pointInAnyPolygon(array $point, array $geometry): bool
    {
        foreach (self::rings($geometry) as $ring) {
            if (self::pointInRing($point, $ring)) return true;
        }
        return false;
    }

    private static function rings(array $geometry): array
    {
        $type = (string) ($geometry['type'] ?? '');
        $coordinates = $geometry['coordinates'] ?? [];
        if ($type === 'Polygon') return is_array($coordinates) ? $coordinates : [];
        if ($type !== 'MultiPolygon' || !is_array($coordinates)) return [];
        $rings = [];
        foreach ($coordinates as $polygon) {
            if (is_array($polygon)) $rings = array_merge($rings, $polygon);
        }
        return $rings;
    }

    private static function pointInRing(array $point, array $ring): bool
    {
        if (!self::isPoint($point) || count($ring) < 3) return false;
        $inside = false;
        $j = count($ring) - 1;
        for ($i = 0, $count = count($ring); $i < $count; $j = $i++) {
            if (!self::isPoint($ring[$i] ?? null) || !self::isPoint($ring[$j] ?? null)) continue;
            [$xi, $yi] = [(float) $ring[$i][0], (float) $ring[$i][1]];
            [$xj, $yj] = [(float) $ring[$j][0], (float) $ring[$j][1]];
            $intersect = (($yi > $point[1]) !== ($yj > $point[1]))
                && ($point[0] < ($xj - $xi) * ($point[1] - $yi) / (($yj - $yi) ?: 1e-12) + $xi);
            if ($intersect) $inside = !$inside;
        }
        return $inside;
    }

    private static function pointList(mixed $value): array
    {
        if (!is_array($value)) return [];
        return array_values(array_filter($value, static fn ($point): bool => self::isPoint($point)));
    }

    private static function nestedPoints(mixed $value): array
    {
        $points = [];
        if (!is_array($value)) return $points;
        foreach ($value as $list) $points = array_merge($points, self::pointList($list));
        return $points;
    }

    private static function deepPoints(mixed $value): array
    {
        $points = [];
        if (!is_array($value)) return $points;
        foreach ($value as $nested) $points = array_merge($points, self::nestedPoints($nested));
        return $points;
    }

    private static function boxIntersects(array $a, array $b): bool
    {
        return $a[0] <= $b[2] && $a[2] >= $b[0] && $a[1] <= $b[3] && $a[3] >= $b[1];
    }

    private static function isPoint(mixed $value): bool
    {
        return is_array($value) && isset($value[0], $value[1]) && is_numeric($value[0]) && is_numeric($value[1]);
    }
}
