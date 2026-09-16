<?php
$embedded = $photoUploadEmbedded ?? false;
$subjectActionBase = $subjectActionBase ?? 'avaluos/' . $record['id'] . '/expediente';
$photoUnitId = $photoUploadUnitId ?? '';
$photoUnitLabel = $photoUploadUnitLabel ?? '';
$photoUnitTypology = $photoUploadTypology ?? '';
$photoEyebrow = $photoUploadEyebrow ?? 'Evidencia posterior a la tipología';
$photoTitle = $photoUploadTitle ?? 'Fotos para comprobar la unidad';
$photoDescription = $photoUploadDescription ?? 'Sube las fotos después de escoger la tipología probable. La imagen real permite confirmar o ajustar la clasificación constructiva antes de usarla en reposición o descripción.';
$photoReturnTo = $photoUploadReturnTo ?? $subjectActionBase;
$visiblePhotos = $photoUnitId === '' ? $photos : array_values(array_filter($photos,
    static fn (array $photo): bool => (string) ($photo['unit_id'] ?? '') === $photoUnitId));
?>
    <<?= $embedded ? 'div' : 'section' ?> class="<?= $embedded ? 'mt-8 rounded-xl border border-slate-200 bg-slate-50 p-5' : 'rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8' ?>">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="eyebrow"><?= e($photoEyebrow) ?></p>
                <h2 class="mt-2 text-2xl font-semibold"><?= e($photoTitle) ?></h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">
                    <?= e($photoDescription) ?>
                </p>
                <?php if ($photoUnitLabel): ?>
                    <p class="mt-2 text-xs font-semibold text-teal-800">
                        <?= e($photoUnitLabel) ?><?= $photoUnitTypology ? ' · ' . e($photoUnitTypology) : '' ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($photoMessage): ?>
            <p class="mt-5 rounded-xl bg-emerald-50 p-4 text-sm font-semibold text-emerald-800"><?= e($photoMessage) ?></p>
        <?php endif; ?>
        <?php if ($photoError): ?>
            <p class="mt-5 rounded-xl bg-red-50 p-4 text-sm font-semibold text-red-800"><?= e($photoError) ?></p>
        <?php endif; ?>
        <form class="mt-6 grid gap-4 lg:grid-cols-[1fr_auto]" method="post" enctype="multipart/form-data"
            action="<?= e(url($subjectActionBase . '/fotos')) ?>"
            x-data="photoUpload" @submit="busy = true">
            <?= csrf_field() ?>
            <?php if ($photoUnitId): ?><input type="hidden" name="unit_id" value="<?= e($photoUnitId) ?>"><?php endif; ?>
            <div class="label">Agregar fotos
                <div class="mt-2 grid gap-3 rounded-xl border border-dashed border-slate-300 bg-white p-4 sm:grid-cols-2">
                    <label class="btn-secondary min-h-11">Elegir archivos
                        <input class="sr-only" type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple
                            x-ref="photos" @change="update($event.target)">
                    </label>
                    <div class="min-h-11 rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-600"
                        tabindex="0" @paste="paste($event)">
                        Pegar imagen aquí con Ctrl+V
                    </div>
                    <p class="mt-2 text-xs leading-5 text-slate-500">
                        El botón abre archivos; el recuadro derecho recibe imágenes copiadas.
                    </p>
                    <p class="mt-1 text-xs font-semibold text-teal-800" x-show="fileNames" x-text="fileNames"></p>
                </div>
            </div>
            <div class="flex items-end">
                <button class="btn-primary min-h-11 w-full lg:w-auto" type="submit" :disabled="busy || !hasFiles"
                    x-text="busy ? 'Subiendo...' : 'Subir fotos'">Subir fotos</button>
            </div>
        </form>
        <?php if ($visiblePhotos): ?>
            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($visiblePhotos as $photo): ?>
                    <?php $canRender = !empty($photo['file_available']) || !empty($photo['has_blob']); ?>
                    <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                        <?php if ($canRender): ?>
                            <a class="block" href="<?= e(url('avaluos/' . $record['id'] . '/fotos/' . $photo['id'])) ?>" target="_blank" rel="noopener">
                                <img class="aspect-[4/3] w-full object-contain" alt="Foto del inmueble"
                                    src="<?= e(url('avaluos/' . $record['id'] . '/fotos/' . $photo['id'])) ?>" loading="lazy">
                            </a>
                            <?php if (empty($photo['file_available']) && !empty($photo['has_blob'])): ?>
                                <p class="border-t border-amber-100 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-800">
                                    Mostrada desde respaldo interno.
                                </p>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="p-4 text-sm font-semibold text-red-700">Archivo físico no encontrado. Vuelve a subir esta foto.</p>
                        <?php endif; ?>
                        <form class="border-t border-slate-200 bg-white p-3" method="post"
                            action="<?= e(url('avaluos/' . $record['id'] . '/fotos/' . $photo['id'] . '/eliminar')) ?>"
                            x-data="{ busy: false }" @submit="busy = true">
                            <?= csrf_field() ?>
                            <input type="hidden" name="return_to" value="<?= e($photoReturnTo) ?>">
                            <button class="btn-secondary min-h-10 w-full text-sm" type="submit" :disabled="busy"
                                x-text="busy ? 'Quitando...' : 'Quitar foto'">Quitar foto</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="mt-5 rounded-xl border border-dashed border-slate-300 p-5 text-sm text-slate-600">
                Aún no hay fotos cargadas para <?= $photoUnitId ? 'esta unidad' : 'este expediente' ?>.
            </p>
        <?php endif; ?>
    </<?= $embedded ? 'div' : 'section' ?>>
