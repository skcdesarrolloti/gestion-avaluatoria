<?php
$targetPerFactor = (int) ($sampleDesign['target_per_factor'] ?? 15);
$targetTotal = (int) ($sampleDesign['target_total'] ?? 60);
$sampleRows = is_array($comparableRows ?? null) ? $comparableRows : [];
$factorCounts = [];
$usableCount = 0;
$georefCount = 0;
foreach ($sampleRows as $row) {
    $hasData = trim((string) ($row['source_name'] ?? '')) !== ''
        || trim((string) ($row['source_url'] ?? '')) !== ''
        || trim((string) ($row['price_amount'] ?? '')) !== ''
        || trim((string) ($row['area_m2'] ?? '')) !== '';
    if ($hasData && ($row['active'] ?? 'si') === 'si' && ($row['status'] ?? '') !== 'descartada') $usableCount++;
    if (trim((string) ($row['latitude'] ?? '')) !== '' && trim((string) ($row['longitude'] ?? '')) !== '') $georefCount++;
    $factor = (string) ($row['analysis_factor'] ?? '');
    if ($factor !== '' && ($row['active'] ?? 'si') === 'si' && ($row['status'] ?? '') !== 'descartada') {
        $factorCounts[$factor] = ($factorCounts[$factor] ?? 0) + 1;
    }
}
$nextAction = $usableCount === 0
    ? 'Empieza en Buscador: abre portales e inmobiliarias, filtra por ciudad, barrio y tipología, y trae las primeras ofertas verificables.'
    : ($usableCount < $targetTotal
        ? 'Sigue en Captura: completa precio, área, fuente, enlace, fecha y factor 8.4 hasta acercarte a 60 muestras.'
        : 'Pasa a Mapa y Matriz: revisa concentración espacial, duplicados, descartes y datos listos para 8.4.');
?>
<section class="rounded-xl border border-teal-100 bg-teal-50 p-4">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase text-teal-800">Qué hago en este numeral</p>
            <h3 class="mt-2 text-xl font-semibold text-teal-950">Aquí no se calcula todavía: aquí construyes la muestra de mercado</h3>
            <p class="mt-2 max-w-4xl text-sm leading-6 text-teal-950"><?= e($nextAction) ?></p>
            <p class="mt-1 max-w-4xl text-xs font-semibold leading-5 text-teal-800">
                Usa las pestañas en este orden: Buscador, Filtros, Captura, Mapa, Matriz, Variables, Fórmulas y Criterios.
            </p>
        </div>
        <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-teal-800"><?= e((string) $usableCount) ?>/<?= e((string) $targetTotal) ?> muestras</span>
    </div>
    <div class="mt-4 grid gap-3 md:grid-cols-3">
        <article class="rounded-lg bg-white p-3 text-sm font-semibold text-slate-800 shadow-sm">
            1. Buscar fuentes
            <span class="mt-1 block text-xs font-medium leading-5 text-slate-500">Portales + inmobiliarias locales. Abre fuentes y copia enlaces verificables.</span>
        </article>
        <article class="rounded-lg bg-white p-3 text-sm font-semibold text-slate-800 shadow-sm">
            2. Diligenciar comparables
            <span class="mt-1 block text-xs font-medium leading-5 text-slate-500">Llena 60 filas posibles con precio, área, fuente, fecha, factor y observación.</span>
        </article>
        <article class="rounded-lg bg-white p-3 text-sm font-semibold text-slate-800 shadow-sm">
            3. Revisar ubicación
            <span class="mt-1 block text-xs font-medium leading-5 text-slate-500">Marca coordenadas para ver si la muestra sí corresponde al microsector comparable.</span>
        </article>
    </div>
    <div class="mt-4 grid gap-3 sm:grid-cols-3">
        <div class="rounded-lg bg-white px-3 py-2"><p class="text-xs font-bold uppercase text-slate-500">Con datos</p><p class="font-semibold text-slate-950"><?= e((string) $usableCount) ?></p></div>
        <div class="rounded-lg bg-white px-3 py-2"><p class="text-xs font-bold uppercase text-slate-500">Con factor 8.4</p><p class="font-semibold text-slate-950"><?= e((string) array_sum($factorCounts)) ?></p></div>
        <div class="rounded-lg bg-white px-3 py-2"><p class="text-xs font-bold uppercase text-slate-500">Con coordenadas</p><p class="font-semibold text-slate-950"><?= e((string) $georefCount) ?></p></div>
    </div>
</section>

