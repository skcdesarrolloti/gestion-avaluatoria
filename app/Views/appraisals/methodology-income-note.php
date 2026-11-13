<?php
[$textLabel,$placeholder,$condition]=$textDefinition;
$textField='income_concept['.$textKey.']';
?>
<div class="mt-4" x-show="(<?= e($condition) ?>) || Boolean(issues[<?= e(json_encode($textField)) ?>])">
    <label class="block font-semibold" for="income-<?= e($textKey) ?>"><?= e($textLabel) ?></label>
    <textarea class="input" id="income-<?= e($textKey) ?>" name="<?= e($textField) ?>" x-model="values.<?= e($textKey) ?>" rows="3" maxlength="1000"
        placeholder="<?= e($placeholder) ?>" aria-describedby="income-<?= e($textKey) ?>-hint income-<?= e($textKey) ?>-error"
        aria-invalid="<?= isset($incomeErrors[$textField])?'true':'false' ?>" :aria-invalid="Boolean(issues[<?= e(json_encode($textField)) ?>])"><?= e($incomeValues[$textKey] ?? '') ?></textarea>
    <p id="income-<?= e($textKey) ?>-hint" class="mt-1 text-sm text-slate-600">Opcional en el borrador · máximo 1000 caracteres. Conserva fuente y fecha; lo desconocido queda pendiente. Cambiar de opción conserva la nota.</p>
    <p id="income-<?= e($textKey) ?>-error" class="mt-2 text-sm text-red-800" role="alert" x-text="issues[<?= e(json_encode($textField)) ?>] || ''"><?= e($incomeErrors[$textField] ?? '') ?></p>
</div>
