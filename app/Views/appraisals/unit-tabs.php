<?php
$subjectUnitsOld=json_decode(\App\Core\Session::pullFlash('subject_units_'.$record['id']) ?: '{}',true) ?: [];
foreach ($units as &$unitRow) $unitRow=array_replace($unitRow,$subjectUnitsOld['units'][$unitRow['id']] ?? []);
unset($unitRow);
$unitLabel = static function (array $unit): string {
    return ($unit['unit_kind'] === 'annex' ? 'Anexo ' : 'Unidad ') . (int) $unit['unit_index'];
};
$visibleUnits = array_values(array_filter($units, static fn (array $unit): bool => $unit['unit_kind'] !== 'common'));
$hasPhotos = !empty($photos);
$typologyOptions = $igacTypologiesByCategory ?? [];
$subjectActionBase = $subjectActionBase ?? 'avaluos/' . $record['id'] . '/expediente';
$labelInput = static fn (array $unit): string => (string) $unit['label'] === $unitLabel($unit) ? '' : (string) $unit['label'];
$labelMap = [];
foreach ($visibleUnits as $unit) $labelMap[$unit['id']] = $labelInput($unit);
?>
<section id="unidades-capitulo-3" class="scroll-mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8"
    x-init="$nextTick(() => { if (<?= ($_GET['section'] ?? '') === 'tipologias' ? 'true' : 'false' ?>) $el.scrollIntoView() })"
    x-data="{
        active: '<?= e(\App\Services\SubjectUnitNavigation::selected($visibleUnits)) ?>',
        labels: <?= e(json_encode($labelMap, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
        typologies: <?= e(json_encode($typologyOptions, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
        igacFamilies: <?= e(json_encode(\App\Support\IgacTypologySelection::families(), JSON_THROW_ON_ERROR)) ?>,
        igacFilterRules: <?= e(json_encode(\App\Support\IgacTypologyFilter::rules(), JSON_THROW_ON_ERROR)) ?>,
        constructionIgacCategories: <?= e(json_encode(\App\Support\AppraisalConstructionTypeCatalog::igacCategories(), JSON_THROW_ON_ERROR)) ?>,
        imageBase: <?= e(json_encode(url('assets/tipologias-igac/images'), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
        imageUrl(item) { return item?.image ? this.imageBase + '/' + encodeURIComponent(item.image) : '' },
        selectedItem(igacCategory, igacHint) {
            return (this.typologies[igacCategory] || []).find(item => item.value === igacHint) || null
        },
        descriptionDraft(item) {
            if (!item) return ''
            return [item.description, item.specifications ? 'Especificaciones IGAC: ' + item.specifications : '']
                .filter(Boolean).join('\n\n')
        }
    }">
    <?php require __DIR__ . '/methodology-return.php'; ?>
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
        data-module-autosave data-save-in-place data-autosave-endpoint="<?= e(url($subjectActionBase . '/unidades/autoguardar')) ?>"
        data-autosave-topic="<?= e('appraisal:' . $record['id'] . ':subject-units') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="version" value="<?= (int)($subjectUnitsOld['version'] ?? $record['version'] ?? 0) ?>">
        <?php if ($subjectUnitsOld): ?><p role="alert" class="rounded-lg bg-amber-50 p-3"><?= e($subjectUnitsOld['message']) ?></p><?php endif; ?>
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
                x-data="igacUnitSelector({
                    constructionType: <?= e(json_encode((string)($unit['unit_kind']==='annex'?($unit['construction_type'] ?? ''):($unit['property_type'] ?? '')), JSON_THROW_ON_ERROR)) ?>,
                    defaultConstructionType: <?= e(json_encode($unit['unit_kind']==='annex'?'':(string)($record['tipo_inmueble'] ?? ''), JSON_THROW_ON_ERROR)) ?>,
                    unitKind: <?= e(json_encode($unit['unit_kind'], JSON_THROW_ON_ERROR)) ?>,
                    igacCategory: <?= e(json_encode((string) $unit['igac_category'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
                    igacHint: <?= e(json_encode((string) $unit['igac_typology_hint'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
                    notes: <?= e(json_encode((string) $unit['notes'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
                    browserOpen: false
                })">
                <div class="grid gap-5 md:grid-cols-2">
                    <label class="label">Nombre de la unidad definido por el analista
                        <input class="input" name="units[<?= e($unit['id']) ?>][label]" maxlength="120"
                            x-model="labels['<?= e($unit['id']) ?>']" value="<?= e($labelInput($unit)) ?>" placeholder="Ej. Casa principal, Local 1, Piscina">
                        <input type="hidden" name="units[<?= e($unit['id']) ?>][default_label]" value="<?= e($unitLabel($unit)) ?>">
                    </label>
                    <?php require __DIR__ . '/subject-unit-kind.php'; ?>
                    <?php require __DIR__ . '/subject-igac-selector.php'; ?>
                    <label class="label md:col-span-2">
                        Descripción física propia de <?= e($labelInput($unit) ?: $unitLabel($unit)) ?>
                        <span class="mt-1 block text-xs font-normal text-slate-600">Describe ubicación interna, acceso, uso y características observadas. Para garaje: número o celda, nivel y si es cubierto; para depósito: identificación, cerramiento y condiciones propias. El texto IGAC es sólo una base para contrastar.</span>
                        <?php if ($hasPhotos): ?>
                            <span class="mt-3 block rounded-xl border border-emerald-50 bg-emerald-50 p-3 text-xs leading-5 text-emerald-800"
                                x-show="selectedItem(igacCategory, igacHint)">
                                <strong class="block">Sugerencia IGAC para contrastar con la foto real</strong>
                                <span class="mt-1 block" x-text="descriptionDraft(selectedItem(igacCategory, igacHint))"></span>
                                <button class="btn-secondary mt-3 min-h-10 bg-white text-xs" type="button"
                                    @click="notes = descriptionDraft(selectedItem(igacCategory, igacHint)); $dispatch('input')">
                                    Pasar sugerencia al campo editable
                                </button>
                            </span>
                        <?php endif; ?>
                        <textarea class="input" name="units[<?= e($unit['id']) ?>][notes]" rows="3" maxlength="2000"
                            x-model="notes" placeholder="Ajusta la descripción base según la foto, la visita y el criterio del perito."><?= e($unit['notes']) ?></textarea>
                    </label>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="mt-5 flex justify-end">
            <p class="mr-auto self-center text-xs font-semibold text-slate-500" data-autosave-status>
                Autoguardado activo
            </p>
            <button class="btn-primary" type="submit">Guardar unidades / reintentar</button>
        </div>
    </form>
    <?php foreach ($visibleUnits as $unit): ?>
        <div x-show="active === '<?= e($unit['id']) ?>'">
            <?php require __DIR__ . '/subject-unit-occupation.php'; ?>
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
