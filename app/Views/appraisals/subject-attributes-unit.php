<?php
$unitId = (string) $unit['id'];
$catalog = $unitSpecialAttributeCatalog ?? $specialAttributeCatalog;
$score = $attributeScore($unit, $catalog);
$unitType = (string) (($unit['property_type'] ?? '') ?: ($record['tipo_inmueble'] ?? ''));
?>
<div class="mt-5 rounded-xl border border-slate-200 p-5" data-attribute-unit data-unit-id="<?= e($unitId) ?>"
    x-show="activeAttributes === '<?= e($unitId) ?>'">
    <input type="hidden" name="unit_attributes[<?= e($unitId) ?>][_present]" value="1">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h3 class="text-base font-semibold"><?= e($unit['label'] ?: $attributeUnitLabel($unit)) ?></h3>
        <div class="flex flex-wrap gap-2">
            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                <?= e($unitType !== '' ? ucfirst($unitType) : 'Tipo pendiente') ?>
            </span>
            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                <?= e($unit['igac_typology_hint'] ?: 'Tipología IGAC pendiente') ?>
            </span>
            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-800">
                <span x-text="unitScoreText('<?= e($unitId) ?>')">
                Ajuste <?= e($formatAttributeAdjustment($score['score'] === null ? null : (float) $score['adjustment'])) ?>
                <?= $score['score'] === null ? '' : ' · índice ' . e((string) $score['percent']) . '%' ?>
                </span>
            </span>
        </div>
    </div>
    <div class="mt-4 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950">
        Selecciona solo condiciones que realmente diferencian al sujeto. Si no aplica, déjalo sin diligenciar.
        Califica de 1 a 5: por debajo de 3 resta como demérito; por encima de 3 suma como atributo.
        El peso se coloca en medio automáticamente; cámbialo a bajo o alto solo cuando el efecto real lo justifique.
    </div>
    <div class="mt-5 flex flex-wrap gap-2 rounded-xl bg-slate-100 p-3 text-xs font-semibold text-slate-700">
        <?php foreach ($catalog as [$groupLabel]): ?>
            <span class="rounded-full bg-white px-3 py-1"><?= e($groupLabel) ?></span>
        <?php endforeach; ?>
    </div>
    <?php if (count($catalog) === 1): ?>
        <p class="mt-3 rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm leading-6 text-amber-900">
            Solo aparece Base común porque esta unidad no tiene tipo de inmueble definido. Ve a 3.1 Ficha básica del sujeto, pestaña Identificación, y selecciona casa, apartamento, lote, local, oficina, bodega u otro tipo para cargar los atributos específicos.
        </p>
    <?php endif; ?>
    <div class="mt-4 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-amber-100 bg-amber-50 p-4 text-sm text-amber-950">
        <span><strong>Selección valuatoria:</strong> revisa la lista completa del tipo de inmueble y marca solo los atributos que incidan en valor. Se sugiere trabajar máximo 6, sin bloquear casos especiales.</span>
        <span class="rounded-full px-3 py-1 text-xs font-semibold" :class="unitSuggestionClass('<?= e($unitId) ?>')" x-text="unitLimitText('<?= e($unitId) ?>')"></span>
    </div>
    <div class="mt-5 space-y-5">
        <?php foreach ($catalog as $groupKey => [$groupLabel, $attributes]): ?>
            <div class="rounded-xl border border-slate-200">
                <div class="bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-800"><?= e($groupLabel) ?></div>
                <div class="grid gap-3 p-4 lg:grid-cols-2">
                    <?php foreach ($attributes as $key => [$label, $help, $options]): ?>
                        <?php $enabled = $attrHasValue($unit, $key); ?>
                        <div class="rounded-xl border border-slate-200 bg-white p-4" data-attribute-row
                            x-data="{ enabled: <?= $enabled ? 'true' : 'false' ?> }"
                            :class="enabled ? 'ring-1 ring-blue-200' : ''">
                            <label class="flex items-start gap-3">
                                <input class="mt-1 size-5 shrink-0" type="checkbox" data-attribute-toggle x-model="enabled">
                                <span>
                                    <strong class="block text-sm text-slate-950"><?= e($label) ?></strong>
                                    <span class="mt-1 block text-xs leading-5 text-slate-500"><?= e($help) ?></span>
                                </span>
                            </label>
                            <div class="mt-4 grid gap-4 md:grid-cols-2" x-show="enabled" x-cloak>
                                <label class="label md:col-span-2">Valor observado
                                    <select class="input" name="unit_attributes[<?= e($unitId) ?>][items][<?= e($key) ?>][value]" :disabled="!enabled">
                                        <?php foreach ($options as $value => $text): ?>
                                            <option value="<?= e($value) ?>" <?= $attrValue($unit, $key, 'value') === $value ? 'selected' : '' ?>><?= e($text) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </label>
                                <?php foreach (['impact' => 'Impacto', 'evidence' => 'Evidencia'] as $field => $fieldLabel): ?>
                                    <label class="label"><?= e($fieldLabel) ?>
                                        <select class="input" name="unit_attributes[<?= e($unitId) ?>][items][<?= e($key) ?>][<?= e($field) ?>]" :disabled="!enabled">
                                            <?php foreach ($specialAttributeOptions[$field] as $value => $text): ?>
                                                <option value="<?= e($value) ?>" <?= $attrValue($unit, $key, $field) === $value ? 'selected' : '' ?>><?= e($text) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </label>
                                <?php endforeach; ?>
                                <?php foreach (['rating' => 'Calificación', 'weight' => 'Peso'] as $field => $fieldLabel): ?>
                                    <label class="label"><?= e($fieldLabel) ?>
                                        <select class="input" name="unit_attributes[<?= e($unitId) ?>][items][<?= e($key) ?>][<?= e($field) ?>]"
                                            <?= $field === 'rating' ? 'data-attribute-rating' : 'data-attribute-weight' ?> :disabled="!enabled">
                                            <?php foreach ($specialAttributeOptions[$field] as $value => $text): ?>
                                                <option value="<?= e($value) ?>" <?= $attrValue($unit, $key, $field) === $value ? 'selected' : '' ?>><?= e($text) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </label>
                                <?php endforeach; ?>
                                <label class="label md:col-span-2">Observación valuatoria
                                    <textarea class="input" name="unit_attributes[<?= e($unitId) ?>][items][<?= e($key) ?>][notes]"
                                        rows="3" maxlength="220" placeholder="Criterio escrito del perito." :disabled="!enabled"><?= e($attrValue($unit, $key, 'notes')) ?></textarea>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
