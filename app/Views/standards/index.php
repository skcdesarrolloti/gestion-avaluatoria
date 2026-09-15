<?php
$activeCategoryCode = $activeCategoryCode ?? ($categories[0]['code'] ?? 'A');
$standardStats = $standardStats ?? ['total' => 0, 'available' => 0, 'missing' => 0];
$storageReport = $storageReport ?? ['dir' => '', 'configured' => false, 'writable' => false,
    'present' => 0, 'total' => 0, 'marked_missing' => 0, 'limits' => []];
$limits = $storageReport['limits'];
$maxFiles = max(0, (int) ($limits['max_file_uploads'] ?? 0));
?>
<section class="space-y-7" x-data="{ query: '' }">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-teal-800">Biblioteca valuatoria</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-950">Normas Técnicas Sectoriales</h1>
            <p class="mt-3 max-w-3xl text-slate-600">Consulta las normas por grupo y categoría de inscripción. Los códigos A, B y 1 a 13 conservan la identificación acordada.</p>
        </div>
        <label class="block min-w-full text-sm font-medium text-slate-700 lg:min-w-80">
            Buscar norma
            <input class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-slate-950 shadow-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-700/20"
                type="search" placeholder="Código, categoría o título"
                x-model.trim="query">
        </label>
    </div>
    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-950">Importar PDFs</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Selecciona todos los archivos PDF de normas. El sistema los cruza por nombre con el catálogo y los guarda en almacenamiento privado.</p>
                <p class="mt-3 text-sm font-medium <?= $standardStats['missing'] === 0 ? 'text-emerald-700' : 'text-amber-700' ?>">
                    <?= e($standardStats['available']) ?> de <?= e($standardStats['total']) ?> PDF cargados<?= $standardStats['missing'] === 0 ? '. Biblioteca completa.' : '; faltan ' . e($standardStats['missing']) . '.' ?>
                </p>
            </div>
            <form class="flex flex-col gap-3 sm:flex-row sm:items-end" method="post" action="<?= e(url('normas-tecnicas-sectoriales/importar')) ?>" enctype="multipart/form-data" x-data="{ busy: false }" @submit="busy = true">
                <?= csrf_field() ?>
                <input type="hidden" name="categoria" value="<?= e($activeCategoryCode) ?>">
                <label class="block text-sm font-medium text-slate-700">
                    Archivos PDF
                    <input class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700"
                        type="file" name="standard_files[]" accept="application/pdf,.pdf" multiple required>
                    <span class="mt-1 block text-xs text-slate-500">Si el lote es grande, sube grupos pequeños para respetar los límites del hosting.</span>
                </label>
                <button class="btn-primary shrink-0" type="submit" :disabled="busy" x-text="busy ? 'Importando…' : 'Importar'">Importar</button>
            </form>
        </div>
        <?php if (!empty($importNotice)): ?>
            <?php $ok = (bool) ($importNotice['ok'] ?? false); ?>
            <div class="mt-4 rounded-lg <?= $ok ? 'bg-teal-50 text-teal-900' : 'bg-red-50 text-red-800' ?> p-4 text-sm">
                <?php if (!$ok): ?>
                    <p><?= e($importNotice['message'] ?? 'No se pudo importar.') ?></p>
                <?php else: ?>
                    <p><?= count($importNotice['copied'] ?? []) ?> importado(s), <?= count($importNotice['skipped'] ?? []) ?> sin cambios, <?= count($importNotice['missing'] ?? []) ?> pendiente(s).</p>
                    <?php if (!empty($importNotice['unknown']) || !empty($importNotice['errors'])): ?>
                        <p class="mt-2">Algunos archivos no coincidieron o no pudieron guardarse; revisa sus nombres y formato PDF.</p>
                        <ul class="mt-2 list-disc space-y-1 pl-5">
                            <?php foreach (array_slice($importNotice['errors'] ?? [], 0, 5) as $error): ?>
                                <li><?= e($error) ?></li>
                            <?php endforeach; ?>
                            <?php foreach (array_slice($importNotice['unknown'] ?? [], 0, 5) as $name): ?>
                                <li><?= e($name) ?> no coincide con el catálogo.</li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="font-semibold text-slate-950">Diagnóstico de guardado</p>
                    <p class="mt-1 break-words text-slate-600">Ruta: <?= e($storageReport['dir']) ?></p>
                </div>
                <span class="rounded-full <?= $storageReport['writable'] ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' ?> px-3 py-1 text-xs font-semibold">
                    <?= $storageReport['writable'] ? 'Carpeta escribible' : 'Sin permiso de escritura' ?>
                </span>
            </div>
            <dl class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div><dt class="text-xs font-semibold uppercase text-slate-500">Modo</dt><dd><?= $storageReport['configured'] ? 'NTS_STORAGE_DIR' : 'storage/ del proyecto' ?></dd></div>
                <div><dt class="text-xs font-semibold uppercase text-slate-500">Archivos físicos</dt><dd><?= e($storageReport['present']) ?> / <?= e($storageReport['total']) ?></dd></div>
                <div><dt class="text-xs font-semibold uppercase text-slate-500">BD sin archivo</dt><dd><?= e($storageReport['marked_missing']) ?></dd></div>
                <div><dt class="text-xs font-semibold uppercase text-slate-500">Límite por lote</dt><dd><?= e($maxFiles ?: ($limits['max_file_uploads'] ?? '')) ?> archivo(s)</dd></div>
            </dl>
            <p class="mt-3 text-xs leading-5 text-slate-500">
                Límites PHP: upload_max_filesize <?= e($limits['upload_max_filesize'] ?? '') ?>,
                post_max_size <?= e($limits['post_max_size'] ?? '') ?>,
                max_execution_time <?= e($limits['max_execution_time'] ?? '') ?>s.
            </p>
            <?php if (!$storageReport['configured']): ?>
                <p class="mt-3 rounded-lg bg-blue-50 p-3 text-sm text-blue-800">Usando la ruta interna storage/normas-tecnicas-sectoriales. La app crea la carpeta si PHP tiene permisos y Git conserva la carpeta base sin versionar los PDFs.</p>
            <?php endif; ?>
            <?php if ($maxFiles > 0 && $maxFiles < (int) $storageReport['total']): ?>
                <p class="mt-3 rounded-lg bg-amber-50 p-3 text-sm text-amber-800">El hosting acepta <?= e($maxFiles) ?> archivos por carga y el catálogo tiene <?= e($storageReport['total']) ?>. Importa en varios lotes o aumenta max_file_uploads.</p>
            <?php endif; ?>
            <?php if ((int) $storageReport['marked_missing'] > 0): ?>
                <p class="mt-3 rounded-lg bg-red-50 p-3 text-sm text-red-800">La base tiene PDFs marcados como importados, pero no existen en disco. Eso confirma que el storage fue limpiado, movido o no es la carpeta correcta.</p>
            <?php endif; ?>
        </div>
    </section>
    <nav class="rounded-lg bg-slate-200/70 p-2" aria-label="Categorías de normas técnicas">
        <div class="flex gap-2 overflow-x-auto">
            <?php foreach ($categories as $category): ?>
                <?php
                $isActive = $category['code'] === $activeCategoryCode;
                $available = count(array_filter($category['standards'], static fn (array $item): bool => (bool) $item['has_file']));
                ?>
                <a class="inline-flex min-h-11 shrink-0 items-center gap-2 rounded-md px-3 py-2 text-xs font-semibold transition <?= $isActive ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:bg-white/70' ?>"
                    href="<?= e(url('normas-tecnicas-sectoriales?categoria=' . rawurlencode((string) $category['code']))) ?>"
                    <?= $isActive ? 'aria-current="page"' : '' ?>>
                    <span class="inline-flex size-7 items-center justify-center rounded-full bg-teal-800 text-xs text-white"><?= e($category['code']) ?></span>
                    <span class="max-w-44 truncate"><?= e($category['name']) ?></span>
                    <span class="rounded-full bg-white/80 px-2 py-0.5 text-[11px] text-slate-500"><?= $available ?>/<?= count($category['standards']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </nav>
    <div class="grid gap-6">
        <?php foreach ($categories as $category): ?>
            <?php if ($category['code'] !== $activeCategoryCode) { continue; } ?>
            <section class="pt-2">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-xl font-semibold text-slate-950">
                        <span class="mr-2 inline-flex size-9 items-center justify-center rounded-full bg-teal-800 text-sm text-white"><?= e($category['code']) ?></span>
                        <?= e($category['name']) ?>
                    </h2>
                    <span class="text-sm text-slate-500"><?= count($category['standards']) ?> norma(s)</span>
                </div>
                <?php if ($category['standards']): ?>
                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                        <?php foreach ($category['standards'] as $standard): ?>
                            <?php
                            $status = $standard['has_file'] ? 'PDF disponible' : 'Pendiente de importar';
                            $standardTerm = mb_strtolower(implode(' ', [
                                $standard['standard_code'], $standard['title'], $standard['source_filename'], $standard['kind'],
                            ]));
                            ?>
                            <article class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm"
                                x-show='query === "" || <?= e(json_encode($standardTerm, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>.includes(query.toLowerCase())'>
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-teal-800"><?= e($standard['standard_code']) ?></p>
                                        <h3 class="mt-1 font-semibold text-slate-950"><?= e($standard['title']) ?></h3>
                                    </div>
                                    <span class="shrink-0 rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600"><?= e($standard['kind']) ?></span>
                                </div>
                                <p class="mt-3 text-sm text-slate-500"><?= e($standard['source_filename']) ?></p>
                                <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                                    <span class="text-xs font-medium <?= $standard['has_file'] ? 'text-emerald-700' : 'text-amber-700' ?>"><?= e($status) ?></span>
                                    <a class="btn-secondary" href="<?= e(url('normas-tecnicas-sectoriales/' . $standard['slug'])) ?>">Consultar</a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="mt-4 rounded-lg border border-dashed border-slate-300 bg-white p-4 text-sm text-slate-500">Sin normas asignadas por ahora.</p>
                <?php endif; ?>
            </section>
        <?php endforeach; ?>
    </div>
</section>
