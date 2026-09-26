<?php
$activeCategoryCode = $activeCategoryCode ?? ($categories[0]['code'] ?? 'RESIDENCIALES');
$stats = $stats ?? ['total' => 0, 'categories' => 0];
$documentCategories = $documentCategories ?? [];
$activeDocumentCategory = $activeDocumentCategory ?? 'todos';
$documents = $documents ?? [];
$documentStats = $documentStats ?? ['documents' => 0, 'topics' => 0];
?>
<section class="space-y-7" x-data="{ query: '' }">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="eyebrow">Biblioteca rectora</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-950">IGAC</h1>
            <p class="mt-3 max-w-3xl text-slate-600">
                Centro único para documentos IGAC usados por conservación, tipologías,
                identificación predial y soporte valuatorio. Cada documento existe una sola vez.
            </p>
        </div>
        <label class="block min-w-full text-sm font-medium text-slate-700 lg:min-w-80">
            Buscar en IGAC
            <input class="input" type="search" placeholder="Código, nombre, tema o módulo" x-model.trim="query">
        </label>
    </div>

    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-950">Biblioteca documental central</h2>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                    Los módulos citan estos registros mediante su ficha central, sin cargar una copia por módulo.
                    La biblioteca queda preparada para nuevas versiones, históricos y más relaciones.
                </p>
            </div>
            <div class="flex flex-wrap gap-2 text-xs font-semibold">
                <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600"><?= e($documentStats['documents']) ?> documento(s)</span>
                <span class="rounded-full bg-blue-50 px-3 py-1 text-blue-700"><?= e($documentStats['topics']) ?> tema(s)</span>
                <span class="rounded-full bg-emerald-50 px-3 py-1 text-emerald-700"><?= e($stats['total']) ?> tipología(s)</span>
            </div>
        </div>
    </section>

    <nav class="rounded-lg bg-slate-200/70 p-2" aria-label="Filtros de documentos IGAC">
        <div class="flex gap-2 overflow-x-auto">
            <?php foreach ($documentCategories as $category): ?>
                <?php $code = (string) $category['code']; $isActive = $code === $activeDocumentCategory; ?>
                <a class="inline-flex min-h-11 shrink-0 items-center rounded-md px-4 py-2 text-xs font-semibold transition <?= $isActive ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:bg-white/70' ?>"
                    href="<?= e(url('igac?documentos=' . rawurlencode($code))) ?>" <?= $isActive ? 'aria-current="page"' : '' ?>>
                    <?= e((string) $category['label']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </nav>

    <section class="grid gap-4 lg:grid-cols-2">
        <?php foreach ($documents as $document): ?>
            <?php
            $term = mb_strtolower(implode(' ', [
                $document['codigo'], $document['nombre'], $document['tipo_documento'], $document['version'],
                implode(' ', $document['categoria_igac']), implode(' ', $document['temas_relacionados']),
                implode(' ', $document['modulos_que_lo_utilizan']),
            ]));
            ?>
            <article class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm"
                x-show='query === "" || <?= e(json_encode($term, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>.includes(query.toLowerCase())'>
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-anywhere text-sm font-semibold text-teal-800"><?= e((string) $document['codigo']) ?></p>
                        <h2 class="text-anywhere mt-1 text-base font-semibold text-slate-950"><?= e((string) $document['nombre']) ?></h2>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600"><?= e((string) $document['tipo_documento']) ?></span>
                </div>
                <p class="mt-3 text-sm leading-6 text-slate-600"><?= e((string) $document['descripcion_corta']) ?></p>
                <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-3">
                    <div><dt class="text-xs font-semibold uppercase text-slate-500">Versión</dt><dd><?= e((string) $document['version']) ?></dd></div>
                    <div><dt class="text-xs font-semibold uppercase text-slate-500">Vigencia</dt><dd><?= e((string) $document['fecha_vigencia']) ?></dd></div>
                    <div><dt class="text-xs font-semibold uppercase text-slate-500">Estado</dt><dd><?= e((string) $document['estado']) ?></dd></div>
                </dl>
                <div class="mt-4 flex flex-wrap gap-2">
                    <?php foreach ($document['temas_relacionados'] as $topic): ?>
                        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700"><?= e((string) $topic) ?></span>
                    <?php endforeach; ?>
                </div>
                <div class="mt-4 flex flex-wrap gap-3">
                    <a class="btn-primary" href="<?= e(url('igac/documento/' . rawurlencode((string) $document['id']))) ?>">Abrir ficha</a>
                    <?php if ((string) ($document['archivo_descarga'] ?? '') !== ''): ?>
                        <a class="btn-secondary" href="<?= e(url('igac/documento/' . rawurlencode((string) $document['id']) . '/descargar')) ?>" data-no-fetch>Descargar</a>
                    <?php endif; ?>
                    <?php if ((string) $document['fuente_oficial'] !== ''): ?>
                        <a class="btn-secondary" href="<?= e((string) $document['fuente_oficial']) ?>" target="_blank" rel="noopener" data-no-fetch>Ficha oficial</a>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </section>

    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-950">Catálogo de tipologías dentro de IGAC</h2>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
            El catálogo operativo conserva sus imágenes y categorías; ahora queda alojado bajo la biblioteca IGAC.
        </p>
    </section>

    <nav class="rounded-lg bg-slate-200/70 p-2" aria-label="Categorías de tipologías IGAC">
        <div class="flex gap-2 overflow-x-auto">
            <?php foreach ($categories as $category): ?>
                <?php $isActive = $category['code'] === $activeCategoryCode; ?>
                <a class="inline-flex min-h-11 shrink-0 items-center gap-2 rounded-md px-3 py-2 text-xs font-semibold transition <?= $isActive ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:bg-white/70' ?>"
                    href="<?= e(url('igac?categoria=' . rawurlencode((string) $category['code']))) ?>"
                    <?= $isActive ? 'aria-current="page"' : '' ?>>
                    <span class="inline-flex size-7 items-center justify-center rounded-full bg-teal-800 text-xs text-white"><?= e(substr((string) $category['name'], 0, 1)) ?></span>
                    <span class="max-w-44 truncate"><?= e($category['name']) ?></span>
                    <span class="rounded-full bg-white/80 px-2 py-0.5 text-[11px] text-slate-500"><?= e($category['count']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </nav>

    <section class="pt-2">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-xl font-semibold text-slate-950"><?= e($typologies[0]['category_name'] ?? 'Tipologías') ?></h2>
            <span class="text-sm text-slate-500"><?= count($typologies) ?> registro(s)</span>
        </div>
        <div class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
            <?php foreach ($typologies as $typology): ?>
                <?php
                $term = mb_strtolower(implode(' ', [$typology['denomination'], $typology['description'],
                    $typology['specifications'], $typology['category_name'], $typology['useful_life'], $typology['unit']]));
                $imageUrl = url('assets/tipologias-igac/images/' . rawurlencode((string) $typology['image_filename']));
                ?>
                <article class="grid min-w-0 max-w-full gap-4 overflow-hidden rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-[minmax(140px,180px)_minmax(0,1fr)]"
                    x-show='query === "" || <?= e(json_encode($term, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>.includes(query.toLowerCase())'>
                    <a class="block aspect-[4/3] w-full self-start overflow-hidden rounded-md border border-slate-200 bg-white" href="<?= e($imageUrl) ?>" target="_blank" rel="noopener" data-no-fetch>
                        <img class="h-full w-full object-contain" src="<?= e($imageUrl) ?>" alt="<?= e($typology['denomination']) ?>" loading="lazy">
                    </a>
                    <div class="min-w-0">
                        <div class="flex min-w-0 flex-wrap items-start justify-between gap-2">
                            <h3 class="text-anywhere min-w-0 flex-1 text-base font-semibold text-slate-950"><?= e($typology['denomination']) ?></h3>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600"><?= e($typology['category_name']) ?></span>
                        </div>
                        <p class="text-anywhere mt-3 text-sm leading-6 text-slate-600"><?= e($typology['description']) ?></p>
                        <p class="mt-3 text-xs font-semibold uppercase text-slate-500">Especificaciones técnicas</p>
                        <p class="text-anywhere mt-1 text-sm leading-6 text-slate-600"><?= e($typology['specifications']) ?></p>
                        <dl class="mt-4 flex flex-wrap gap-2 text-xs font-semibold">
                            <div class="rounded-full bg-blue-50 px-3 py-1 text-blue-700">Vida útil: <?= e($typology['useful_life'] ?: 'N/A') ?></div>
                            <div class="rounded-full bg-emerald-50 px-3 py-1 text-emerald-700">Unidad: <?= e($typology['unit'] ?: 'N/A') ?></div>
                        </dl>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</section>
