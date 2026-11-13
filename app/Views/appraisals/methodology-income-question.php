<?php
$field='income_concept['.$name.']'; $fieldId='income-'.$name;
$savedValue=(string)($incomeValues[$name] ?? '');
$alreadyAnswered=isset($question['values'][$savedValue]) && !in_array($savedValue,['pending','unknown'],true) && !isset($incomeErrors[$field]);
?>
<div class="mt-5" x-show="showQuestion(<?= e(json_encode($name)) ?>)">
    <details class="rounded-xl border p-4" <?= !$alreadyAnswered?'open':'' ?> :open="issues[<?= e(json_encode($field)) ?>] ? true : $el.open">
        <summary class="min-h-11 cursor-pointer py-2 font-semibold">
            <?= e($question['label']) ?>
            <span class="mt-1 block text-sm font-normal text-slate-600" x-text="label(<?= e(json_encode($name)) ?>,<?= e(json_encode($question['values'],JSON_UNESCAPED_UNICODE)) ?>)"><?= e($question['values'][$savedValue] ?? 'Por completar o verificar') ?></span>
        </summary>
        <div class="mt-3">
            <label for="<?= e($fieldId) ?>" class="block font-semibold"><?= e($question['label']) ?></label>
            <select id="<?= e($fieldId) ?>" name="<?= e($field) ?>" class="input" x-model="values.<?= e($name) ?>"
                aria-describedby="<?= e($fieldId) ?>-hint <?= e($fieldId) ?>-error" aria-invalid="<?= isset($incomeErrors[$field])?'true':'false' ?>"
                :aria-invalid="Boolean(issues[<?= e(json_encode($field)) ?>])">
                <option value="">Selecciona una opción</option>
                <?php if ($savedValue!=='' && !isset($question['values'][$savedValue])): ?><option value="<?= e($savedValue) ?>" selected>Respuesta no admitida: selecciona una opción válida</option><?php endif; ?>
                <?php foreach ($question['values'] as $value=>$label): ?><option value="<?= e($value) ?>" <?= $savedValue===$value?'selected':'' ?>><?= e($label) ?></option><?php endforeach; ?>
            </select>
            <p id="<?= e($fieldId) ?>-hint" class="mt-2 text-sm text-slate-600"><?= e($question['meaning']) ?></p>
            <p id="<?= e($fieldId) ?>-error" class="mt-2 text-sm text-red-800" role="alert" x-text="issues[<?= e(json_encode($field)) ?>] || ''"><?= e($incomeErrors[$field] ?? '') ?></p>
            <?php require __DIR__.'/methodology-income-help.php'; ?>
        </div>
    </details>
    <?php foreach (\App\Services\IncomeConcept::texts() as $textKey=>$textDefinition):
        if (\App\Services\IncomePreparationNotes::parent($textKey)!==$name) continue;
        require __DIR__.'/methodology-income-note.php';
    endforeach; ?>
</div>
