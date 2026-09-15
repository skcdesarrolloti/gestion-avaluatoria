<?php
$activeCategoryCode = $activeCategoryCode ?? ($categories[0]['code'] ?? 'A');
$legalStats = $legalStats ?? ['total' => 0, 'articles' => 0, 'vigente' => 0, 'derogada' => 0, 'historica' => 0];
$storageReport = $storageReport ?? ['dir' => '', 'configured' => false, 'writable' => false,
    'present' => 0, 'total' => 0, 'marked_missing' => 0, 'limits' => []];
$limits = $storageReport['limits'];
$maxFiles = max(0, (int) ($limits['max_file_uploads'] ?? 0));
?>
<section class="space-y-7" x-data="{ query: '' }">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-teal-800">Biblioteca jurídica</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-950">Marco Jurídico Nacional</h1>
            <p class="mt-3 max-w-3xl text-slate-600">Consulta leyes, decretos, resoluciones y actos colombianos relacionados con cada categoría valuatoria. Las normas derogadas se conservarán como referencia histórica.</p>
        </div>
        <label class="block min-w-full text-sm font-medium text-slate-700 lg:min-w-80">
            Buscar documento
            <input class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-slate-950 shadow-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-700/20"
                type="search" placeholder="Ley, decreto, resolución o título" x-model.trim="query">
        </label>
    </div>
    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="grid gap-5 lg:grid-cols-[1fr_380px]">
            <div>
                <h2 class="text-lg font-semibold text-slate-950">Repositorio jurídico</h2>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Cada ley o decreto se registra como documento fuente, pero por categoría solo se cargan artículos o fragmentos relevantes con su nota de aplicación.</p>
                <div class="mt-4 flex flex-wrap gap-2 text-xs font-semibold">
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600"><?= e($legalStats['total']) ?> documento(s)</span>
                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-indigo-700"><?= e($legalStats['articles']) ?> artículo(s)</span>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-emerald-700"><?= e($legalStats['vigente']) ?> vigente(s)</span>
                    <span class="rounded-full bg-amber-50 px-3 py-1 text-amber-700"><?= e($legalStats['derogada']) ?> derogada(s)</span>
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-blue-700"><?= e($legalStats['historica']) ?> histórica(s)</span>
                </div>
            </div>
            <form class="grid gap-3" method="post" action="<?= e(url('marco-juridico-valuatorio/importar')) ?>" enctype="multipart/form-data" x-data="{ busy: false }" @submit="busy = true">
                <?= csrf_field() ?>
                <label class="block text-sm font-medium text-slate-700">
                    Categoría jurídica
                    <select class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-3" name="categoria" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= e($category['code']) ?>" <?= $category['code'] === $activeCategoryCode ? 'selected' : '' ?>><?= e($category['code'] . ' · ' . $category['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="block text-sm font-medium text-slate-700">
                    Estado
                    <select class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-3" name="estado" required>
                        <option value="vigente">Vigente</option>
                        <option value="derogada">Derogada</option>
                        <option value="historica">Histórica</option>
                    </select>
                </label>
                <label class="block text-sm font-medium text-slate-700">
                    PDFs jurídicos o ZIP
                    <input class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700"
                        type="file" name="legal_files[]" accept="application/pdf,.pdf,application/zip,.zip" multiple required>
                    <span class="mt-1 block text-xs text-slate-500">Puedes subir varios PDFs o un ZIP con PDFs; los artículos relevantes se clasifican después.</span>
                </label>
                <button class="btn-primary" type="submit" :disabled="busy" x-text="busy ? 'Importando…' : 'Importar documentos'">Importar documentos</button>
            </form>
        </div>
        <?php if (!empty($importNotice)): ?>
            <?php $ok = (bool) ($importNotice['ok'] ?? false); ?>
            <div class="mt-4 rounded-lg <?= $ok ? 'bg-teal-50 text-teal-900' : 'bg-red-50 text-red-800' ?> p-4 text-sm">
                <?php if (!$ok): ?>
                    <p><?= e($importNotice['message'] ?? 'No se pudo importar.') ?></p>
                <?php else: ?>
                    <p><?= count($importNotice['copied'] ?? []) ?> importado(s), <?= count($importNotice['skipped'] ?? []) ?> sin cambios.</p>
                    <?php if (!empty($importNotice['errors'])): ?>
                        <ul class="mt-2 list-disc space-y-1 pl-5">
                            <?php foreach (array_slice($importNotice['errors'], 0, 5) as $error): ?>
                                <li><?= e($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="font-semibold text-slate-950">Diagnóstico de guardado jurídico</p>
                    <p class="mt-1 break-words text-slate-600">Ruta: <?= e($storageReport['dir']) ?></p>
                </div>
                <span class="rounded-full <?= $storageReport['writable'] ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' ?> px-3 py-1 text-xs font-semibold">
                    <?= $storageReport['writable'] ? 'Carpeta escribible' : 'Sin permiso de escritura' ?>
                </span>
            </div>
            <dl class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div><dt class="text-xs font-semibold uppercase text-slate-500">Modo</dt><dd><?= $storageReport['configured'] ? 'LEGAL_STORAGE_DIR' : 'Storage interno' ?></dd></div>
                <div><dt class="text-xs font-semibold uppercase text-slate-500">Archivos físicos</dt><dd><?= e($storageReport['present']) ?> / <?= e($storageReport['total']) ?></dd></div>
                <div><dt class="text-xs font-semibold uppercase text-slate-500">BD sin archivo</dt><dd><?= e($storageReport['marked_missing']) ?></dd></div>
                <div><dt class="text-xs font-semibold uppercase text-slate-500">Límite por lote</dt><dd><?= e($maxFiles ?: ($limits['max_file_uploads'] ?? '')) ?> archivo(s)</dd></div>
            </dl>
            <?php if (!$storageReport['configured']): ?>
                <p class="mt-3 rounded-lg bg-amber-50 p-3 text-sm text-amber-800">Configura LEGAL_STORAGE_DIR en una carpeta privada persistente fuera del checkout antes de cargar bibliotecas jurídicas grandes.</p>
            <?php endif; ?>
            <?php if ((int) $storageReport['marked_missing'] > 0): ?>
                <p class="mt-3 rounded-lg bg-red-50 p-3 text-sm text-red-800">Hay documentos marcados en base, pero el PDF no existe en disco. Revisa la ruta persistente antes de seguir cargando.</p>
            <?php endif; ?>
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
                    <span class="rounded-full bg-white/80 px-2 py-0.5 text-[11px] text-slate-500"><?= count($category['articles']) ?>/<?= count($category['documents']) ?></span>
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
                <span class="text-sm text-slate-500"><?= count($category['articles']) ?> artículo(s), <?= count($category['documents']) ?> documento(s)</span>
            </div>
            <?php if ($category['documents']): ?>
                <h3 class="mt-5 text-sm font-semibold uppercase text-slate-500">Documentos fuente</h3>
                <div class="mt-3 grid gap-3 md:grid-cols-2">
                    <?php foreach ($category['documents'] as $document): ?>
                        <?php $term = mb_strtolower(implode(' ', [$document['document_code'], $document['title'], $document['document_type'], $document['status'], $document['source_filename']])); ?>
                        <article class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm"
                            x-show='query === "" || <?= e(json_encode($term, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>.includes(query.toLowerCase())'>
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-teal-800"><?= e($document['document_code']) ?></p>
                                    <h3 class="mt-1 font-semibold text-slate-950"><?= e($document['title']) ?></h3>
                                </div>
                                <span class="shrink-0 rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600"><?= e($document['document_type']) ?></span>
                            </div>
                            <p class="mt-3 text-sm text-slate-500"><?= e($document['source_filename'] ?: $document['source_reference']) ?></p>
                            <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                                <span class="text-xs font-medium <?= $document['has_file'] ? 'text-emerald-700' : 'text-amber-700' ?>"><?= $document['has_file'] ? 'PDF disponible' : 'Pendiente de archivo' ?></span>
                                <?php if ($document['has_file']): ?>
                                    <a class="btn-secondary" target="_blank" rel="noopener" href="<?= e(url('marco-juridico-valuatorio/' . $document['slug'] . '/archivo')) ?>">Abrir PDF</a>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if ($category['articles']): ?>
                <h3 class="mt-5 text-sm font-semibold uppercase text-slate-500">Artículos y fragmentos</h3>
                <div class="mt-4 grid gap-3 md:grid-cols-2">
                    <?php foreach ($category['articles'] as $article): ?>
                        <?php $term = mb_strtolower(implode(' ', [$article['document_code'], $article['article_label'], $article['title'], $article['excerpt'], $article['status']])); ?>
                        <article class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm"
                            x-show='query === "" || <?= e(json_encode($term, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>.includes(query.toLowerCase())'>
                            <div class="flex items-start justify-between gap-3">
                                <div><p class="text-sm font-semibold text-teal-800"><?= e($article['document_code']) ?> · <?= e($article['article_label']) ?></p>
                                    <h3 class="mt-1 font-semibold text-slate-950"><?= e($article['title'] ?: $article['document_title']) ?></h3></div>
                                <span class="shrink-0 rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600"><?= e($article['document_type']) ?></span>
                            </div>
                            <p class="mt-3 text-sm leading-6 text-slate-600"><?= e($article['excerpt']) ?></p>
                            <p class="mt-4 text-xs text-slate-500"><?= e($article['applicability'] ?: 'Aplicación pendiente de documentar.') ?></p>
                            <p class="mt-4 text-xs font-medium text-amber-700"><?= e(ucfirst((string) $article['status'])) ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php elseif (!$category['documents']): ?>
                <p class="mt-4 rounded-lg border border-dashed border-slate-300 bg-white p-4 text-sm text-slate-500">Sin artículos jurídicos asignados por ahora.</p>
            <?php endif; ?>
        </section>
    <?php endforeach; ?>
</section>
