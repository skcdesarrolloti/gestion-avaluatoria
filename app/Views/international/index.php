<?php
$activeGroupCode = $activeGroupCode ?? ($groups[0]['code'] ?? 'G');
$stats = $stats ?? ['total' => 0, 'vigente' => 0, 'historica' => 0];
?>
<section class="space-y-7" x-data="{ query: '' }">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-teal-800">Referencia internacional</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-950">Normas Internacionales de Valuación</h1>
            <p class="mt-3 max-w-3xl text-slate-600">Consulta la estructura IVS separada del marco jurídico colombiano, relacionada con las categorías RAA cuando aplique.</p>
        </div>
        <label class="block min-w-full text-sm font-medium text-slate-700 lg:min-w-80">
            Buscar IVS
            <input class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-slate-950 shadow-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-700/20"
                type="search" placeholder="IVS, activo o categoría RAA" x-model.trim="query">
        </label>
    </div>
    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-950">Biblioteca IVS</h2>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">La edición efectiva desde el 31 de enero de 2025 queda registrada como referencia técnica. No se mezcla con leyes, decretos ni resoluciones nacionales.</p>
        <div class="mt-4 flex flex-wrap gap-2 text-xs font-semibold">
            <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600"><?= e($stats['total']) ?> norma(s)</span>
            <span class="rounded-full bg-emerald-50 px-3 py-1 text-emerald-700"><?= e($stats['vigente']) ?> vigente(s)</span>
            <span class="rounded-full bg-blue-50 px-3 py-1 text-blue-700"><?= e($stats['historica']) ?> histórica(s)</span>
        </div>
    </section>
    <nav class="rounded-lg bg-slate-200/70 p-2" aria-label="Grupos IVS">
        <div class="flex gap-2 overflow-x-auto">
            <?php foreach ($groups as $group): ?>
                <?php $isActive = $group['code'] === $activeGroupCode; ?>
                <a class="inline-flex min-h-11 shrink-0 items-center gap-2 rounded-md px-3 py-2 text-xs font-semibold transition <?= $isActive ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:bg-white/70' ?>"
                    href="<?= e(url('normas-internacionales-valuacion?grupo=' . rawurlencode((string) $group['code']))) ?>"
                    <?= $isActive ? 'aria-current="page"' : '' ?>>
                    <span class="inline-flex size-7 items-center justify-center rounded-full bg-teal-800 text-xs text-white"><?= e($group['code']) ?></span>
                    <span class="max-w-44 truncate"><?= e($group['name']) ?></span>
                    <span class="rounded-full bg-white/80 px-2 py-0.5 text-[11px] text-slate-500"><?= count($group['standards']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </nav>
    <?php foreach ($groups as $group): ?>
        <?php if ($group['code'] !== $activeGroupCode) { continue; } ?>
        <section class="pt-2">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-xl font-semibold text-slate-950">
                    <span class="mr-2 inline-flex size-9 items-center justify-center rounded-full bg-teal-800 text-sm text-white"><?= e($group['code']) ?></span>
                    <?= e($group['name']) ?>
                </h2>
                <span class="text-sm text-slate-500"><?= count($group['standards']) ?> norma(s)</span>
            </div>
            <div class="mt-4 grid gap-3 md:grid-cols-2">
                <?php foreach ($group['standards'] as $standard): ?>
                    <?php $term = mb_strtolower(implode(' ', [$standard['standard_code'], $standard['title'], $standard['applicable_categories'], $standard['summary'], $standard['source_reference']])); ?>
                    <article class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm"
                        x-show='query === "" || <?= e(json_encode($term, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>.includes(query.toLowerCase())'>
                        <div class="flex items-start justify-between gap-3">
                            <div><p class="text-anywhere text-sm font-semibold text-teal-800"><?= e($standard['standard_code']) ?></p>
                                <h3 class="text-anywhere mt-1 font-semibold text-slate-950"><?= e($standard['title']) ?></h3></div>
                            <span class="shrink-0 rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700"><?= e($standard['status']) ?></span>
                        </div>
                        <p class="mt-3 text-sm text-slate-500"><?= e($standard['summary']) ?></p>
                        <?php if ($standard['source_reference'] !== ''): ?>
                            <p class="text-anywhere mt-3 text-xs text-slate-500"><?= e($standard['source_reference']) ?></p>
                        <?php endif; ?>
                        <p class="mt-4 text-xs font-medium text-slate-600">Categorías RAA: <?= e($standard['applicable_categories']) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>
</section>
