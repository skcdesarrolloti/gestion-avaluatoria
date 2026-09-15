<section class="space-y-8" x-data="{ query: '' }">
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
            </div>
            <form class="flex flex-col gap-3 sm:flex-row sm:items-end" method="post" action="<?= e(url('normas-tecnicas-sectoriales/importar')) ?>" enctype="multipart/form-data" x-data="{ busy: false }" @submit="busy = true">
                <?= csrf_field() ?>
                <label class="block text-sm font-medium text-slate-700">
                    Archivos PDF
                    <input class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700"
                        type="file" name="standard_files[]" accept="application/pdf,.pdf" multiple required>
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
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </section>
    <div class="grid gap-6">
        <?php foreach ($categories as $category): ?>
            <?php
            $parts = array_merge([$category['code'], $category['name']], array_column($category['standards'], 'standard_code'),
                array_column($category['standards'], 'title'), array_column($category['standards'], 'source_filename'));
            $term = mb_strtolower(implode(' ', $parts));
            ?>
            <section class="border-t border-slate-200 pt-6" x-show='<?= e(json_encode($term, JSON_UNESCAPED_UNICODE)) ?>.includes(query.toLowerCase()) || query === ""'>
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
                            <?php $status = $standard['has_file'] ? 'PDF disponible' : 'Pendiente de importar'; ?>
                            <article class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
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
