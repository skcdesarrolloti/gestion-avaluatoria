<?php use App\Support\AppraisalCatalog; ?>
<?php $definitions = AppraisalCatalog::textFields(); ?>
<?php foreach ($definitions as $name => [$label, $placeholder, $max]): ?>
    <?php if ($name === 'observaciones') { continue; } ?>
    <div class="<?= $name === 'titulo' ? 'sm:col-span-2' : '' ?>">
        <label class="label" for="<?= e($name) ?>"><?= e($label) ?></label>
        <input class="input" id="<?= e($name) ?>" name="<?= e($name) ?>" x-model="fields.<?= e($name) ?>"
            placeholder="<?= e($placeholder) ?>" maxlength="<?= $max ?>"
            :aria-invalid="Boolean(errors.<?= e($name) ?>)" aria-describedby="error-<?= e($name) ?>">
        <p id="error-<?= e($name) ?>" class="field-error" x-text="errors.<?= e($name) ?> || ''"></p>
    </div>
<?php endforeach; ?>
<?php foreach (AppraisalCatalog::selectFields() as $name => [$label, $placeholder, $max, $help, $options]): ?>
    <div>
        <label for="<?= e($name) ?>" class="label"><?= e($label) ?></label>
        <select id="<?= e($name) ?>" name="<?= e($name) ?>" class="input" x-model="fields.<?= e($name) ?>"
            :aria-invalid="Boolean(errors.<?= e($name) ?>)" aria-describedby="help-<?= e($name) ?> error-<?= e($name) ?>">
            <option value=""><?= e($placeholder) ?></option>
            <?php foreach ($options as $value => $text): ?>
                <option value="<?= e($value) ?>"><?= e($text) ?></option>
            <?php endforeach; ?>
        </select>
        <p id="help-<?= e($name) ?>" class="mt-1 text-xs leading-5 text-slate-500"><?= e($help) ?></p>
        <p id="error-<?= e($name) ?>" class="field-error" x-text="errors.<?= e($name) ?> || ''"></p>
    </div>
<?php endforeach; ?>
<div class="sm:col-span-2">
    <label for="observaciones" class="label">Observaciones</label>
    <textarea id="observaciones" name="observaciones" rows="5" class="input resize-y" x-model="fields.observaciones" maxlength="4000"
        placeholder="Describe el encargo, la información pendiente o las observaciones iniciales."
        :aria-invalid="Boolean(errors.observaciones)" aria-describedby="error-observaciones"></textarea>
    <p id="error-observaciones" class="field-error" x-text="errors.observaciones || ''"></p>
</div>