<div class="mt-6 grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
    <section class="rounded-xl border border-blue-100 bg-blue-50 p-4">
        <p class="text-xs font-bold uppercase text-blue-800">Diseño de muestra para 8.3</p>
        <h3 class="mt-2 text-xl font-semibold text-blue-950">Antes de capturar, decide qué factores vas a probar</h3>
        <p class="mt-3 text-sm leading-6 text-blue-950"><?= e((string) ($sampleDesign['minimum_message'] ?? '')) ?></p>
        <div class="mt-4 grid gap-3">
            <?php foreach (($sampleDesign['protocol'] ?? []) as $step): ?>
                <p class="rounded-lg bg-white p-3 text-sm leading-6 text-slate-700"><?= e((string) $step) ?></p>
            <?php endforeach; ?>
        </div>
    </section>
    <section class="rounded-xl border border-emerald-100 bg-emerald-50 p-4">
        <p class="text-xs font-bold uppercase text-emerald-800">Puente hacia 8.4</p>
        <h3 class="mt-2 text-xl font-semibold text-emerald-950">Análisis estadístico robusto</h3>
        <ul class="mt-3 space-y-2 text-sm leading-6 text-emerald-950">
            <?php foreach (($sampleDesign['statistics'] ?? []) as $item): ?>
                <li>- <?= e((string) $item) ?></li>
            <?php endforeach; ?>
        </ul>
        <div class="mt-4 rounded-lg bg-white p-3 text-sm leading-6 text-slate-700">
            <?php foreach (($sampleDesign['next_84'] ?? []) as $item): ?>
                <p><?= e((string) $item) ?></p>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<section class="mt-6 rounded-xl border border-indigo-100 bg-indigo-50 p-4">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <p class="text-xs font-bold uppercase text-indigo-800">Cumplimiento Resolución 941</p>
            <h3 class="mt-2 text-xl font-semibold text-indigo-950">Artículos que gobiernan el método de mercado</h3>
            <p class="mt-2 text-sm leading-6 text-indigo-950">
                Usa estas tarjetas como lista de control antes de pasar a 8.4. No son transcripción literal:
                son una lectura operativa para que la investigación de mercado quede soportada.
            </p>
        </div>
        <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-indigo-800">Arts. 16 a 21</span>
    </div>
    <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
        <?php foreach (($sampleDesign['compliance_articles'] ?? []) as $article): ?>
            <article class="rounded-lg bg-white p-3 text-sm leading-6 text-slate-700">
                <p class="text-xs font-bold uppercase text-indigo-700"><?= e((string) ($article[0] ?? 'Art.')) ?></p>
                <p class="mt-1"><?= e((string) ($article[1] ?? '')) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="mt-6 grid gap-4 lg:grid-cols-[0.9fr_1.1fr]">
    <div class="rounded-xl border border-orange-100 bg-orange-50 p-4">
        <p class="text-xs font-bold uppercase text-orange-800">Construcción de 60 datos</p>
        <h3 class="mt-2 text-xl font-semibold text-orange-950"><?= e((string) $targetTotal) ?> comparables como banco de investigación</h3>
        <div class="mt-3 grid gap-2">
            <?php foreach (($sampleDesign['sample_plan'] ?? []) as $label => $text): ?>
                <p class="rounded-lg bg-white p-3 text-sm leading-6 text-slate-700">
                    <strong><?= e((string) $label) ?>:</strong> <?= e((string) $text) ?>
                </p>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
        <p class="text-xs font-bold uppercase text-slate-500">Factores prioritarios de esta tipología</p>
        <p class="mt-2 text-sm leading-6 text-slate-700">
            Estos son los factores que conviene agotar primero. Si alguno no aplica al caso, el analista lo deja sin uso
            y documenta el motivo en la captura.
        </p>
        <div class="mt-3 grid gap-2 sm:grid-cols-2">
            <?php foreach (($sampleDesign['priority_factors'] ?? []) as $factor): ?>
                <span class="rounded-lg bg-white px-3 py-2 text-sm font-semibold text-slate-700"><?= e((string) $factor) ?></span>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="mt-6 rounded-xl border border-slate-200 bg-white p-4">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <p class="text-xs font-bold uppercase text-slate-500">Factores de análisis</p>
            <h3 class="mt-2 text-xl font-semibold text-slate-950">Meta de muestra por factor</h3>
            <p class="mt-2 text-sm leading-6 text-slate-600">
                Cada muestra capturada se marca con un factor. El tablero ayuda a ver si la investigación ya soporta
                un análisis estadístico o si todavía falta mercado comparable.
            </p>
        </div>
        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700"><?= e((string) $targetPerFactor) ?> muestras por factor</span>
    </div>
    <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
        <?php foreach ($factorTargets as $target): ?>
            <?php
            $key = (string) ($target['key'] ?? '');
            $count = (int) ($factorCounts[$key] ?? 0);
            $ok = $count >= $targetPerFactor;
            ?>
            <article class="rounded-xl border <?= $ok ? 'border-emerald-200 bg-emerald-50' : 'border-slate-200 bg-slate-50' ?> p-4">
                <div class="flex items-start justify-between gap-3">
                    <h4 class="font-semibold text-slate-950"><?= e((string) ($target['label'] ?? 'Factor')) ?></h4>
                    <span class="rounded-full <?= $ok ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' ?> px-2 py-0.5 text-xs font-bold">
                        <?= e((string) $count) ?>/<?= e((string) $targetPerFactor) ?>
                    </span>
                </div>
                <ul class="mt-3 space-y-1 text-xs leading-5 text-slate-600">
                    <?php foreach (array_slice(is_array($target['variables'] ?? null) ? $target['variables'] : [], 0, 5) as $variable): ?>
                        <li>- <?= e((string) $variable) ?></li>
                    <?php endforeach; ?>
                </ul>
            </article>
        <?php endforeach; ?>
    </div>
</section>
