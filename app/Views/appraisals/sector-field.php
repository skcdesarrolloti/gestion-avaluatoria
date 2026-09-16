<?php
$value = (string) ($sector[$field] ?? '');
$help = (string) ($sectorHelps[$field] ?? '');
$placeholder = $type === 'textarea' ? 'Redacta la lectura técnica observada en campo.' : 'Diligencia según soporte o visita.';
?>
<label class="label"> <?= e($label) ?>
    <?php if ($help): ?><span class="help-dot" title="<?= e($help) ?>">?</span><?php endif; ?>
    <?php if ($type === 'select'): ?>
        <select class="input" name="<?= e($field) ?>">
            <option value="">Selecciona opción</option>
            <?php foreach (($sectorOptions[$field] ?? []) as $optionValue => $optionLabel): ?>
                <option value="<?= e((string) $optionValue) ?>" <?= $value === (string) $optionValue ? 'selected' : '' ?>>
                    <?= e((string) $optionLabel) ?>
                </option>
            <?php endforeach; ?>
        </select>
    <?php elseif ($type === 'textarea'): ?>
        <textarea class="input min-h-28" name="<?= e($field) ?>" placeholder="<?= e($placeholder) ?>"><?= e($value) ?></textarea>
    <?php else: ?>
        <input class="input" name="<?= e($field) ?>" value="<?= e($value) ?>" placeholder="<?= e($placeholder) ?>">
    <?php endif; ?>
</label>
