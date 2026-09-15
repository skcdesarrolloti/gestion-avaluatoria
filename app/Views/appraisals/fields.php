<?php
$definitions = [
    'titulo' => ['Título de la ficha', 'Ej. Apartamento en Laureles', 160],
    'direccion' => ['Dirección', 'Ej. Calle 10 # 43-20', 220],
    'municipio' => ['Municipio', 'Ej. Medellín', 120],
];
?>
<?php foreach ($definitions as $name => [$label, $placeholder, $max]): ?>
    <div class="<?= $name === 'titulo' ? 'sm:col-span-2' : '' ?>">
        <label class="label" for="<?= e($name) ?>"><?= e($label) ?></label>
        <input class="input" id="<?= e($name) ?>" name="<?= e($name) ?>" x-model="fields.<?= e($name) ?>"
            placeholder="<?= e($placeholder) ?>" maxlength="<?= $max ?>"
            :aria-invalid="Boolean(errors.<?= e($name) ?>)" aria-describedby="error-<?= e($name) ?>">
        <p id="error-<?= e($name) ?>" class="field-error" x-text="errors.<?= e($name) ?> || ''"></p>
    </div>
<?php endforeach; ?>
<div class="sm:col-span-2">
    <label for="tipo" class="label">Tipo de avalúo</label>
    <select id="tipo" name="tipo" class="input" x-model="fields.tipo" :aria-invalid="Boolean(errors.tipo)" aria-describedby="error-tipo">
        <option value="">Selecciona un tipo de avalúo</option>
        <option value="urbano">Avalúo urbano</option><option value="posesion">Avalúo de posesión</option>
    </select>
    <p id="error-tipo" class="field-error" x-text="errors.tipo || ''"></p>
</div>
<div class="sm:col-span-2">
    <label for="observaciones" class="label">Observaciones</label>
    <textarea id="observaciones" name="observaciones" rows="5" class="input resize-y" x-model="fields.observaciones" maxlength="4000"
        placeholder="Describe el encargo, la información pendiente o las observaciones iniciales."
        :aria-invalid="Boolean(errors.observaciones)" aria-describedby="error-observaciones"></textarea>
    <p id="error-observaciones" class="field-error" x-text="errors.observaciones || ''"></p>
</div>
