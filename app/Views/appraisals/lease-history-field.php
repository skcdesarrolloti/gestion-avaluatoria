<?php
$fieldName='lease[monthly_history]['.$month.']['.$monthlyField.']';
$fieldId='history-'.$month.'-'.$monthlyField; $fieldValue=(string)($monthlyValues[$monthlyField] ?? '');
$fieldError=$leaseErrors[$fieldName] ?? ''; $model=$monthlyModel.'.'.$monthlyField;
?>
<div class="<?= $definition['type']==='textarea'?'md:col-span-2':'' ?>">
    <label class="label" for="<?= e($fieldId) ?>"><?= e($definition['label']) ?></label>
    <?php if ($definition['type']==='select'): ?>
    <select class="input" id="<?= e($fieldId) ?>" name="<?= e($fieldName) ?>" x-model="<?= e($model) ?>"
        aria-describedby="<?= e($fieldId) ?>-help <?= e($fieldId) ?>-error" aria-invalid="<?= $fieldError!==''?'true':'false' ?>" :aria-invalid="Boolean(issues['<?= e($fieldName) ?>'])">
        <option value="">Por completar o verificar</option>
        <?php foreach ($definition['options'] as $value=>$label): ?><option value="<?= e($value) ?>" <?= $value===$fieldValue?'selected':'' ?>><?= e($label) ?></option><?php endforeach; ?>
    </select>
    <?php elseif ($definition['type']==='textarea'): ?>
    <textarea class="input" id="<?= e($fieldId) ?>" name="<?= e($fieldName) ?>" rows="2" maxlength="1500" x-model="<?= e($model) ?>"
        placeholder="<?= e($definition['help']) ?>" aria-describedby="<?= e($fieldId) ?>-help <?= e($fieldId) ?>-error"
        aria-invalid="<?= $fieldError!==''?'true':'false' ?>" :aria-invalid="Boolean(issues['<?= e($fieldName) ?>'])"><?= e($fieldValue) ?></textarea>
    <?php else: ?>
    <input class="input" id="<?= e($fieldId) ?>" name="<?= e($fieldName) ?>" type="text" inputmode="decimal" maxlength="30"
        value="<?= e($fieldValue) ?>" x-model="<?= e($model) ?>" placeholder="<?= e($definition['type']==='days'?'Días enteros; vacío si se desconoce':($definition['type']==='percent'?'Porcentaje documentado; 0 si no aplica':'Importe documentado en COP; vacío si se desconoce')) ?>"
        aria-describedby="<?= e($fieldId) ?>-help <?= e($fieldId) ?>-error" aria-invalid="<?= $fieldError!==''?'true':'false' ?>" :aria-invalid="Boolean(issues['<?= e($fieldName) ?>'])">
    <?php endif; ?>
    <p class="mt-1 text-xs text-slate-600" id="<?= e($fieldId) ?>-help"><?= e($definition['help']) ?></p>
    <p class="mt-1 text-sm text-red-700" role="alert" id="<?= e($fieldId) ?>-error" x-text="issues['<?= e($fieldName) ?>'] || ''"><?= e($fieldError) ?></p>
</div>
