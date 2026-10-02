<?php
$unitFlow = \App\Services\MethodologyWorkflow::saved($record);
$unitMethod = (string) ($unitFlow[(string) ($unit['id'] ?? '')]['method'] ?? '');
?>
<label class="label">Método de valoración
    <input type="hidden" name="config_units[<?= e($key) ?>][original_method]" value="<?= e($unitMethod) ?>" data-original-method="<?= e($key) ?>">
    <select class="input" name="config_units[<?= e($key) ?>][method_choice]">
        <option value="">Selecciona cómo valorar esta unidad</option>
        <?php foreach (\App\Services\MethodologyWorkflow::METHODS as $value => $text): ?>
            <option value="<?= e($value) ?>" <?= $unitMethod === $value ? 'selected' : '' ?>><?= e($text) ?></option>
        <?php endforeach; ?>
    </select>
</label>
