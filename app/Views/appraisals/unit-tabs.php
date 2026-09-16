<?php
$unitLabel = static function (array $unit): string {
    return ($unit['unit_kind'] === 'annex' ? 'Anexo ' : 'Unidad ') . (int) $unit['unit_index'];
};
$visibleUnits = array_values(array_filter($units, static fn (array $unit): bool => $unit['unit_kind'] !== 'common'));
$hasPhotos = !empty($photos);
$typologyOptions = $igacTypologiesByCategory ?? [];
$subjectActionBase = $subjectActionBase ?? 'avaluos/' . $record['id'] . '/capitulo-0';
$labelInput = static fn (array $unit): string => (string) $unit['label'] === $unitLabel($unit) ? '' : (string) $unit['label'];
$labelMap = [];
foreach ($visibleUnits as $unit) $labelMap[$unit['id']] = $labelInput($unit);
?>
<section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8"
    x-data="{
        active: '<?= e($visibleUnits[0]['id'] ?? '') ?>',
        labels: <?= e(json_encode($labelMap, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
        typologies: <?= e(json_encode($typologyOptions, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
        imageBase: <?= e(json_encode(url('assets/tipologias-igac/images'), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
        imageUrl(item) { return item?.image ? this.imageBase + '/' + encodeURIComponent(item.image) : '' },
        selectedItem(category, hint) {
            return (this.typologies[category] || []).find(item => item.value === hint) || null
        },
        descriptionDraft(item) {
            if (!item) return ''
            return [item.description, item.specifications ? 'Especificaciones IGAC: ' + item.specifications : '']
                .filter(Boolean).join('\n\n')
        }
    }">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Unidades del predio</p>
            <h2 class="mt-2 text-2xl font-semibold">Tipología por unidad</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Primero clasifica cada unidad o anexo; luego sube las fotos para comprobar si la evidencia
                coincide. La tipología IGAC ayuda a orientar descripción y valor de reposición.
            </p>
        </div>
        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600"><?= count($visibleUnits) ?> pestaña(s)</span>
    </div>
    <form class="mt-6" method="post" action="<?= e(url($subjectActionBase . '/unidades')) ?>"
        x-data="{ busy: false }" @submit="busy = true">
        <?= csrf_field() ?>
        <?php if (!$visibleUnits): ?>
            <p class="rounded-xl border border-dashed border-slate-300 p-5 text-sm text-slate-600">
                Aún no hay unidades ni anexos configurados. Define los conteos en la lectura inicial del predio.
            </p>
        <?php else: ?>
            <div class="flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" role="tablist">
                <?php foreach ($visibleUnits as $unit): ?>
                <button class="min-h-11 shrink-0 rounded-lg px-4 py-2 text-sm font-semibold"
                    type="button" @click="active = '<?= e($unit['id']) ?>'"
                    :class="active === '<?= e($unit['id']) ?>' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:bg-white/70'">
                    <span x-text="labels['<?= e($unit['id']) ?>'] || 'Nombrar <?= e(strtolower($unitLabel($unit))) ?>'"></span>
                </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php foreach ($visibleUnits as $unit): ?>
            <div class="mt-5 rounded-xl border border-slate-200 p-5" x-show="active === '<?= e($unit['id']) ?>'"
                x-data="{
                    category: <?= e(json_encode((string) $unit['igac_category'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
                    hint: <?= e(json_encode((string) $unit['igac_typology_hint'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
                    notes: <?= e(json_encode((string) $unit['notes'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
                    browserOpen: false
                }">
                <div class="grid gap-5 md:grid-cols-2">
                    <label class="label">Nombre de la unidad definido por el analista
                        <input class="input" name="units[<?= e($unit['id']) ?>][label]" maxlength="120"
                            x-model="labels['<?= e($unit['id']) ?>']" placeholder="Ej. Casa principal, Local 1, Piscina">
                        <input type="hidden" name="units[<?= e($unit['id']) ?>][default_label]" value="<?= e($unitLabel($unit)) ?>">
                    </label>
                    <label class="label">Categoría IGAC de esta unidad
                        <select class="input" name="units[<?= e($unit['id']) ?>][igac_category]" x-model="category"
                            @change="if (!(typologies[category] || []).some(item => item.value === hint)) hint = ''">
                            <option value="">Por definir</option>
                            <?php foreach ($igacCategories as $category): ?>
                                <option value="<?= e($category['code']) ?>" <?= (string) $unit['igac_category'] === (string) $category['code'] ? 'selected' : '' ?>>
                                    <?= e($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label class="label md:col-span-2">Tipología preliminar de esta unidad
                        <select class="input" name="units[<?= e($unit['id']) ?>][igac_typology_hint]"
                            x-model="hint" :disabled="!category">
                            <option value="" x-text="category ? 'Selecciona tipología preliminar' : 'Selecciona primero la categoría IGAC'"></option>
                            <template x-for="item in (typologies[category] || [])" :key="item.value">
                                <option :value="item.value" x-text="item.label"></option>
                            </template>
                        </select>
                        <span class="mt-1 block text-xs leading-5 text-slate-500" x-show="category">
                            <span x-text="(typologies[category] || []).length"></span>
                            tipologías disponibles para esta categoría.
                        </span>
                        <span class="mt-1 block text-xs leading-5 text-slate-500" x-show="!category">
                            Selecciona una categoría para reducir la búsqueda del catálogo IGAC.
                        </span>
                        <article class="mt-4 grid gap-4 rounded-xl border border-slate-200 bg-slate-50 p-3 sm:grid-cols-[9rem_1fr]"
                            x-show="selectedItem(category, hint)">
                            <a class="block aspect-[4/3] overflow-hidden rounded-lg border border-slate-200 bg-white"
                                :href="imageUrl(selectedItem(category, hint))" target="_blank" rel="noopener" data-no-fetch>
                                <img class="h-full w-full object-contain" :src="imageUrl(selectedItem(category, hint))"
                                    alt="Tipología IGAC seleccionada" loading="lazy">
                            </a>
                            <div class="min-w-0">
                                <p class="text-anywhere text-sm font-semibold text-slate-950"
                                    x-text="selectedItem(category, hint)?.label"></p>
                                <p class="text-anywhere mt-2 line-clamp-3 text-xs leading-5 text-slate-600"
                                    x-text="selectedItem(category, hint)?.description"></p>
                                <p class="text-anywhere mt-2 line-clamp-2 text-xs leading-5 text-slate-500"
                                    x-text="selectedItem(category, hint)?.specifications"></p>
                                <button class="btn-secondary mt-3 min-h-10 text-xs" type="button"
                                    @click="notes = descriptionDraft(selectedItem(category, hint))">
                                    Usar como descripción base
                                </button>
                            </div>
                        </article>
                        <div class="mt-4" x-show="category">
                            <button class="btn-secondary min-h-10 text-sm" type="button" @click="browserOpen = !browserOpen"
                                x-text="browserOpen ? 'Cerrar opciones con foto' : 'Ver opciones con foto'">
                                Ver opciones con foto
                            </button>
                            <div class="mt-3 max-h-[28rem] overflow-y-auto rounded-xl border border-slate-200 p-3"
                                x-show="browserOpen">
                                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                                    <template x-for="item in (typologies[category] || [])" :key="item.value">
                                        <button class="grid min-h-32 gap-3 rounded-xl border p-3 text-left hover:border-teal-700 sm:grid-cols-[6rem_1fr]"
                                            type="button" @click="hint = item.value; browserOpen = false"
                                            :class="hint === item.value ? 'border-teal-700 bg-teal-50' : 'border-slate-200 bg-white'">
                                            <span class="block aspect-[4/3] overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                                                <img class="h-full w-full object-contain" :src="imageUrl(item)"
                                                    alt="Referencia IGAC" loading="lazy">
                                            </span>
                                            <span class="min-w-0">
                                                <span class="text-anywhere block text-xs font-semibold text-slate-950"
                                                    x-text="item.label"></span>
                                                <span class="text-anywhere mt-1 line-clamp-3 block text-xs leading-5 text-slate-600"
                                                    x-text="item.description"></span>
                                            </span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </label>
                    <label class="label md:col-span-2">
                        Descripción editable para el informe
                        <?php if ($hasPhotos): ?>
                            <span class="mt-3 block rounded-xl border border-emerald-50 bg-emerald-50 p-3 text-xs leading-5 text-emerald-800"
                                x-show="selectedItem(category, hint)">
                                <strong class="block">Sugerencia IGAC para contrastar con la foto real</strong>
                                <span class="mt-1 block" x-text="descriptionDraft(selectedItem(category, hint))"></span>
                                <button class="btn-secondary mt-3 min-h-10 bg-white text-xs" type="button"
                                    @click="notes = descriptionDraft(selectedItem(category, hint))">
                                    Pasar sugerencia al campo editable
                                </button>
                            </span>
                        <?php endif; ?>
                        <textarea class="input" name="units[<?= e($unit['id']) ?>][notes]" rows="3" maxlength="2000"
                            x-model="notes" placeholder="Ajusta la descripción base según la foto, la visita y el criterio del perito."></textarea>
                    </label>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="mt-5 flex justify-end">
            <button class="btn-primary" type="submit" :disabled="busy"
                x-text="busy ? 'Guardando...' : 'Guardar unidades'">Guardar unidades</button>
        </div>
    </form>
    <?php foreach ($visibleUnits as $unit): ?>
        <div x-show="active === '<?= e($unit['id']) ?>'">
            <?php
            $photoUploadEmbedded = true;
            $photoUploadUnitId = (string) $unit['id'];
            $photoUploadUnitLabel = $labelInput($unit) ?: $unitLabel($unit);
            $photoUploadTypology = (string) $unit['igac_typology_hint'];
            require BASE_PATH . '/app/Views/appraisals/photo-upload.php';
            unset($photoUploadEmbedded, $photoUploadUnitId, $photoUploadUnitLabel, $photoUploadTypology);
            ?>
        </div>
    <?php endforeach; ?>
</section>
