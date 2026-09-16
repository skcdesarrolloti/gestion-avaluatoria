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
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="mt-5 rounded-xl border border-dashed border-slate-300 p-5 text-sm text-slate-600">
                Aún no hay fotos cargadas para este expediente.
            </p>
        <?php endif; ?>
    </section>

