<?php
$definition=\App\Services\LeaseFields::all()[$leaseField];
$fieldName=$leasePrefix.'['.$leaseField.']'; $fieldId=$leasePrefix.'-'.$leaseField;
$fieldError=$leaseErrors[$fieldName] ?? ''; $fieldValue=(string)($leaseValues[$leaseField] ?? '');
?>
<div class="<?= $definition['type']==='textarea'?'md:col-span-2':'' ?>">
    <label class="label" for="<?= e($fieldId) ?>"><?= e($definition['label']) ?></label>
    <?php if ($definition['type']==='select'): ?>
    <select class="input" id="<?= e($fieldId) ?>" name="<?= e($fieldName) ?>" x-model="values.<?= e($leaseField) ?>"
        aria-describedby="<?= e($fieldId) ?>-help <?= e($fieldId) ?>-error" aria-invalid="<?= $fieldError!==''?'true':'false' ?>" :aria-invalid="Boolean(issues['<?= e($fieldName) ?>'])">
        <option value="">Por completar o verificar</option>
        <?php if ($fieldValue!=='' && !isset($definition['options'][$fieldValue])): ?><option value="<?= e($fieldValue) ?>" selected><?= e($fieldValue) ?> · revisar</option><?php endif; ?>
        <?php foreach ($definition['options'] as $value=>$label): ?><option value="<?= e($value) ?>" <?= $value===$fieldValue?'selected':'' ?>><?= e($label) ?></option><?php endforeach; ?>
    </select>
    <?php elseif ($definition['type']==='textarea'): ?>
    <textarea class="input" id="<?= e($fieldId) ?>" name="<?= e($fieldName) ?>" rows="3" maxlength="1500" x-model="values.<?= e($leaseField) ?>"
        placeholder="<?= e($definition['help']) ?>" aria-describedby="<?= e($fieldId) ?>-help <?= e($fieldId) ?>-error"
        aria-invalid="<?= $fieldError!==''?'true':'false' ?>" :aria-invalid="Boolean(issues['<?= e($fieldName) ?>'])"><?= e($fieldValue) ?></textarea>
    <?php else: ?>
    <input class="input" id="<?= e($fieldId) ?>" name="<?= e($fieldName) ?>" type="<?= $definition['type']==='date'?'date':'text' ?>"
        <?= $definition['type']==='money'?'inputmode="decimal"':'' ?> maxlength="1500" value="<?= e($fieldValue) ?>" x-model="values.<?= e($leaseField) ?>"
        <?= $definition['type']!=='date'?'placeholder="'.e($definition['help']).'"':'' ?>
        aria-describedby="<?= e($fieldId) ?>-help <?= e($fieldId) ?>-error" aria-invalid="<?= $fieldError!==''?'true':'false' ?>" :aria-invalid="Boolean(issues['<?= e($fieldName) ?>'])">
    <?php endif; ?>
    <p class="mt-1 text-xs text-slate-600" id="<?= e($fieldId) ?>-help"><?= e($definition['help']) ?></p>
    <p class="mt-1 text-sm text-red-700" role="alert" id="<?= e($fieldId) ?>-error" x-text="issues['<?= e($fieldName) ?>'] || ''"><?= e($fieldError) ?></p>
</div>
