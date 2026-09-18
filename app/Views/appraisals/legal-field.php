<?php
$value = $field($key);
?>
<?php if (in_array($key, $checkboxFields, true)): ?>
    <label class="flex min-h-12 items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-800">
        <input class="h-5 w-5 rounded border-slate-300 text-teal-700 focus:ring-teal-600"
            type="checkbox" name="<?= e($key) ?>" value="Sí" <?= $value === 'Sí' ? 'checked' : '' ?>>
        <?= e($label($key)) ?>
    </label>
<?php else: ?>
    <label class="label <?= $isTextarea($key) ? 'md:col-span-2' : '' ?>"><?= e($label($key)) ?>
        <?php if (isset($selectOptions[$key])): ?>
            <select class="input mt-2" name="<?= e($key) ?>">
                <?php foreach ($selectOptions[$key] as $optionValue => $text): ?>
                    <option value="<?= e($optionValue) ?>" <?= $value === $optionValue ? 'selected' : '' ?>>
                        <?= e($text) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php elseif ($isTextarea($key)): ?>
            <textarea class="input mt-2 min-h-28" name="<?= e($key) ?>"
                placeholder="Pendiente de lectura o revisión manual"><?= e($value) ?></textarea>
        <?php else: ?>
            <input class="input mt-2 <?= $value === '' ? 'bg-slate-50' : '' ?>" name="<?= e($key) ?>"
                value="<?= e($value) ?>" placeholder="Pendiente de lectura o revisión manual">
        <?php endif; ?>
    </label>
<?php endif; ?>
