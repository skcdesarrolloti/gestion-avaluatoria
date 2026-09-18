<?php
$unitId = (string) $unit['id'];
$firstAttributeGroup = (string) array_key_first($specialAttributeCatalog);
$score = $attributeScore($unit, $specialAttributeCatalog);
?>
<div class="mt-5 rounded-xl border border-slate-200 p-5"
    x-data="{ activeAttributeGroup: '<?= e($firstAttributeGroup) ?>' }"
    x-show="activeAttributes === '<?= e($unitId) ?>'">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h3 class="text-base font-semibold"><?= e($unit['label'] ?: $attributeUnitLabel($unit)) ?></h3>
        <div class="flex flex-wrap gap-2">
            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                <?= e($unit['igac_typology_hint'] ?: 'Tipología pendiente') ?>
            </span>
            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-800">
                Índice <?= $score['score'] === null ? 'pendiente' : e((string) $score['score']) . ' / 5 · ' . e($score['label']) ?>
            </span>
        </div>
    </div>
    <div class="mt-4 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950">
        Selecciona solo atributos que realmente diferencian al sujeto. Si un atributo no aplica, déjalo sin diligenciar.
        Califica de 1 a 5 y asigna peso bajo, medio o alto solo cuando el atributo pueda incidir en el valor.
    </div>
    <div class="mt-5 flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" role="tablist">
        <?php $attributeGroupNumber = 1; ?>
        <?php foreach ($specialAttributeCatalog as $groupKey => [$groupLabel, $attributes]): ?>
            <button class="min-h-11 shrink-0 rounded-lg px-4 py-2 text-sm font-semibold" type="button"
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
                x-show="activeAttributeGroup === '<?= e($groupKey) ?>'">
                <div class="bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-800"><?= e($groupLabel) ?></div>
                <table class="table-fixed text-left text-sm" style="min-width: 91rem;">
                    <colgroup>
                        <col style="width: 17rem;">
                        <col style="width: 13rem;">
                        <col style="width: 12rem;">
                        <col style="width: 12rem;">
                        <col style="width: 12rem;">
                        <col style="width: 13rem;">
                        <col style="width: 10rem;">
                        <col style="width: 22rem;">
                    </colgroup>
                    <thead class="bg-blue-900 text-xs uppercase tracking-wide text-white">
                        <tr>
                            <th class="px-3 py-3">Atributo</th>
                            <th class="px-3 py-3">Valor observado</th>
                            <th class="px-3 py-3">Impacto</th>
                            <th class="px-3 py-3">Evidencia</th>
                            <th class="px-3 py-3">Calificación</th>
                            <th class="px-3 py-3">Peso</th>
                            <th class="px-3 py-3">Observación</th>
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
                                <?php foreach (['impact' => 'impact', 'evidence' => 'evidence'] as $field => $optionKey): ?>
                                    <td class="px-3 py-3 align-top">
                                        <select class="input min-w-40" name="unit_attributes[<?= e($unitId) ?>][items][<?= e($key) ?>][<?= e($field) ?>]"
                                            <?= $field === 'evidence' ? 'x-model="evidence"' : '' ?>>
                                            <?php foreach ($specialAttributeOptions[$optionKey] as $value => $text): ?>
                                                <option value="<?= e($value) ?>" <?= $attrValue($unit, $key, $field) === $value ? 'selected' : '' ?>><?= e($text) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                <?php endforeach; ?>
                                <?php foreach (['rating' => 'rating', 'weight' => 'weight'] as $field => $optionKey): ?>
                                    <td class="px-3 py-3 align-top">
                                        <select class="input min-w-40" name="unit_attributes[<?= e($unitId) ?>][items][<?= e($key) ?>][<?= e($field) ?>]">
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
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    </div>
</div>
