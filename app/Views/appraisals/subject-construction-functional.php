<?php
$unitType = (string) (($unit['property_type'] ?? '') ?: ($record['tipo_inmueble'] ?? ''));
$functionalFields = \App\Support\AppraisalFunctionalVariableCatalog::fieldsFor($unitType);
$functionalGuide = \App\Support\AppraisalFunctionalVariableCatalog::guideFor($unitType);
$functionalGroups = \App\Support\AppraisalFunctionalVariableCatalog::factorGroupsFor($unitType);
$selectValue = static fn (string $key, string $value): string => $cv($unit, $key) === $value ? 'selected' : '';
?>
<div class="mt-5 space-y-5" x-show="activeConstructionDetail === 'funcionales'">
    <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950">
        <strong><?= e($functionalGuide['title'] ?? 'Tipología pendiente') ?>:</strong>
        <?= e($functionalGuide['summary'] ?? 'Define la tipología para activar los factores pertinentes.') ?>
    </div>
    <div class="grid gap-3 lg:grid-cols-4">
        <?php foreach ($functionalGroups as $group => $items): ?>
            <div class="rounded-xl border border-slate-200 bg-white p-4">
                <p class="text-xs font-semibold uppercase text-slate-500"><?= e($group) ?></p>
                <ul class="mt-2 space-y-1 text-sm leading-5 text-slate-700">
                    <?php foreach ($items as $item): ?>
                        <li>- <?= e($item) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="grid gap-5 md:grid-cols-3">
        <?php foreach ($functionalFields as $key => $definition): ?>
            <?php if (($definition['kind'] ?? '') === 'textarea'): ?>
                <label class="label md:col-span-3"><?= e($definition['label']) ?>
                    <textarea class="input" name="unit_constructions[<?= e($unitId) ?>][<?= e($key) ?>]" rows="3"
                        placeholder="<?= e($definition['placeholder'] ?? '') ?>"><?= e($cv($unit, $key)) ?></textarea>
                </label>
                <?php continue; ?>
            <?php endif; ?>
            <label class="label"><?= e($definition['label']) ?>
                <?php if (($definition['kind'] ?? '') === 'select'): ?>
                    <select class="input" name="unit_constructions[<?= e($unitId) ?>][<?= e($key) ?>]">
                        <?php foreach (($definition['options'] ?? []) as $value => $label): ?>
                            <option value="<?= e($value) ?>" <?= $selectValue($key, (string) $value) ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <input class="input" name="unit_constructions[<?= e($unitId) ?>][<?= e($key) ?>]"
                        inputmode="<?= e(($definition['kind'] ?? '') === 'number' ? 'numeric' : 'decimal') ?>"
                        value="<?= e($cv($unit, $key)) ?>" placeholder="<?= e($definition['placeholder'] ?? '') ?>">
                <?php endif; ?>
                <?php if (($definition['help'] ?? '') !== ''): ?>
                    <span class="mt-1 block text-xs text-slate-500"><?= e($definition['help']) ?></span>
                <?php endif; ?>
            </label>
        <?php endforeach; ?>
    </div>
</div>
