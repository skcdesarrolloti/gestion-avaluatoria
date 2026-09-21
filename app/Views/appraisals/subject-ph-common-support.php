<?php
$commonShort = static function (string $value): string {
    $value = trim(preg_replace('/\s+/u', ' ', $value) ?? '');
    return mb_strlen($value) > 150 ? mb_substr($value, 0, 147) . '…' : $value;
};
$commonPill = static function (string $state): string {
    return match ($state) {
        'ok' => '<button type="button" disabled class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">Completo</button>',
        'warn' => '<button type="button" disabled class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">Revisar</button>',
        default => '<button type="button" disabled class="rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-800">Falta</button>',
    };
};
$commonLabels = $phCatalog['commonAreas'];
$commonNames = static function (array $keys, bool $found) use ($phMap, $commonLabels): array {
    $names = [];
    foreach ($keys as $key) {
        $has = trim($phMap('common_areas', (string) $key, 'status')) !== '';
        if ($has === $found) $names[] = (string) ($commonLabels[$key] ?? $key);
    }
    return $names;
};
$commonMetric = static function (array $keys) use ($commonNames, $commonShort): array {
    $found = $commonNames($keys, true); $missing = $commonNames($keys, false); $total = count($keys); $count = count($found);
    $value = $count . ' de ' . $total . ' elementos';
    $value .= $found ? ': ' . $commonShort(implode(', ', array_slice($found, 0, 8))) : ': sin elementos detectados';
    $lack = $missing ? 'Faltan: ' . $commonShort(implode(', ', array_slice($missing, 0, 8))) : 'Sin faltantes en esta matriz.';
    $state = $count === 0 ? 'warn' : ($count === $total ? 'ok' : 'warn');
    return [$value, $state, $lack];
};
$commonGroupKeys = static fn (string $group) => array_keys($phCatalog['commonAreaGroups'][$group][1] ?? []);
$essentialMetric = $commonMetric($commonGroupKeys('esenciales'));
$amenityMetric = $commonMetric($commonGroupKeys('no_esenciales'));
$exclusiveMetric = $commonMetric($commonGroupKeys('uso_exclusivo'));
$supportMetric = $commonMetric($commonGroupKeys('soporte_operativo'));
$priorityTotal = count($priorityKeys); $priorityCount = count($priorityFound);
$priorityFoundNames = $commonNames($priorityKeys, true); $priorityMissingNames = $commonNames($priorityKeys, false);
$priorityValue = $priorityTotal > 0 ? $priorityCount . ' de ' . $priorityTotal . ' factores prioritarios' : 'Sin tipología seleccionada';
if ($priorityTotal > 0) $priorityValue .= $priorityFoundNames ? ': ' . $commonShort(implode(', ', array_slice($priorityFoundNames, 0, 8))) : ': sin factores detectados';
$priorityMissing = $priorityMissingNames ? 'Faltan prioritarios: ' . $commonShort(implode(', ', array_slice($priorityMissingNames, 0, 8))) : 'Listo para comparar contra PH similares.';
$commonRows = [
    ['Texto editable para Entregable', $technicalValue('resumen_comunes_ph') !== '' ? 'Texto construido' : 'Sin texto construido', $technicalValue('resumen_comunes_ph') !== '' ? 'ok' : 'missing', 'Construir el párrafo con bienes comunes y aporte valuatorio.'],
    ['Bienes comunes esenciales', ...$essentialMetric],
    ['Amenidades y bienes no esenciales', ...$amenityMetric],
    ['Áreas comunes de uso exclusivo', ...$exclusiveMetric],
    ['Soporte operativo y técnico común', ...$supportMetric],
    ['Dotación prioritaria por tipología', $priorityValue, $priorityTotal === 0 ? 'missing' : ($priorityCount === 0 ? 'warn' : ($priorityCount >= min(5, $priorityTotal) ? 'ok' : 'warn')), $priorityMissing],
];
?>
<div class="rounded-xl border border-slate-200 bg-white p-4 text-sm leading-6 lg:col-span-2">
    <h4 class="font-semibold text-slate-900">Campos de bienes comunes y soporte para construir el Entregable</h4>
    <p class="mt-1 text-slate-600">Primero revisa esta matriz; el detalle editable queda abajo como soporte de cada conclusión.</p>
    <div class="mt-3 overflow-x-auto">
        <table class="w-full min-w-[56rem] text-left text-sm">
            <thead class="text-xs uppercase text-slate-500"><tr><th class="py-2 pr-3">Campo</th><th class="py-2 pr-3">Valor detectado</th><th class="py-2 pr-3">Estado</th><th class="py-2">Qué falta</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($commonRows as [$field, $value, $state, $missing]): ?>
                    <tr><td class="py-2 pr-3 font-semibold text-slate-800"><?= e($field) ?></td><td class="py-2 pr-3 text-slate-700"><?= e($value) ?></td><td class="py-2 pr-3"><?= $commonPill($state) ?></td><td class="py-2 text-slate-600"><?= e($state === 'ok' ? 'Listo para texto.' : $missing) ?></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950 lg:col-span-2">
    <strong>Dotación esperada por tipología:</strong>
    <?php foreach ($phCatalog['typologyPriorities'] as $typology => $keys): ?>
        <p class="mt-2" x-show="phTypology === '<?= e((string) $typology) ?>'">
            <?= e((string) ($phCatalog['typologies'][$typology] ?? $typology)) ?>:
            <?= e(implode(', ', array_map(static fn (string $key): string => (string) ($phCatalog['commonAreas'][$key] ?? $key), $keys))) ?>.
        </p>
    <?php endforeach; ?>
    <p class="mt-2" x-show="!phTypology">Selecciona una tipología para ver los factores prioritarios que debe revisar el analista.</p>
</div>
