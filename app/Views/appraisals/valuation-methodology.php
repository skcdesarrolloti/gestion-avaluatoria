<?php
$currentStep = 'metodologia';
$captured = $guide['captured'] ?? [];
$factorGroups = $guide['factor_groups'] ?? [];
$methodologyChapterData = is_array($methodologyChapter ?? null) ? $methodologyChapter : ['sections' => [], 'text' => '', 'references' => []];
$methodologyText = (string) ($methodologyChapterData['text'] ?? '');
$methodologySections = is_array($methodologyChapterData['sections'] ?? null) ? $methodologyChapterData['sections'] : [];
$methodologyReferences = is_array($methodologyChapterData['references'] ?? null) ? $methodologyChapterData['references'] : [];
$methodologyGuides = is_array($methodologyChapterData['method_guides'] ?? null) ? $methodologyChapterData['method_guides'] : [];
$methodologyDecision = is_array($methodologyChapterData['decision'] ?? null) ? $methodologyChapterData['decision'] : ['rows' => []];
$methodologyDecisionRows = is_array($methodologyDecision['rows'] ?? null) ? $methodologyDecision['rows'] : [];
$methodologyMessage = \App\Core\Session::pullFlash('methodology_message');
$methodologyError = \App\Core\Session::pullFlash('methodology_error');
?>
<a href="<?= e(url('valuaciones')) ?>" class="inline-flex min-h-11 items-center text-sm font-medium text-teal-800">← Valuaciones</a>
<div class="mt-3 flex flex-wrap items-start justify-between gap-5">
    <div>
        <p class="eyebrow">Numeral 8 · Metodología valuatoria</p>
        <h1 class="mt-2 text-3xl font-semibold">Marco académico y selección metodológica</h1>
        <p class="mt-3 max-w-3xl text-slate-600">
            Primero se ambienta al lector con la academia normativa vigente. Después se desarrolla el método
            seleccionado para el caso; la búsqueda de comparables o insumos queda en el desarrollo del 8.3.
        </p>
    </div>
    <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-800">Numeral 8</span>
</div>
<?php require BASE_PATH . '/app/Views/appraisals/step-nav.php'; ?>
<?php if ($methodologyMessage): ?>
    <p class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800"><?= e($methodologyMessage) ?></p>
<?php endif; ?>
<?php if ($methodologyError): ?>
    <p class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-800"><?= e($methodologyError) ?></p>
<?php endif; ?>

<div class="mt-8" x-data="{ methodologyTab: '81' }"
    data-refresh-on-save-topic="<?= e('appraisal:' . $record['id'] . ':chapter-zero,appraisal:' . $record['id'] . ':subject-units') ?>">
    <p class="hidden rounded-xl bg-emerald-50 p-4 text-sm font-semibold text-emerald-800" data-refresh-message>
        Se actualizó información que alimenta el numeral 8.2. Recargando metodología con la información guardada...
    </p>
    <div class="rounded-2xl bg-slate-100 p-2">
        <div class="flex gap-2 overflow-x-auto">
            <button type="button" class="min-h-11 shrink-0 rounded-xl px-4 py-2 text-sm font-semibold"
                :class="methodologyTab === '81' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600'"
                @click="methodologyTab = '81'">8.1 Marco académico</button>
            <button type="button" class="min-h-11 shrink-0 rounded-xl px-4 py-2 text-sm font-semibold"
                :class="methodologyTab === '82' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600'"
                @click="methodologyTab = '82'">8.2 Matriz y método</button>
            <button type="button" class="min-h-11 shrink-0 rounded-xl px-4 py-2 text-sm font-semibold"
                :class="methodologyTab === '83' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600'"
                @click="methodologyTab = '83'">8.3 Desarrollo operativo</button>
        </div>
    </div>

<section x-show="methodologyTab === '81'" class="mt-6 rounded-2xl border border-indigo-100 bg-indigo-50 p-6 shadow-sm sm:p-8">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">8.1 Academia valuatoria</p>
            <h2 class="mt-2 text-2xl font-semibold text-indigo-950">Texto base que pasa al entregable</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-indigo-900">
                Actualizado con Resolución IGAC 941 de 2026. La Resolución 620 queda como antecedente, no como
                regla principal. IVS, NIIF y NTS se citan como marco complementario según la finalidad del encargo.
            </p>
        </div>
        <a class="rounded-full bg-white px-4 py-2 text-sm font-bold text-indigo-800" target="_blank" rel="noopener" href="https://www.igac.gov.co/node/53595">Fuente IGAC 941</a>
    </div>
    <?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-method-guides.php'; ?>
    <textarea class="input mt-5 min-h-80 bg-white font-mono text-sm leading-6" rows="18" readonly><?= e($methodologyText) ?></textarea>
    <div class="mt-5 grid gap-4 lg:grid-cols-2">
        <?php foreach ($methodologySections as $section): ?>
            <article class="rounded-xl border border-white/80 bg-white/80 p-4 text-sm leading-6">
                <h3 class="font-semibold text-slate-950"><?= e((string) ($section[0] ?? 'Sección')) ?></h3>
                <p class="mt-2 whitespace-pre-wrap text-slate-700"><?= e((string) ($section[1] ?? '')) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
    <?php if ($methodologyReferences !== []): ?>
        <div class="mt-5 grid gap-3 md:grid-cols-2 xl:grid-cols-5">
            <?php foreach ($methodologyReferences as $reference): ?>
                <article class="rounded-xl border border-indigo-100 bg-white p-4 text-sm leading-5">
                    <p class="text-xs font-bold uppercase text-indigo-700"><?= e((string) ($reference[0] ?? 'Referencia')) ?></p>
                    <h3 class="mt-2 font-semibold text-slate-950"><?= e((string) ($reference[1] ?? '')) ?></h3>
                    <p class="mt-2 text-xs leading-5 text-slate-600"><?= e((string) ($reference[2] ?? '')) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<div x-show="methodologyTab === '82'" class="mt-6 space-y-8">
<?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-decision.php'; ?>
</div>

<div x-show="methodologyTab === '83'" class="mt-6 space-y-8">
<?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-search.php'; ?>
<section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Insumos disponibles</p>
            <h2 class="mt-2 text-2xl font-semibold">Datos del sujeto que ya orientan la búsqueda</h2>
        </div>
        <a class="btn-secondary min-h-11" href="<?= e(url('avaluos/' . $record['id'] . '/bien-sujeto')) ?>">Revisar bien sujeto</a>
    </div>
    <?php if ($captured === []): ?>
        <p class="mt-5 rounded-xl border border-dashed border-slate-300 p-4 text-sm text-slate-600">
            Aún faltan datos del sujeto para convertirlos en criterios de búsqueda. Completa el numeral 3.
        </p>
    <?php else: ?>
        <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($captured as $item): ?>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase text-slate-500"><?= e($item['label']) ?></p>
                    <p class="mt-1 text-sm font-semibold text-slate-900"><?= e($item['value']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
</div>
</div>
