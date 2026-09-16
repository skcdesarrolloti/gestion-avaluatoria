<?php
$unitId = (string) $unit['id'];
$firstAttributeGroup = (string) array_key_first($specialAttributeCatalog);
?>
<div class="mt-5 rounded-xl border border-slate-200 p-5"
    x-data="{ activeAttributeGroup: '<?= e($firstAttributeGroup) ?>' }"
    x-show="activeAttributes === '<?= e($unitId) ?>'">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h3 class="text-base font-semibold"><?= e($unit['label'] ?: $attributeUnitLabel($unit)) ?></h3>
        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
            <?= e($unit['igac_typology_hint'] ?: 'Tipología pendiente') ?>
        </span>
    </div>
    <div class="mt-4 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950">
        Selecciona solo atributos que realmente diferencian al sujeto. Si un atributo no aplica, déjalo sin diligenciar.
        Esta lectura servirá luego para orientar la búsqueda y homologación de comparables en el numeral 3.
    </div>
    <div class="mt-4 rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-sm leading-6 text-emerald-950"
        x-show="showPh">
        Como el encargo está marcado en PH, registra también las amenidades del conjunto y usa evidencia fotográfica
        cuando el atributo sea relevante.
    </div>
    <div class="mt-5 flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" role="tablist">
        <?php $attributeGroupNumber = 1; ?>
        <?php foreach ($specialAttributeCatalog as $groupKey => [$groupLabel, $attributes]): ?>
            <button class="min-h-11 shrink-0 rounded-lg px-4 py-2 text-sm font-semibold" type="button"
                <?= $groupKey === 'ph' ? 'x-show="showPh"' : '' ?>
                @click="activeAttributeGroup = '<?= e($groupKey) ?>'"
                :class="activeAttributeGroup === '<?= e($groupKey) ?>' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:bg-white/70'">
                <span class="mr-1 inline-flex size-6 items-center justify-center rounded-full bg-blue-700 text-xs text-white">
                    <?= $attributeGroupNumber ?>
                </span>
                <?= e($groupLabel) ?>
            </button>
            <?php $attributeGroupNumber++; ?>
        <?php endforeach; ?>
    </div>
    <div class="mt-5">
        <?php foreach ($specialAttributeCatalog as $groupKey => [$groupLabel, $attributes]): ?>
            <div class="overflow-x-auto rounded-xl border border-slate-200"
                x-show="activeAttributeGroup === '<?= e($groupKey) ?>'<?= $groupKey === 'ph' ? ' && showPh' : '' ?>">
                <div class="bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-800"><?= e($groupLabel) ?></div>
                <table class="table-fixed text-left text-sm" style="min-width: 104rem;">
                    <colgroup>
                        <col style="width: 17rem;">
                        <col style="width: 13rem;">
                        <col style="width: 12rem;">
                        <col style="width: 12rem;">
                        <col style="width: 12rem;">
                        <col style="width: 22rem;">
                        <col style="width: 16rem;">
                    </colgroup>
                    <thead class="bg-blue-900 text-xs uppercase tracking-wide text-white">
                        <tr>
                            <th class="px-3 py-3">Atributo</th>
                            <th class="px-3 py-3">Valor observado</th>
                            <th class="px-3 py-3">Estado</th>
                            <th class="px-3 py-3">Impacto</th>
                            <th class="px-3 py-3">Evidencia</th>
                            <th class="px-3 py-3">Observación</th>
                            <th class="px-3 py-3">Foto soporte</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php foreach ($attributes as $key => [$label, $help, $options]): ?>
                            <tr x-data="photoUpload('<?= e($attrValue($unit, $key, 'evidence')) ?>')">
                                <td class="px-3 py-3 align-top">
                                    <strong class="block text-slate-950"><?= e($label) ?></strong>
                                    <span class="mt-1 block max-w-xs text-xs leading-5 text-slate-500"><?= e($help) ?></span>
                                </td>
                                <td class="px-3 py-3 align-top">
                                    <select class="input min-w-48" name="unit_attributes[<?= e($unitId) ?>][items][<?= e($key) ?>][value]">
                                        <?php foreach ($options as $value => $text): ?>
                                            <option value="<?= e($value) ?>" <?= $attrValue($unit, $key, 'value') === $value ? 'selected' : '' ?>><?= e($text) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <?php foreach (['state' => 'state', 'impact' => 'impact', 'evidence' => 'evidence'] as $field => $optionKey): ?>
                                    <td class="px-3 py-3 align-top">
                                        <select class="input min-w-40" name="unit_attributes[<?= e($unitId) ?>][items][<?= e($key) ?>][<?= e($field) ?>]"
                                            <?= $field === 'evidence' ? 'x-model="evidence"' : '' ?>>
                                            <?php foreach ($specialAttributeOptions[$optionKey] as $value => $text): ?>
                                                <option value="<?= e($value) ?>" <?= $attrValue($unit, $key, $field) === $value ? 'selected' : '' ?>><?= e($text) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                <?php endforeach; ?>
                                <td class="px-3 py-3 align-top">
                                    <textarea class="input" name="unit_attributes[<?= e($unitId) ?>][items][<?= e($key) ?>][notes]"
                                        rows="3" maxlength="220" placeholder="Criterio escrito del perito."><?= e($attrValue($unit, $key, 'notes')) ?></textarea>
                                </td>
                                <td class="px-3 py-3 align-top">
                                    <div class="rounded-lg border border-dashed border-slate-300 bg-white p-2"
                                        x-show="evidence === 'foto'" @paste="paste($event)" tabindex="0">
                                        <div class="grid gap-2">
                                            <label class="btn-secondary min-h-10 w-full text-xs">Elegir archivo
                                                <input class="sr-only" type="file" name="attribute_photos[<?= e($unitId) ?>][<?= e($key) ?>][]"
                                                    accept="image/jpeg,image/png,image/webp" multiple x-ref="photos"
                                                    @change="update($event.target)">
                                            </label>
                                            <span class="rounded-lg border border-dashed border-slate-300 px-3 py-2 text-center text-xs text-slate-600"
                                                tabindex="0" @paste="paste($event)">Pegar Ctrl+V</span>
                                        </div>
                                        <p class="mt-2 truncate text-xs font-semibold text-teal-800" x-show="fileCountLabel"
                                            x-text="fileCountLabel" :title="fileNames"></p>
                                        <div class="mt-2 grid grid-cols-2 gap-2" x-show="previews.length">
                                            <template x-for="preview in previews" :key="preview.url">
                                                <img class="aspect-[4/3] w-full rounded-md border border-slate-200 object-contain"
                                                    :src="preview.url" :alt="preview.name">
                                            </template>
                                        </div>
                                    </div>
                                    <p class="min-w-52 rounded-lg bg-slate-50 p-3 text-xs text-slate-500"
                                        x-show="evidence !== 'foto'">
                                        Selecciona Foto en evidencia cuando necesites soporte visual.
                                    </p>
                                    <?php $evidencePhotos = $photosForAttribute($unitId, $key); ?>
                                    <?php if ($evidencePhotos): ?>
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            <?php foreach ($evidencePhotos as $photo): ?>
                                                <a class="block size-11 overflow-hidden rounded-md border border-slate-200 bg-slate-50"
                                                    href="<?= e(url('avaluos/' . $record['id'] . '/fotos/' . $photo['id'])) ?>" target="_blank" rel="noopener">
                                                    <img class="h-full w-full object-contain" alt="Evidencia de atributo"
                                                        src="<?= e(url('avaluos/' . $record['id'] . '/fotos/' . $photo['id'])) ?>" loading="lazy">
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    </div>
    <label class="label mt-5 block">Texto editable para el entregable del numeral 2.4
        <textarea class="input" name="unit_attributes[<?= e($unitId) ?>][report_text]" rows="5" maxlength="1500"
            placeholder="Redacción técnica sobre atributos diferenciales del sujeto."><?= e((string) ($unit['special_attributes_report_text'] ?? '')) ?></textarea>
    </label>
</div>
