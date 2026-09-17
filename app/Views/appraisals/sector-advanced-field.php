<?php
use App\Support\AppraisalSectorAdvancedCatalog;
$value = $sectionValues[$fieldName] ?? ($fieldType === 'multiselect' ? [] : '');
$name = 'sector_sections[' . $sectionCode . '][' . $fieldName . ']';
$id = 'sector_' . $sectionCode . '_' . $fieldName;
$options = $optionKey ? AppraisalSectorAdvancedCatalog::options($optionKey) : [];
$formAttr = isset($sectorFormId) ? ' form="' . e($sectorFormId) . '"' : '';
$help = AppraisalSectorAdvancedCatalog::helps()[$fieldName]
    ?? 'Completa este dato con fuente, fecha o validación de campo. Si no está confirmado, deja escrito qué falta validar.';
?>
<?php if ($fieldType === 'textarea'): ?>
    <label class="label md:col-span-2" for="<?= e($id) ?>"><?= e($fieldLabel) ?>
        <span class="help-dot" title="<?= e($help) ?>">?</span>
        <textarea class="input mt-2 min-h-28" id="<?= e($id) ?>" name="<?= e($name) ?>"<?= $formAttr ?>
            placeholder="Completa o ajusta este dato con fuente, fecha o validación de campo."><?= e((string) $value) ?></textarea>
        <span class="mt-2 block text-xs font-normal leading-5 text-slate-600"><?= e($help) ?></span>
    </label>
<?php elseif ($fieldType === 'select'): ?>
    <label class="label" for="<?= e($id) ?>"><?= e($fieldLabel) ?>
        <span class="help-dot" title="<?= e($help) ?>">?</span>
        <select class="input mt-2" id="<?= e($id) ?>" name="<?= e($name) ?>"<?= $formAttr ?>>
            <option value="">Selecciona una opción</option>
            <?php foreach ($options as $optionValue => $optionLabel): ?>
                <option value="<?= e((string) $optionValue) ?>" <?= (string) $value === (string) $optionValue ? 'selected' : '' ?>>
                    <?= e((string) $optionLabel) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <span class="mt-2 block text-xs font-normal leading-5 text-slate-600"><?= e($help) ?></span>
    </label>
<?php elseif ($fieldType === 'multiselect'): ?>
    <?php $selected = is_array($value) ? array_map('strval', $value) : []; ?>
    <fieldset class="md:col-span-2 rounded-xl border border-slate-200 bg-slate-50 p-4">
        <legend class="label"><?= e($fieldLabel) ?><span class="help-dot" title="<?= e($help) ?>">?</span></legend>
        <p class="mt-2 text-xs leading-5 text-slate-600"><?= e($help) ?></p>
        <div class="mt-3 grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($options as $optionValue => $optionLabel): ?>
                <label class="flex min-h-11 items-center gap-2 rounded-lg bg-white px-3 py-2 text-sm text-slate-700">
                    <input class="size-4" type="checkbox" name="<?= e($name) ?>[]"<?= $formAttr ?> value="<?= e((string) $optionValue) ?>"
                        <?= in_array((string) $optionValue, $selected, true) ? 'checked' : '' ?>>
                    <span><?= e((string) $optionLabel) ?></span>
                </label>
            <?php endforeach; ?>
        </div>
    </fieldset>
<?php else: ?>
    <label class="label" for="<?= e($id) ?>"><?= e($fieldLabel) ?>
        <span class="help-dot" title="<?= e($help) ?>">?</span>
        <input class="input mt-2" id="<?= e($id) ?>" name="<?= e($name) ?>"<?= $formAttr ?> value="<?= e((string) $value) ?>"
            placeholder="Dato, fuente o referencia verificable">
        <span class="mt-2 block text-xs font-normal leading-5 text-slate-600"><?= e($help) ?></span>
    </label>
<?php endif; ?>
