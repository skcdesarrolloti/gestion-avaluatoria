<?php
$fieldName='lease[monthly_history]['.$month.']['.$monthlyField.']';$fieldId='history-'.$month.'-'.$monthlyField;
$definition=$historyFields[$monthlyField];$fieldValue=$monthlyValues[$monthlyField] ?? '';
?>
<label class="label" for="<?= e($fieldId) ?>"><?= e(['occupancy'=>'Ocupación','rent_billed'=>'Canon (COP)','admin_billed'=>'PH (COP)','vat_billed'=>'IVA (COP)','rent_paid'=>'Recibido (COP)'][$monthlyField] ?? $definition['label']) ?></label>
<?php if ($definition['type']==='select'): ?>
<select class="input" id="<?= e($fieldId) ?>" name="<?= e($fieldName) ?>" x-model="<?= e($monthlyModel.'.'.$monthlyField) ?>" :aria-invalid="Boolean(issues['<?= e($fieldName) ?>'])" aria-describedby="<?= e($fieldId) ?>-help <?= e($fieldId) ?>-error">
    <option value="">Por documentar</option><?php foreach ($definition['options'] as $value=>$label): ?><option value="<?= e($value) ?>" <?= $value===$fieldValue?'selected':'' ?>><?= e($label) ?></option><?php endforeach; ?>
</select>
<?php else: ?>
<input class="input" id="<?= e($fieldId) ?>" name="<?= e($fieldName) ?>" value="<?= e($fieldValue) ?>" x-model="<?= e($monthlyModel.'.'.$monthlyField) ?>" placeholder="COP; vacío si se desconoce" inputmode="decimal" :aria-invalid="Boolean(issues['<?= e($fieldName) ?>'])" aria-describedby="<?= e($fieldId) ?>-help <?= e($fieldId) ?>-error">
<?php endif; ?>
<span class="sr-only" id="<?= e($fieldId) ?>-help"><?= e($definition['help']) ?></span>
<span class="text-xs text-red-800" role="alert" id="<?= e($fieldId) ?>-error" x-text="issues['<?= e($fieldName) ?>'] || ''"><?= e($leaseErrors[$fieldName] ?? '') ?></span>
