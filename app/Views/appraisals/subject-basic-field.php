<?php
$help = $fieldHelp($key);
?>
<?php if (isset($subjectCatalog[$key])): [$label, $options] = $subjectCatalog[$key]; ?>
    <label class="label"><?= e($label) ?>
        <?php if ($help !== ''): ?><span class="help-dot" title="<?= e($help) ?>">?</span><?php endif; ?>
        <select class="input" name="<?= e($key) ?>">
            <option value="">Selecciona opción</option>
            <?php foreach ($options as $value => $option): ?>
                <option value="<?= e($value) ?>" <?= $sv($key) === (string) $value ? 'selected' : '' ?>><?= e($option) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
<?php elseif ($key === 'subject_reference_date'): ?>
    <label class="label">Fecha de referencia del sujeto
        <?php if ($help !== ''): ?><span class="help-dot" title="<?= e($help) ?>">?</span><?php endif; ?>
        <input class="input" type="date" name="subject_reference_date" value="<?= e($sv($key)) ?>">
    </label>
<?php else: [$label, $placeholder] = $textLabels[$key]; ?>
    <label class="label <?= in_array($key, ['restrictions', 'legal_urban_affectations', 'address'], true) ? 'md:col-span-2' : '' ?>">
        <?= e($label) ?>
        <?php if ($help !== ''): ?><span class="help-dot" title="<?= e($help) ?>">?</span><?php endif; ?>
        <input class="input" name="<?= e($key) ?>" value="<?= e($sv($key)) ?>" placeholder="<?= e($placeholder) ?>">
    </label>
<?php endif; ?>
