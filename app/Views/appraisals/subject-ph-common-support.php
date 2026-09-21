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
$commonFoundText = static function (array $keys) use ($phMap, $commonLabels, $commonShort): string {
    $names = [];
    foreach ($keys as $key) if (trim($phMap('common_areas', (string) $key, 'status')) !== '') $names[] = (string) ($commonLabels[$key] ?? $key);
    return $names ? $commonShort(implode(', ', array_slice($names, 0, 8))) : '';
};
$commonGroupKeys = static fn (string $group) => array_keys($phCatalog['commonAreaGroups'][$group][1] ?? []);
$priorityTotal = count($priorityKeys); $priorityCount = count($priorityFound);
$priorityText = $commonFoundText($priorityKeys);
$commonRows = [
    ['Texto editable para Entregable', $technicalValue('resumen_comunes_ph') !== '' ? 'Texto construido' : 'Sin texto construido', $technicalValue('resumen_comunes_ph') !== '' ? 'ok' : 'missing', 'Construir el párrafo con bienes comunes y aporte valuatorio.'],
    ['Bienes comunes esenciales', $commonFoundText($commonGroupKeys('esenciales')) ?: 'Sin esenciales confirmados', ($commonStats['esenciales'][1] ?? 0) > 0 ? 'ok' : 'warn', 'Confirmar estructura, redes, escaleras, red contra incendio, tanques y evacuación.'],
    ['Amenidades y bienes no esenciales', $commonFoundText($commonGroupKeys('no_esenciales')) ?: 'Sin amenidades confirmadas', ($commonStats['no_esenciales'][1] ?? 0) > 0 ? 'ok' : 'warn', 'Completar solo las amenidades que apliquen a la tipología.'],
    ['Áreas comunes de uso exclusivo', $commonFoundText($commonGroupKeys('uso_exclusivo')) ?: 'Sin usos exclusivos confirmados', ($commonStats['uso_exclusivo'][1] ?? 0) > 0 ? 'ok' : 'warn', 'Precisar parqueaderos, depósitos, terrazas, patios o zonas asignadas.'],
    ['Soporte operativo y técnico común', $commonFoundText($commonGroupKeys('soporte_operativo')) ?: 'Sin soporte operativo confirmado', ($commonStats['soporte_operativo'][1] ?? 0) > 0 ? 'ok' : 'warn', 'Confirmar portería, vigilancia, CCTV, administración, planta, subestación, vías o maniobra según tipología.'],
    ['Dotación prioritaria por tipología', $priorityTotal > 0 ? $priorityCount . ' de ' . $priorityTotal . ' factores: ' . ($priorityText ?: 'sin factores detectados') : 'Sin tipología seleccionada', $priorityTotal === 0 ? 'missing' : ($priorityCount === 0 ? 'warn' : ($priorityCount >= min(5, $priorityTotal) ? 'ok' : 'warn')), 'Comparar únicamente contra PH de la misma tipología, escala y localización.'],
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
