<?php
$value = (string) ($sector[$field] ?? '');
$help = (string) ($sectorHelps[$field] ?? '');
$placeholder = match ($field) {
    'sector_map_url' => 'https://www.google.com/maps/search/?api=1&query=...',
    'sector_latitude', 'sector_longitude' => 'Ej. 10.407952',
    'sector_area_ha', 'sector_perimeter_m' => 'Dato de fuente oficial o medición manual',
    default => $type === 'textarea' ? 'Redacta la lectura técnica observada en campo.' : 'Diligencia según soporte o visita.',
};
$formAttr = isset($sectorFormId) ? ' form="' . e($sectorFormId) . '"' : '';
?>
<label class="label"> <?= e($fieldLabel) ?>
    <?php if ($help): ?><span class="help-dot" title="<?= e($help) ?>">?</span><?php endif; ?>
    <?php if ($type === 'select'): ?>
        <select class="input" name="<?= e($field) ?>"<?= $formAttr ?>>
            <option value="">Selecciona opción</option>
            <?php foreach (($sectorOptions[$field] ?? []) as $optionValue => $optionLabel): ?>
                <option value="<?= e((string) $optionValue) ?>" <?= $value === (string) $optionValue ? 'selected' : '' ?>>
                    <?= e((string) $optionLabel) ?>
                </option>
            <?php endforeach; ?>
        </select>
    <?php elseif ($type === 'textarea'): ?>
        <textarea class="input min-h-28" name="<?= e($field) ?>"<?= $formAttr ?> placeholder="<?= e($placeholder) ?>"><?= e($value) ?></textarea>
    <?php else: ?>
        <input class="input" name="<?= e($field) ?>"<?= $formAttr ?> value="<?= e($value) ?>" placeholder="<?= e($placeholder) ?>">
    <?php endif; ?>
</label>
