<?php
$mapRows = [];
$toFloat = static function (mixed $value): ?float {
    $text = str_replace(',', '.', trim((string) $value));
    return is_numeric($text) ? (float) $text : null;
};
$subjectLat = $toFloat($subject['latitude'] ?? null);
$subjectLng = $toFloat($subject['longitude'] ?? null);
foreach (is_array($comparableRows ?? null) ? $comparableRows : [] as $index => $row) {
    if (($row['active'] ?? 'si') !== 'si' || ($row['status'] ?? '') === 'descartada') continue;
    $lat = $toFloat($row['latitude'] ?? null); $lng = $toFloat($row['longitude'] ?? null);
    if ($lat === null || $lng === null) continue;
    $mapRows[] = ['n' => $index + 1, 'lat' => $lat, 'lng' => $lng, 'row' => $row];
}
$allLat = array_map(static fn (array $p): float => $p['lat'], $mapRows);
$allLng = array_map(static fn (array $p): float => $p['lng'], $mapRows);
if ($subjectLat !== null && $subjectLng !== null) { $allLat[] = $subjectLat; $allLng[] = $subjectLng; }
$hasMap = $allLat !== [] && $allLng !== [];
$minLat = $hasMap ? min($allLat) : 0; $maxLat = $hasMap ? max($allLat) : 1;
$minLng = $hasMap ? min($allLng) : 0; $maxLng = $hasMap ? max($allLng) : 1;
if ($minLat === $maxLat) { $minLat -= 0.002; $maxLat += 0.002; }
if ($minLng === $maxLng) { $minLng -= 0.002; $maxLng += 0.002; }
$xy = static function (float $lat, float $lng) use ($minLat, $maxLat, $minLng, $maxLng): array {
    $x = 40 + (($lng - $minLng) / ($maxLng - $minLng)) * 520;
    $y = 320 - (($lat - $minLat) / ($maxLat - $minLat)) * 260;
    return [round($x, 1), round($y, 1)];
};
$boundsText = $hasMap ? number_format($minLat, 5, '.', '') . ', ' . number_format($minLng, 5, '.', '')
    . ' / ' . number_format($maxLat, 5, '.', '') . ', ' . number_format($maxLng, 5, '.', '') : '';
$tip = static fn (string $text): string => '<span class="help-dot" title="' . e($text) . '">?</span>';
?>
<section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Mapa de muestras seleccionadas <?= $tip('Origen: columnas Latitud, Longitud y Precisión mapa en 2. Capturar muestras. El mapa se dibuja automáticamente con filas activas que tengan coordenadas.') ?></p>
            <h3 class="mt-2 text-xl font-semibold">Concentración espacial del mercado</h3>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Registra latitud y longitud en Captura para ver si las muestras realmente rodean el bien sujeto
                o si se están alejando del microsector comparable.
            </p>
        </div>
        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700"><?= count($mapRows) ?> muestra(s) con coordenadas</span>
    </div>
    <?php if (!$hasMap): ?>
        <div class="mt-4 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-6 text-sm leading-6 text-slate-600">
            Aún no hay coordenadas. En la pestaña Captura completa latitud y longitud de las muestras usadas o
            preseleccionadas. Si el portal no muestra dirección exacta, marca la precisión como aproximada o solo sector.
            Para obtenerlas puedes abrir la ubicación en Google Maps, hacer clic sobre el punto y copiar los dos números
            que aparecen como latitud y longitud.
        </div>
    <?php else: ?>
        <div class="mt-4 grid gap-4 xl:grid-cols-[1.1fr_0.9fr]">
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                <svg class="h-auto w-full" viewBox="0 0 600 360" role="img" aria-label="Mapa esquemático de muestras comparables">
                    <rect x="20" y="20" width="560" height="320" rx="14" fill="#eef6ff" stroke="#cbd5e1" />
                    <path d="M60 300 C150 250 220 290 310 220 S450 140 540 95" fill="none" stroke="#bae6fd" stroke-width="14" stroke-linecap="round" />
                    <path d="M80 80 C180 120 250 70 330 120 S460 210 520 185" fill="none" stroke="#d1fae5" stroke-width="18" stroke-linecap="round" />
                    <?php if ($subjectLat !== null && $subjectLng !== null): ?>
                        <?php [$sx, $sy] = $xy($subjectLat, $subjectLng); ?>
                        <circle cx="<?= e((string) $sx) ?>" cy="<?= e((string) $sy) ?>" r="11" fill="#0f766e" stroke="white" stroke-width="3" />
                        <text x="<?= e((string) ($sx + 14)) ?>" y="<?= e((string) ($sy + 5)) ?>" font-size="13" font-weight="700" fill="#0f172a">Sujeto</text>
                    <?php endif; ?>
                    <?php foreach ($mapRows as $point): ?>
                        <?php [$x, $y] = $xy($point['lat'], $point['lng']); ?>
                        <circle cx="<?= e((string) $x) ?>" cy="<?= e((string) $y) ?>" r="8" fill="#2563eb" stroke="white" stroke-width="2" />
                        <text x="<?= e((string) ($x + 10)) ?>" y="<?= e((string) ($y + 4)) ?>" font-size="11" font-weight="700" fill="#1e3a8a">#<?= e((string) $point['n']) ?></text>
                    <?php endforeach; ?>
                </svg>
                <p class="mt-2 text-xs text-slate-500">Rango mostrado: <?= e($boundsText) ?>. Es un mapa operativo esquemático basado en coordenadas, no reemplaza verificación cartográfica oficial.</p>
            </div>
            <div class="max-h-[380px] overflow-auto rounded-xl border border-slate-200">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-bold uppercase text-slate-500">
                        <tr><th class="px-3 py-3">#</th><th class="px-3 py-3">Muestra</th><th class="px-3 py-3">Precisión</th><th class="px-3 py-3">Abrir</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($mapRows as $point): $row = $point['row']; ?>
                            <tr>
                                <td class="px-3 py-3 font-bold text-slate-500"><?= e((string) $point['n']) ?></td>
                                <td class="px-3 py-3"><strong><?= e((string) ($row['source_name'] ?: $row['project_name'] ?: 'Muestra')) ?></strong><br><span class="text-xs text-slate-500"><?= e((string) ($row['neighborhood'] ?? '')) ?></span></td>
                                <td class="px-3 py-3"><?= e((string) ($row['location_precision'] ?: 'No definida')) ?></td>
                                <td class="px-3 py-3"><a class="text-sm font-bold text-teal-700 underline" href="https://www.google.com/maps/search/?api=1&query=<?= e(rawurlencode($point['lat'] . ',' . $point['lng'])) ?>" target="_blank" rel="noopener" data-no-fetch>Mapa</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</section>
