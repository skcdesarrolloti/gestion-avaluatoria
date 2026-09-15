<?php
$activeGroupCode = $activeGroupCode ?? ($groups[0]['code'] ?? 'G');
$stats = $stats ?? ['total' => 0, 'vigente' => 0, 'available' => 0, 'missing' => 0];
$storageReport = $storageReport ?? ['dir' => '', 'configured' => false, 'writable' => false,
    'present' => 0, 'total' => 0, 'marked_missing' => 0, 'limits' => []];
?>
<section class="space-y-7" x-data="{ query: '' }">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-teal-800">Referencia contable</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-950">Normas NIIF</h1>
            <p class="mt-3 max-w-3xl text-slate-600">Consulta las normas contables que pueden afectar medición, deterioro, revelación y bases de valor en encargos valuatorios con finalidad financiera.</p>
        </div>
        <label class="block min-w-full text-sm font-medium text-slate-700 lg:min-w-80">
            Buscar NIIF
            <input class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-slate-950 shadow-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-700/20"
                type="search" placeholder="NIIF, NIC, activo o campo" x-model.trim="query">
        </label>
    </div>
    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-950">Biblioteca NIIF</h2>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Las NIIF no sustituyen las NTS ni las IVS; se citan cuando el encargo tenga medición contable, valor razonable, deterioro, arrendamiento, activos financieros o revelación financiera. Cada norma tiene su propia caja para subir el PDF correspondiente.</p>
        <div class="mt-4 flex flex-wrap gap-2 text-xs font-semibold">
            <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600"><?= e($stats['total']) ?> norma(s)</span>
            <span class="rounded-full bg-teal-50 px-3 py-1 text-teal-700"><?= e($stats['available']) ?> PDF cargado(s)</span>
            <span class="rounded-full bg-emerald-50 px-3 py-1 text-emerald-700"><?= e($stats['vigente']) ?> vigente(s)</span>
        </div>
        <?php if (!empty($importNotice)): ?>
            <?php $ok = (bool) ($importNotice['ok'] ?? false); ?>
            <div class="mt-4 rounded-lg <?= $ok ? 'bg-teal-50 text-teal-900' : 'bg-red-50 text-red-800' ?> p-4 text-sm">
                <p><?= $ok ? count($importNotice['copied'] ?? []) . ' importado(s), ' . count($importNotice['skipped'] ?? []) . ' sin cambios.' : e($importNotice['message'] ?? 'No se pudo importar.') ?></p>
            </div>
        <?php endif; ?>
        <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">
                <div><p class="font-semibold text-slate-950">Diagnóstico de guardado NIIF</p>
                    <p class="mt-1 break-words text-slate-600">Ruta: <?= e($storageReport['dir']) ?></p></div>
                <span class="rounded-full <?= $storageReport['writable'] ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' ?> px-3 py-1 text-xs font-semibold">
                    <?= $storageReport['writable'] ? 'Carpeta escribible' : 'Sin permiso de escritura' ?>
                </span>
            </div>
            <dl class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div><dt class="text-xs font-semibold uppercase text-slate-500">Modo</dt><dd><?= $storageReport['configured'] ? 'IFRS_STORAGE_DIR' : 'storage/ del proyecto' ?></dd></div>
                <div><dt class="text-xs font-semibold uppercase text-slate-500">Archivos físicos</dt><dd><?= e($storageReport['present']) ?> / <?= e($storageReport['total']) ?></dd></div>
                <div><dt class="text-xs font-semibold uppercase text-slate-500">BD sin archivo</dt><dd><?= e($storageReport['marked_missing']) ?></dd></div>
                <div><dt class="text-xs font-semibold uppercase text-slate-500">Pendientes</dt><dd><?= e($stats['missing']) ?> PDF</dd></div>
            </dl>
        </div>
    </section>
    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-950">Consideraciones del expediente</h2>
        <div class="mt-4 grid gap-3 md:grid-cols-2">
            <?php foreach ($considerations as $item): ?>
                <?php $term = mb_strtolower(implode(' ', $item)); ?>
                <article class="rounded-lg border border-slate-200 p-4" x-show='query === "" || <?= e(json_encode($term, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>.includes(query.toLowerCase())'>
                    <div class="flex items-start justify-between gap-3">
                        <h3 class="font-semibold text-slate-950"><?= e($item['field_label']) ?></h3>
                        <span class="shrink-0 rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600"><?= e($item['classification']) ?></span>
                    </div>
                    <p class="mt-3 text-sm leading-6 text-slate-600"><?= e($item['operational_use']) ?></p>
                    <p class="mt-3 text-xs font-semibold uppercase text-slate-500">Base: <?= e($item['normative_basis']) ?></p>
                    <p class="mt-2 text-xs text-teal-800"><?= e($item['ifrs_relation']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <nav class="rounded-lg bg-slate-200/70 p-2" aria-label="Grupos NIIF">
        <div class="flex gap-2 overflow-x-auto">
            <?php foreach ($groups as $group): ?>
                <?php $isActive = $group['code'] === $activeGroupCode; ?>
                <a class="inline-flex min-h-11 shrink-0 items-center gap-2 rounded-md px-3 py-2 text-xs font-semibold transition <?= $isActive ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:bg-white/70' ?>"
                    href="<?= e(url('normas-niif?grupo=' . rawurlencode((string) $group['code']))) ?>" <?= $isActive ? 'aria-current="page"' : '' ?>>
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
                <h2 class="text-xl font-semibold text-slate-950"><span class="mr-2 inline-flex size-9 items-center justify-center rounded-full bg-teal-800 text-sm text-white"><?= e($group['code']) ?></span><?= e($group['name']) ?></h2>
                <span class="text-sm text-slate-500"><?= count($group['standards']) ?> norma(s)</span>
            </div>
            <div class="mt-4 grid gap-3 md:grid-cols-2">
                <?php foreach ($group['standards'] as $standard): ?>
                    <?php $term = mb_strtolower(implode(' ', [$standard['standard_code'], $standard['title'], $standard['applicable_categories'], $standard['measurement_focus'], $standard['summary'], $standard['field_relevance']])); ?>
                    <article class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm" x-show='query === "" || <?= e(json_encode($term, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>.includes(query.toLowerCase())'>
                        <div class="flex items-start justify-between gap-3">
                            <div><p class="text-anywhere text-sm font-semibold text-teal-800"><?= e($standard['standard_code']) ?></p>
                                <h3 class="text-anywhere mt-1 font-semibold text-slate-950"><?= e($standard['title']) ?></h3></div>
                            <span class="shrink-0 rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700"><?= e($standard['status']) ?></span>
                        </div>
                        <p class="mt-3 text-sm font-medium text-slate-600"><?= e($standard['measurement_focus']) ?></p>
                        <p class="mt-2 text-sm leading-6 text-slate-600"><?= e($standard['summary']) ?></p>
                        <p class="mt-3 text-xs text-teal-800"><?= e($standard['field_relevance']) ?></p>
                        <p class="mt-3 text-xs font-medium text-slate-600">Categorías RAA: <?= e($standard['applicable_categories']) ?></p>
                        <p class="text-anywhere mt-2 text-xs text-slate-500"><?= e($standard['source_reference']) ?></p>
                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                            <span class="text-xs font-medium <?= $standard['has_file'] ? 'text-emerald-700' : 'text-amber-700' ?>"><?= $standard['has_file'] ? 'PDF disponible' : 'Pendiente de archivo' ?></span>
                            <?php if ($standard['has_file']): ?>
                                <a class="btn-secondary" target="_blank" rel="noopener" href="<?= e(url('normas-niif/' . $standard['slug'] . '/archivo')) ?>">Abrir PDF</a>
                            <?php endif; ?>
                        </div>
                        <form class="mt-4 grid gap-2 border-t border-slate-100 pt-4" method="post" action="<?= e(url('normas-niif/' . $standard['slug'] . '/importar')) ?>" enctype="multipart/form-data" x-data="{ busy: false }" @submit="busy = true">
                            <?= csrf_field() ?>
                            <label class="block text-xs font-semibold uppercase text-slate-500">PDF de <?= e($standard['standard_code']) ?>
                                <input class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700" type="file" name="ifrs_file[]" accept="application/pdf,.pdf" required>
                            </label>
                            <button class="btn-primary w-full" type="submit" :disabled="busy" x-text="busy ? 'Subiendo PDF…' : '<?= $standard['has_file'] ? 'Reemplazar PDF NIIF' : 'Subir PDF NIIF' ?>'"><?= $standard['has_file'] ? 'Reemplazar PDF NIIF' : 'Subir PDF NIIF' ?></button>
                        </form>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>
</section>
