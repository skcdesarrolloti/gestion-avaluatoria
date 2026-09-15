<?php
$activeCategoryCode = $activeCategoryCode ?? ($categories[0]['code'] ?? 'A');
$legalStats = $legalStats ?? ['total' => 0, 'vigente' => 0, 'derogada' => 0, 'historica' => 0];
?>
<section class="space-y-7" x-data="{ query: '' }">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-teal-800">Biblioteca jurídica</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-950">Marco Jurídico Valuatorio</h1>
            <p class="mt-3 max-w-3xl text-slate-600">Consulta leyes, decretos, resoluciones y actos relacionados con cada categoría valuatoria. También se conservarán documentos derogados como referencia histórica.</p>
        </div>
        <label class="block min-w-full text-sm font-medium text-slate-700 lg:min-w-80">
            Buscar documento
            <input class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-slate-950 shadow-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-700/20"
                type="search" placeholder="Ley, decreto, resolución o título" x-model.trim="query">
        </label>
    </div>
    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-950">Repositorio jurídico</h2>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">La estructura está lista para clasificar normativa común en A o B, y normativa específica en las categorías 1 a 13. Cuando entregues las leyes, las cargamos con tipo, estado, fecha y fuente.</p>
        <div class="mt-4 flex flex-wrap gap-2 text-xs font-semibold">
            <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600"><?= e($legalStats['total']) ?> documento(s)</span>
            <span class="rounded-full bg-emerald-50 px-3 py-1 text-emerald-700"><?= e($legalStats['vigente']) ?> vigente(s)</span>
            <span class="rounded-full bg-amber-50 px-3 py-1 text-amber-700"><?= e($legalStats['derogada']) ?> derogada(s)</span>
            <span class="rounded-full bg-blue-50 px-3 py-1 text-blue-700"><?= e($legalStats['historica']) ?> histórica(s)</span>
        </div>
    </section>
    <nav class="rounded-lg bg-slate-200/70 p-2" aria-label="Categorías jurídicas">
        <div class="flex gap-2 overflow-x-auto">
            <?php foreach ($categories as $category): ?>
                <?php $isActive = $category['code'] === $activeCategoryCode; ?>
                <a class="inline-flex min-h-11 shrink-0 items-center gap-2 rounded-md px-3 py-2 text-xs font-semibold transition <?= $isActive ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:bg-white/70' ?>"
                    href="<?= e(url('marco-juridico-valuatorio?categoria=' . rawurlencode((string) $category['code']))) ?>"
                    <?= $isActive ? 'aria-current="page"' : '' ?>>
                    <span class="inline-flex size-7 items-center justify-center rounded-full bg-teal-800 text-xs text-white"><?= e($category['code']) ?></span>
                    <span class="max-w-44 truncate"><?= e($category['name']) ?></span>
                    <span class="rounded-full bg-white/80 px-2 py-0.5 text-[11px] text-slate-500"><?= count($category['documents']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </nav>
    <?php foreach ($categories as $category): ?>
        <?php if ($category['code'] !== $activeCategoryCode) { continue; } ?>
        <section class="pt-2">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-xl font-semibold text-slate-950">
                    <span class="mr-2 inline-flex size-9 items-center justify-center rounded-full bg-teal-800 text-sm text-white"><?= e($category['code']) ?></span>
                    <?= e($category['name']) ?>
                </h2>
                <span class="text-sm text-slate-500"><?= count($category['documents']) ?> documento(s)</span>
            </div>
            <?php if ($category['documents']): ?>
                <div class="mt-4 grid gap-3 md:grid-cols-2">
                    <?php foreach ($category['documents'] as $document): ?>
                        <?php $term = mb_strtolower(implode(' ', [$document['document_code'], $document['title'], $document['document_type'], $document['status']])); ?>
                        <article class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm"
                            x-show='query === "" || <?= e(json_encode($term, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>.includes(query.toLowerCase())'>
                            <div class="flex items-start justify-between gap-3">
                                <div><p class="text-sm font-semibold text-teal-800"><?= e($document['document_code']) ?></p>
                                    <h3 class="mt-1 font-semibold text-slate-950"><?= e($document['title']) ?></h3></div>
                                <span class="shrink-0 rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600"><?= e($document['document_type']) ?></span>
                            </div>
                            <p class="mt-3 text-sm text-slate-500"><?= e($document['summary'] ?: 'Sin resumen cargado.') ?></p>
                            <p class="mt-4 text-xs font-medium text-amber-700"><?= e(ucfirst((string) $document['status'])) ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="mt-4 rounded-lg border border-dashed border-slate-300 bg-white p-4 text-sm text-slate-500">Sin documentos jurídicos asignados por ahora.</p>
            <?php endif; ?>
        </section>
    <?php endforeach; ?>
</section>
