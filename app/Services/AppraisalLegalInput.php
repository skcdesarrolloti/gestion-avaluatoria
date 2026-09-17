<?php
declare(strict_types=1);
namespace App\Services;
use App\Support\AppraisalLegalCatalog;

final class AppraisalLegalInput
{
    public static function data(array $input): array
    {
        $data = [];
        foreach (AppraisalLegalCatalog::fieldKeys() as $key) {
            $value = $input[$key] ?? '';
            if (is_array($value)) $value = '';
            $limit = str_starts_with($key, 'reporte_') || in_array($key, ['cabida_linderos', 'reformas_ph'], true) ? 3000 : 500;
            $data[$key] = mb_substr(trim((string) $value), 0, $limit);
        }
        return $data;
    }

    public static function mergeEmpty(array $current, array $suggested): array
    {
        foreach (AppraisalLegalCatalog::defaults() as $key => $empty) {
            $manual = trim((string) ($current[$key] ?? ''));
            if ($manual === '' && trim((string) ($suggested[$key] ?? '')) !== '') {
                $current[$key] = (string) $suggested[$key];
            } elseif (!array_key_exists($key, $current)) {
                $current[$key] = $empty;
            }
        }
        return $current;
    }
}
