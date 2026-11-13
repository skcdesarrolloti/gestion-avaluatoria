<?php
$incomeOld=json_decode(\App\Core\Session::pullFlash(\App\Services\IncomeConceptAction::flashKey($record['id'],$componentKey)) ?: '{}',true) ?: [];
if ($incomeOld):
$incomeValues=array_replace($selected['income_concept'] ?? [],$incomeOld['values'] ?? []);$incomeErrors=$incomeOld['errors'] ?? [];
$incomeState=['values'=>$incomeValues,'issues'=>(object)$incomeErrors,'section'=>'lease','fieldGroups'=>[],'sourceOccupancy'=>''];
?>
<details open class="mt-5 rounded-xl border p-4"><summary class="min-h-11 cursor-pointer py-3 font-semibold">Recuperar borrador de la pantalla anterior</summary>
    <p class="rounded-lg bg-amber-50 p-3" role="alert"><?= e($incomeOld['message']) ?></p>
    <p class="mt-2 text-sm">Estos cambios proceden de la preparación anterior. Se conservan para corregirlos y guardarlos como antecedentes; la ficha del contrato permanece separada.</p>
    <form method="post" action="<?= e(url($basePath.'/renta/concepto')) ?>" data-module-autosave data-save-in-place data-workflow-version
        data-autosave-endpoint="<?= e(url($basePath.'/renta/concepto')) ?>" data-income-preparation="<?= e(json_encode($incomeState,JSON_THROW_ON_ERROR)) ?>"
        x-data="preparation()" @ga:validation="validation($event.detail)">
        <?= csrf_field() ?><input type="hidden" name="component" value="<?= e($componentKey) ?>"><input type="hidden" name="version" value="<?= (int)$incomeOld['version'] ?>">
        <?php foreach (\App\Services\IncomeConcept::questions() as $name=>$question) require __DIR__.'/methodology-income-question.php'; ?>
        <?php foreach (\App\Services\IncomeConcept::texts() as $textKey=>$textDefinition):
            if (\App\Services\IncomePreparationNotes::parent($textKey)==='') require __DIR__.'/methodology-income-note.php';
        endforeach; ?>
        <button type="submit" class="btn-secondary mt-4">Guardar antecedentes recuperados</button><p data-autosave-status role="status">Borrador pendiente de revisión.</p>
    </form>
</details>
<?php endif; ?>
