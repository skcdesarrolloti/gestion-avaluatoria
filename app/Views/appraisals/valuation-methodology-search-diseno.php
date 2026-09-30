<?php
$targetPerFactor = (int) ($sampleDesign['target_per_factor'] ?? 15);
$sampleRows = is_array($comparableRows ?? null) ? $comparableRows : [];
$factorCounts = [];
foreach ($sampleRows as $row) {
    $factor = (string) ($row['analysis_factor'] ?? '');
    if ($factor !== '' && ($row['active'] ?? 'si') === 'si' && ($row['status'] ?? '') !== 'descartada') {
        $factorCounts[$factor] = ($factorCounts[$factor] ?? 0) + 1;
    }
}
?>
<div class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
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
