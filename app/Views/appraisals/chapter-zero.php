<?php
use App\Support\AppraisalCatalog;

$selected = static fn (string $name, string $value): string => (string) ($record[$name] ?? '') === $value ? 'selected' : '';
$field = static fn (string $name): string => (string) ($record[$name] ?? '');
$notes = $catalog['notes'] ?? [];
$initial = ['notes' => $notes];
?>
<a href="<?= e(url('valuaciones')) ?>" class="inline-flex min-h-11 items-center text-sm font-medium text-teal-800">← Valuaciones</a>
<div class="mt-3 flex flex-wrap items-start justify-between gap-5">
    <div>
        <p class="eyebrow">Capítulo 0</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight">Configuración del avalúo</h1>
        <p class="mt-3 max-w-3xl text-slate-600">
            Primero dejamos clara la evidencia visual, el perito responsable y la ruta técnica antes
            de abrir los capítulos del informe.
        </p>
    </div>
    <span class="rounded-full bg-amber-50 px-3 py-1 text-sm font-semibold text-amber-800">Borrador</span>
</div>

<div class="mt-8 space-y-7" x-data="{
    typologyHint: <?= e(json_encode($field('igac_typology_hint'), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
    igacCategory: <?= e(json_encode($field('igac_category'), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>
}">
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="eyebrow">Evidencia inicial</p>
                <h2 class="mt-2 text-2xl font-semibold">Fotos del inmueble</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">
                    Sube las fotos que permitan reconocer qué se va a valorar. Con ellas se hace la
                    preclasificación frente a las tipologías constructivas IGAC.
                </p>
            </div>
            <a class="btn-secondary" href="<?= e(url('tipologias-constructivas-igac')) ?>">Ver tipologías IGAC</a>
        </div>
        <?php if ($photoMessage): ?>
            <p class="mt-5 rounded-xl bg-emerald-50 p-4 text-sm font-semibold text-emerald-800"><?= e($photoMessage) ?></p>
        <?php endif; ?>
        <?php if ($photoError): ?>
            <p class="mt-5 rounded-xl bg-red-50 p-4 text-sm font-semibold text-red-800"><?= e($photoError) ?></p>
        <?php endif; ?>
        <form class="mt-6 grid gap-4 lg:grid-cols-[1fr_auto]" method="post" enctype="multipart/form-data"
            action="<?= e(url('avaluos/' . $record['id'] . '/capitulo-0/fotos')) ?>"
            x-data="{ busy: false, hasFiles: false }" @submit="busy = true">
            <?= csrf_field() ?>
            <label class="label">Agregar fotos
                <input class="input" type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple
                    @change="hasFiles = $event.target.files.length > 0">
            </label>
            <div class="flex items-end">
                <button class="btn-primary min-h-11 w-full lg:w-auto" type="submit" :disabled="busy || !hasFiles"
                    x-text="busy ? 'Subiendo...' : 'Subir fotos'">Subir fotos</button>
            </div>
        </form>
        <?php if ($photos): ?>
            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($photos as $photo): ?>
                    <a class="block overflow-hidden rounded-xl border border-slate-200 bg-slate-50"
                        href="<?= e(url('avaluos/' . $record['id'] . '/fotos/' . $photo['id'])) ?>" target="_blank" rel="noopener">
                        <img class="aspect-[4/3] w-full object-contain" alt="Foto del inmueble"
                            src="<?= e(url('avaluos/' . $record['id'] . '/fotos/' . $photo['id'])) ?>" loading="lazy">
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="mt-5 rounded-xl border border-dashed border-slate-300 p-5 text-sm text-slate-600">
                Aún no hay fotos cargadas para este expediente.
            </p>
        <?php endif; ?>
    </section>

    <?php if ($photos && $igacCandidates): ?>
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="eyebrow">Preclasificación IGAC</p>
                    <h2 class="mt-2 text-2xl font-semibold">Comparación asistida</h2>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                        Esta ayuda cruza la evidencia cargada con el contexto del expediente y el catálogo IGAC.
                        Confirma siempre la tipología con criterio técnico antes de guardar.
                    </p>
                </div>
                <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-800">Sugerencia no automática</span>
            </div>
            <div class="mt-6 grid gap-5 lg:grid-cols-[18rem_1fr]">
                <div class="space-y-3">
                    <p class="text-sm font-semibold text-slate-700">Foto de referencia</p>
                    <?php foreach (array_slice($photos, 0, 2) as $photo): ?>
                        <a class="block overflow-hidden rounded-xl border border-slate-200 bg-slate-50"
                            href="<?= e(url('avaluos/' . $record['id'] . '/fotos/' . $photo['id'])) ?>" target="_blank" rel="noopener">
                            <img class="aspect-[4/3] w-full object-contain" alt="Foto del inmueble"
                                src="<?= e(url('avaluos/' . $record['id'] . '/fotos/' . $photo['id'])) ?>" loading="lazy">
                        </a>
                    <?php endforeach; ?>
                </div>
                <div class="grid gap-4 xl:grid-cols-2">
                    <?php foreach ($igacCandidates as $candidate): ?>
                        <?php $imageUrl = url('assets/tipologias-igac/images/' . rawurlencode((string) $candidate['image_filename'])); ?>
                        <article class="grid min-w-0 gap-4 rounded-xl border border-slate-200 p-4 sm:grid-cols-[7rem_1fr]">
                            <a class="block aspect-[4/3] overflow-hidden rounded-lg border border-slate-200 bg-slate-50"
                                href="<?= e($imageUrl) ?>" target="_blank" rel="noopener" data-no-fetch>
                                <img class="h-full w-full object-contain" src="<?= e($imageUrl) ?>"
                                    alt="<?= e($candidate['denomination']) ?>" loading="lazy">
                            </a>
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-start justify-between gap-2">
                                    <h3 class="text-anywhere text-sm font-semibold text-slate-950"><?= e($candidate['denomination']) ?></h3>
                                    <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-600">
                                        <?= e($candidate['category_name']) ?>
                                    </span>
                                </div>
                                <p class="text-anywhere mt-2 line-clamp-3 text-xs leading-5 text-slate-600">
                                    <?= e($candidate['description']) ?>
                                </p>
                                <button class="btn-secondary mt-3 min-h-10 text-sm" type="button"
                                    @click="igacCategory = <?= e(json_encode($candidate['category_code'], JSON_THROW_ON_ERROR)) ?>;
                                        typologyHint = <?= e(json_encode($candidate['denomination'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>;
                                        document.getElementById('datos-base-capitulo-0').scrollIntoView({ behavior: 'smooth', block: 'start' });">
                                    Usar esta tipología
                                </button>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <form class="grid gap-7 lg:grid-cols-[1fr_18rem]" method="post"
        action="<?= e(url('avaluos/' . $record['id'] . '/capitulo-0')) ?>"
        x-data="{ busy: false, notes: <?= e(json_encode($initial['notes'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?> }"
        @submit="busy = true">
        <?= csrf_field() ?>
        <input type="hidden" name="version" value="<?= e($record['version']) ?>">
        <div id="datos-base-capitulo-0" class="scroll-mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <p class="eyebrow">Configuración</p>
            <h2 class="mt-2 text-2xl font-semibold">Datos base del encargo</h2>
            <div class="mt-6 grid gap-5 md:grid-cols-2">
                <label class="label md:col-span-2">Nombre del inmueble
                    <input class="input" name="titulo" maxlength="160" value="<?= e($field('titulo')) ?>"
                        placeholder="Ej. Lote Bruselas">
                </label>
                <label class="label">Dirección
                    <input class="input" name="direccion" maxlength="220" value="<?= e($field('direccion')) ?>"
                        placeholder="Dirección o referencia de ubicación">
                </label>
                <label class="label">Municipio
                    <input class="input" name="municipio" maxlength="120" value="<?= e($field('municipio')) ?>"
                        placeholder="Municipio">
                </label>
                <label class="label">Perito responsable
                    <select class="input" name="appraiser_id">
                        <option value="">Selecciona perito</option>
                        <?php foreach ($appraisers as $appraiser): ?>
                            <option value="<?= e($appraiser['id']) ?>" <?= $selected('appraiser_id', $appraiser['id']) ?>>
                                <?= e($appraiser['code'] . ' · ' . $appraiser['full_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="label">Categoría IGAC probable
                    <select class="input" name="igac_category" x-model="igacCategory">
                        <option value="">Por definir</option>
                        <?php foreach ($igacCategories as $category): ?>
                            <option value="<?= e($category['code']) ?>" <?= $selected('igac_category', $category['code']) ?>>
                                <?= e($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="label md:col-span-2">Tipología IGAC probable
                    <input class="input" name="igac_typology_hint" maxlength="190"
                        x-model="typologyHint" placeholder="Nombre o referencia de tipología probable">
                </label>
                <?php foreach (['tipo', 'tipo_derecho', 'finalidad', 'tipo_inmueble', 'destinacion',
                    'base_valor', 'aplica_niif', 'regimen_ph', 'estructura_metodo'] as $name): ?>
                    <?php [$label, $placeholder, , $help, $options] = AppraisalCatalog::selectFields()[$name]; ?>
                    <?php $label = $name === 'aplica_niif' ? 'Activo empresarial' : $label; ?>
                    <label class="label"><?= e($label) ?>
                        <select class="input" name="<?= e($name) ?>">
                            <option value=""><?= e($placeholder) ?></option>
                            <?php foreach ($options as $value => $text): ?>
                                <option value="<?= e($value) ?>" <?= $selected($name, $value) ?>><?= e($text) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="mt-1 block text-xs leading-5 text-slate-500"><?= e($help) ?></span>
                    </label>
                <?php endforeach; ?>
                <label class="label md:col-span-2">Notas de inspección y configuración
                    <textarea class="input" name="inspection_notes" rows="4" maxlength="2000"
                        placeholder="Anota dudas de campo, tipología probable, componentes o alertas técnicas."><?= e($field('inspection_notes')) ?></textarea>
                </label>
                <label class="label md:col-span-2">Observaciones generales
                    <textarea class="input" name="observaciones" rows="4" maxlength="4000"
                        placeholder="Observaciones generales del encargo."><?= e($field('observaciones')) ?></textarea>
                </label>
            </div>
        </div>
        <aside class="space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-semibold">Guardar Capítulo 0</h2>
                <p class="mt-3 text-sm leading-6 text-slate-600">
                    Guarda la configuración y las fotos antes de avanzar al Capítulo 1.
                </p>
                <button class="btn-primary mt-5 w-full" type="submit" :disabled="busy"
                    x-text="busy ? 'Guardando...' : 'Guardar configuración'">Guardar configuración</button>
            </div>
            <p class="px-2 text-xs leading-5 text-slate-500">
                El consecutivo técnico se asignará cuando el expediente quede formalmente configurado.
            </p>
        </aside>
    </form>
</div>
