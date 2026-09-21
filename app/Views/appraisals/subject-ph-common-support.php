<?php
$commonShort = static function (string $value): string {
    $value = trim(preg_replace('/\s+/u', ' ', $value) ?? '');
    return mb_strlen($value) > 150 ? mb_substr($value, 0, 147) . '…' : $value;
};
$commonPill = static function (string $state): string {
    return match ($state) {
        'ok' => '<button type="button" disabled class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">Completo</button>',
        'risk' => '<button type="button" disabled class="rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-800">Alerta</button>',
        'warn' => '<button type="button" disabled class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">Revisar</button>',
        default => '<button type="button" disabled class="rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-800">Falta</button>',
    };
};
$commonLabels = $phCatalog['commonAreas'];
$commonBucket = static function (array $keys) use ($phMap, $commonLabels): array {
    $bucket = ['ok' => [], 'warn' => [], 'risk' => [], 'missing' => [], 'na' => []];
    foreach ($keys as $key) {
        $status = trim($phMap('common_areas', (string) $key, 'status'));
        $target = in_array($status, ['ok', 'warn', 'risk', 'na'], true) ? $status : 'missing';
        $bucket[$target][] = (string) ($commonLabels[$key] ?? $key);
    }
    return $bucket;
};
$commonList = static fn (array $names): string => $commonShort(implode(', ', array_slice($names, 0, 8)));
$commonMetric = static function (array $keys) use ($commonBucket, $commonList): array {
    $bucket = $commonBucket($keys); $applicable = max(0, count($keys) - count($bucket['na']));
    $ready = count($bucket['ok']); $pending = count($bucket['warn']); $alerts = count($bucket['risk']);
    $value = $ready . ' verificados de ' . $applicable . ' aplicables';
    if ($bucket['ok']) $value .= ': ' . $commonList($bucket['ok']);
    if ($pending > 0) $value .= '; ' . $pending . ' por confirmar';
    if ($alerts > 0) $value .= '; ' . $alerts . ' con alerta';
    if (count($bucket['na']) > 0) $value .= '; ' . count($bucket['na']) . ' no aplican';
    $lack = [];
    if ($bucket['risk']) $lack[] = 'Atender alertas: ' . $commonList($bucket['risk']);
    if ($bucket['warn']) $lack[] = 'Confirmar: ' . $commonList($bucket['warn']);
    if ($bucket['missing']) $lack[] = 'Faltan: ' . $commonList($bucket['missing']);
    $state = $alerts > 0 ? 'risk' : ($ready > 0 && $pending === 0 && !$bucket['missing'] ? 'ok' : ($ready > 0 || $pending > 0 ? 'warn' : 'missing'));
    return [$value, $state, $lack ? implode(' · ', $lack) : 'Sin faltantes en esta matriz.'];
};
$commonGroupKeys = static fn (string $group) => array_keys($phCatalog['commonAreaGroups'][$group][1] ?? []);
$essentialMetric = $commonMetric($commonGroupKeys('esenciales'));
$amenityMetric = $commonMetric($commonGroupKeys('no_esenciales'));
$exclusiveMetric = $commonMetric($commonGroupKeys('uso_exclusivo'));
$supportMetric = $commonMetric($commonGroupKeys('soporte_operativo'));
$priorityMetric = $commonMetric($priorityKeys);
$priorityValue = $priorityKeys ? $priorityMetric[0] : 'Sin tipología seleccionada';
$priorityState = $priorityKeys ? $priorityMetric[1] : 'missing';
$priorityMissing = $priorityKeys ? $priorityMetric[2] : 'Seleccionar la tipología comparable.';
$commonRows = [
    ['Texto editable para Entregable', $technicalValue('resumen_comunes_ph') !== '' ? 'Texto construido' : 'Sin texto construido', $technicalValue('resumen_comunes_ph') !== '' ? 'ok' : 'missing', 'Construir el párrafo con bienes comunes y aporte valuatorio.'],
    ['Bienes comunes esenciales', ...$essentialMetric],
    ['Amenidades y bienes no esenciales', ...$amenityMetric],
    ['Áreas comunes de uso exclusivo', ...$exclusiveMetric],
    ['Soporte operativo y técnico común', ...$supportMetric],
    ['Dotación prioritaria por tipología', $priorityValue, $priorityState, $priorityMissing],
];
?>
<div class="rounded-xl border border-slate-200 bg-white p-4 text-sm leading-6 lg:col-span-2">
    <h4 class="font-semibold text-slate-900">Campos de bienes comunes y soporte para construir el Entregable</h4>
    <p class="mt-1 text-slate-600">La selección sí afecta el informe: verificado entra como beneficio; por confirmar queda pendiente; alerta pasa a salvedad; no aplica sale del conteo.</p>
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
