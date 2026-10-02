<?php
declare(strict_types=1);
namespace App\Services;
final class SubjectUnitNavigation
{
    public static function selected(array $units): string
    {
        $requested = is_string($_GET['unit'] ?? null) ? $_GET['unit'] : '';
        return in_array($requested, array_column($units, 'id'), true) ? $requested : (string) ($units[0]['id'] ?? '');
    }
    public static function detail(array $allowed, string $fallback): string
    {
        $requested = is_string($_GET['detail'] ?? null) ? $_GET['detail'] : '';
        return in_array($requested, $allowed, true) ? $requested : $fallback;
    }
}
