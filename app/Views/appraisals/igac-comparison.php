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
                    <?php if (!empty($photo['file_available']) || !empty($photo['has_blob'])): ?>
                        <a class="block overflow-hidden rounded-xl border border-slate-200 bg-slate-50"
                            href="<?= e(url('avaluos/' . $record['id'] . '/fotos/' . $photo['id'])) ?>" target="_blank" rel="noopener">
                            <img class="aspect-[4/3] w-full object-contain" alt="Foto del inmueble"
                                src="<?= e(url('avaluos/' . $record['id'] . '/fotos/' . $photo['id'])) ?>" loading="lazy">
                        </a>
                    <?php endif; ?>
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
