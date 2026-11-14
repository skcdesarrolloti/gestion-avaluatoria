<?php
use App\Services\{IncomeValuationContext,IncomeValuationFields};
$ivContext=IncomeValuationContext::build($record,$components[$componentKey] ?? []);
$ivSaved=$selected['income_valuation'] ?? [];
$ivOld=json_decode(\App\Core\Session::pullFlash('income_valuation_'.$record['id'].'_'.$componentKey) ?: '{}',true) ?: [];
$ivState=[];
foreach (['projection','costs','events','rates','adoption'] as $group) {
    $defaults=in_array($group,['projection','adoption'],true)?array_fill_keys(array_keys(IncomeValuationFields::group($group)),''):[];
    $ivState[$group]=array_replace($defaults,$ivSaved[$group] ?? []);
}
if (isset($ivOld['old']['data']) && is_array($ivOld['old']['data'])) $ivState=array_replace($ivState,$ivOld['old']['data']);
if (is_string($ivOld['old']['payload'] ?? null)) {
    $prior=json_decode($ivOld['old']['payload'],true);if (is_array($prior)) $ivState=array_replace($ivState,$prior);
}
$ivEndpoint=url($basePath.'/renta/valoracion');$ivIssues=$ivOld['errors'] ?? [];
?>
<div x-data="valuation" @ga:income-context.window="contextSaved($event.detail)" data-income-valuation data-valuation-state="<?= e(json_encode(['state'=>$ivState,'issues'=>(object)$ivIssues,'context'=>$ivContext,'costCatalog'=>\App\Services\IncomeCostCatalog::all()],JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE)) ?>">
<form id="income-valuation" x-ref="valuationForm" method="post" action="<?= e($ivEndpoint) ?>" data-workflow-version
    data-module-autosave data-save-in-place data-autosave-endpoint="<?= e($ivEndpoint) ?>" @ga:validation="validation($event.detail)" @input="stale=true;previewStale=true" @change="stale=true;previewStale=true">
    <?= csrf_field() ?><input type="hidden" name="component" value="<?= e($componentKey) ?>"><input type="hidden" name="study_scope" value="renta:arriendo">
    <input type="hidden" name="version" value="<?= (int)($ivOld['old']['version'] ?? $record['methodology_version'] ?? 0) ?>">
    <input type="hidden" name="context_hash" value="<?= e($ivOld['old']['context_hash'] ?? $ivContext['hash']) ?>">
    <input type="hidden" name="payload" value="" :value="JSON.stringify(state)">
    <input type="hidden" name="return_step" value="<?= e(in_array($incomeInitial,['evidence','valuation','review'],true)?$incomeInitial:'valuation') ?>" :value="incomeTab">
    <fieldset :disabled="busy" class="min-w-0">
    <?php require __DIR__.'/income-valuation-inputs.php'; ?>
    <section class="rounded-2xl border bg-white p-5 sm:p-8" x-show="incomeTab==='valuation'" <?= $incomeInitial==='valuation'?'':'x-cloak' ?>>
        <h2 class="text-2xl font-semibold">4. Tasas y valoración</h2>
        <p class="mt-3">Agrega alternativas sustentadas, compáralas y escoge una para cada función. Los cálculos no adoptan un valor de avalúo automáticamente.</p>
        <?php $ivAcademy='rates';require __DIR__.'/income-valuation-academy.php';$ivGroup='rates';require __DIR__.'/income-valuation-rows.php'; ?>
        <details class="mt-5 rounded-xl border p-4" open><summary class="min-h-11 cursor-pointer font-semibold">Elegir las tasas y justificar su uso</summary>
            <div class="grid gap-4 md:grid-cols-2"><?php $ivGroup='adoption';$ivDynamic=false;foreach (['direct_id','direct_reason','discount_id','discount_reason'] as $ivKey) require __DIR__.'/income-valuation-field.php'; ?></div>
        </details>
        <details class="mt-5 rounded-xl border p-4"><summary class="min-h-11 cursor-pointer font-semibold">Salida del flujo y conclusión</summary>
            <div class="grid gap-4 md:grid-cols-2"><?php foreach (['terminal_mode','exit_id','exit_reason','terminal_growth','terminal_capex','selling_cost','conclusion'] as $ivKey) require __DIR__.'/income-valuation-field.php'; ?></div>
        </details>
        <p class="mt-4 text-sm">Si solo hay soporte para un método, se mostrará su resultado y los pendientes del otro. Para FCD revisa calendario, costos y salida.</p>
        <button class="btn-primary mt-4" type="button" @click="calculate()" :disabled="busy" x-text="busy?'Calculando y guardando…':'Comparar tasas y calcular los dos métodos'">Comparar tasas y calcular los dos métodos</button>
        <noscript><button class="btn-primary mt-4" type="submit" name="operation" value="calculate">Calcular y guardar</button></noscript>
    </section>
    <div class="mt-4 flex flex-wrap items-center gap-3" x-show="(incomeTab==='evidence' && incomePane!=='history') || incomeTab==='valuation'" <?= $incomeInitial==='valuation'?'':'x-cloak' ?>>
        <button type="submit" class="btn-secondary">Guardar borrador ahora</button><p class="text-sm" role="status" data-autosave-status>Los cambios se guardan como borrador; calcular es una acción separada.</p>
    </div>
    </fieldset>
    <input type="hidden" name="form_complete" value="1">
    <?php if (!empty($ivOld['message'])): ?><p role="alert" class="mt-3 text-red-800"><?= e($ivOld['message']) ?></p><?php endif; ?>
    <p class="mt-3 text-red-800" role="alert" x-text="issues.data || ''"></p>
</form>
<p class="mt-3" role="status" aria-live="polite" x-text="message"></p>
<?php require __DIR__.'/income-flow-preview.php'; ?>
<p class="mt-3 rounded-lg bg-amber-50 p-3" x-show="stale && ['valuation','review'].includes(incomeTab)" x-cloak>Hay cambios desde el resultado mostrado. Guarda y vuelve a calcular para actualizarlo.</p>
<div x-ref="valuationResults" data-income-valuation-results x-show="incomeTab==='valuation' || incomeTab==='review'" <?= in_array($incomeInitial,['valuation','review'],true)?'':'x-cloak' ?>>
    <?php require __DIR__.'/income-valuation-results.php'; ?>
</div>
</div>
