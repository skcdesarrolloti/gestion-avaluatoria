<?php
declare(strict_types=1);
namespace App\Services;

final class MarketComponentReview
{
    /** Descriptive statistics only; no automatic adoption, adjustment or exclusion of outliers. */
    public static function build(array $rows): array
    {
        $groups = []; $pending = 0; $excluded = 0;
        foreach ($rows as $row) {
            if (($row['active'] ?? '') === 'no' || ($row['status'] ?? '') === 'descartada') { $excluded++; continue; }
            $price = (float) ($row['price_amount'] ?? 0);
            $area = (float) ($row['area_m2'] ?? 0);
            $basis = trim($row['area_basis'] ?? '');
            $operation = mb_strtolower(trim($row['operation'] ?? ''));
            $priceUnit = $row['price_unit'] ?? '';
            if (($row['status'] ?? '') !== 'usada' || $price <= 0 || $basis === '' || trim($row['property_type'] ?? '') === ''
                || !in_array($operation, ['venta', 'arriendo'], true)
                || !in_array($priceUnit, ['precio_total', 'canon_mensual', 'valor_m2'], true)
                || ($priceUnit !== 'valor_m2' && $area <= 0)
                || ($operation === 'venta' && $priceUnit === 'canon_mensual')
                || !in_array($row['ph_regime'] ?? '', ['si', 'no'], true)) { $pending++; continue; }
            $key = implode(' · ', [$operation, $row['property_type'] ?? '', $row['ph_regime'] === 'si' ? 'PH' : 'No PH', $basis, $priceUnit]);
            $groups[$key][] = $priceUnit === 'valor_m2' ? $price : $price / $area;
        }
        foreach ($groups as $key => $values) {
            sort($values, SORT_NUMERIC);
            $n = count($values); $mean = array_sum($values) / $n;
            $variance = $n > 1 ? array_sum(array_map(static fn ($v) => ($v - $mean) ** 2, $values)) / ($n - 1) : null;
            $middle = intdiv($n, 2);
            $groups[$key] = ['n' => $n, 'mean' => $mean,
                'median' => $n % 2 ? $values[$middle] : ($values[$middle - 1] + $values[$middle]) / 2,
                'sd' => $variance === null ? null : sqrt($variance),
                'cv' => $variance === null ? null : 100 * sqrt($variance) / $mean];
        }
        return ['groups' => $groups, 'pending' => $pending, 'excluded' => $excluded];
    }
}
