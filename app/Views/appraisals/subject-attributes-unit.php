<?php $unitId = (string) $unit['id']; ?>
<div class="mt-5 rounded-xl border border-slate-200 p-5" x-show="activeAttributes === '<?= e($unitId) ?>'">
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
    <?php if ($hasHorizontalProperty): ?>
        <div class="mt-4 rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-sm leading-6 text-emerald-950">
            Como el encargo está marcado en PH, registra también las amenidades del conjunto y usa evidencia fotográfica
            cuando el atributo sea relevante.
        </div>
    <?php endif; ?>
    <div class="mt-5 space-y-5">
        <?php foreach ($specialAttributeCatalog as $groupKey => [$groupLabel, $attributes]): ?>
            <?php if ($groupKey === 'ph' && !$hasHorizontalProperty) continue; ?>
            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <div class="bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-800"><?= e($groupLabel) ?></div>
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-blue-900 text-xs uppercase tracking-wide text-white">
                        <tr>
                            <th class="px-3 py-3">Atributo</th>
                            <th class="px-3 py-3">Valor observado</th>
                            <th class="px-3 py-3">Estado</th>
                            <th class="px-3 py-3">Impacto</th>
                            <th class="px-3 py-3">Evidencia</th>
                            <th class="px-3 py-3">Observación</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php foreach ($attributes as $key => [$label, $help, $options]): ?>
                            <tr x-data="{ evidence: '<?= e($attrValue($unit, $key, 'evidence')) ?>' }">
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
                                    <input class="input min-w-64" name="unit_attributes[<?= e($unitId) ?>][items][<?= e($key) ?>][notes]"
                                        value="<?= e($attrValue($unit, $key, 'notes')) ?>" placeholder="Soporte o criterio del perito">
                                    <div class="mt-2 rounded-lg border border-dashed border-slate-300 bg-white p-2"
                                        x-show="evidence === 'foto'"
                                        x-data="photoUpload" @paste="paste($event)" tabindex="0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <label class="btn-secondary min-h-10 text-xs">Adjuntar foto
                                                <input class="sr-only" type="file" name="attribute_photos[<?= e($unitId) ?>][<?= e($key) ?>][]"
                                                    accept="image/jpeg,image/png,image/webp" multiple x-ref="photos"
                                                    @change="update($event.target)">
                                            </label>
                                            <span class="text-xs text-slate-500">o pega aquí con Ctrl+V</span>
                                        </div>
                                        <p class="mt-1 text-xs font-semibold text-teal-800" x-show="fileNames" x-text="fileNames"></p>
                                    </div>
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
