<?php
$currentStep = 'metodologia';
$captured = $guide['captured'] ?? [];
?>
<a href="<?= e(url('valuaciones')) ?>" class="inline-flex min-h-11 items-center text-sm font-medium text-teal-800">← Valuaciones</a>
<div class="mt-3 flex flex-wrap items-start justify-between gap-5">
    <div>
        <p class="eyebrow">Numeral 8 · Metodología valuatoria</p>
        <h1 class="mt-2 text-3xl font-semibold">Lineamiento para búsqueda de comparables</h1>
        <p class="mt-3 max-w-3xl text-slate-600">
            Este módulo convierte la descripción del bien sujeto en criterios operativos para buscar muestras
            de mercado semejantes. No calcula valores; prepara la base técnica para comparar con mayor homogeneidad.
        </p>
    </div>
    <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-800">Numeral 8</span>
</div>
<?php require BASE_PATH . '/app/Views/appraisals/step-nav.php'; ?>

<section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
    <div class="grid gap-4 lg:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <p class="eyebrow">Bien sujeto</p>
            <h2 class="mt-2 text-xl font-semibold"><?= e($guide['type_label'] ?? 'Tipología pendiente') ?></h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">
                <?= e(trim((string) ($record['titulo'] ?? '')) ?: 'Ficha sin título definido') ?>
            </p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <p class="eyebrow">Mercado comparable</p>
            <h2 class="mt-2 text-xl font-semibold"><?= e($guide['business_label'] ?: 'Tipo de negocio pendiente') ?></h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">
                La búsqueda debe conservar el mismo mercado: venta, renta u otro enfoque definido por el encargo.
            </p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <p class="eyebrow">Derecho valorado</p>
            <h2 class="mt-2 text-xl font-semibold"><?= e($guide['right_label'] ?: 'Derecho pendiente') ?></h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">
                Si el derecho jurídico no coincide, la muestra requiere observación técnica antes de homologarse.
            </p>
        </div>
    </div>

    <div class="mt-6 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950">
        <strong>Lectura técnica:</strong> los comparables deben parecerse primero en tipología, uso, localización,
        derecho, fecha y unidad de comparación. Las diferencias inevitables se documentan para homologación.
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <div class="rounded-xl border border-slate-200 bg-white">
            <div class="rounded-t-xl bg-blue-900 px-4 py-3 text-sm font-semibold uppercase text-white">Criterios de búsqueda</div>
            <ol class="space-y-3 p-4 text-sm leading-6 text-slate-700">
                <?php foreach (($guide['criteria'] ?? []) as $index => $criterion): ?>
                    <li class="flex gap-3">
                        <span class="mt-1 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-semibold text-blue-800"><?= e((string) ($index + 1)) ?></span>
                        <span><?= e($criterion) ?></span>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
        <div class="space-y-6">
            <div class="rounded-xl border border-amber-200 bg-amber-50">
                <div class="rounded-t-xl bg-amber-600 px-4 py-3 text-sm font-semibold uppercase text-white">Evitar al seleccionar muestras</div>
                <ul class="space-y-3 p-4 text-sm leading-6 text-amber-950">
                    <?php foreach (($guide['avoid'] ?? []) as $item): ?>
                        <li>- <?= e($item) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="rounded-xl border border-emerald-200 bg-emerald-50">
                <div class="rounded-t-xl bg-emerald-700 px-4 py-3 text-sm font-semibold uppercase text-white">Homologación posterior</div>
                <ul class="space-y-3 p-4 text-sm leading-6 text-emerald-950">
                    <?php foreach (($guide['homologation'] ?? []) as $item): ?>
                        <li>- <?= e($item) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
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
