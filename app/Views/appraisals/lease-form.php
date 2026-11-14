<?php
use App\Services\{LeaseFields,LeaseRegister};
$leaseRegister=LeaseRegister::read($record); $leaseRelated=LeaseRegister::forUnit($record,$leaseUnitId);
$leaseRequested=is_string($_GET['lease'] ?? null)?$_GET['lease']:'';
$leaseId=preg_match('/^[a-f0-9]{32}$/D',$leaseRequested)?$leaseRequested:
    ($leaseRequested==='new'?bin2hex(random_bytes(16)):(string)(array_key_first($leaseRelated) ?? bin2hex(random_bytes(16))));
$leaseSaved=$leaseRegister[$leaseId] ?? [];
$leaseOld=json_decode(\App\Core\Session::pullFlash('lease_'.$record['id']) ?: '{}',true) ?: [];
$leaseOwnOld=empty($leaseOld['antecedent']) && ($leaseOld['lease_id'] ?? '')===$leaseId?$leaseOld:[];
$leaseValues=array_replace(array_fill_keys(array_keys(LeaseFields::all()),''),['economic_source'=>'own'],$leaseSaved,$leaseOwnOld['values'] ?? []);
$leaseValues=array_intersect_key($leaseValues,LeaseFields::all());
foreach (['history_rule'=>'ipc','history_calendar'=>'january','history_ipc_points'=>'0','initial_rent_period'=>'mensual','history_admin_rule'=>'ipc'] as $key=>$default)
    if ($leaseValues[$key]==='') $leaseValues[$key]=$default;
$leaseErrors=$leaseOwnOld['errors'] ?? []; $leasePrefix='lease';
$leaseLinked=$leaseOwnOld['units'] ?? $leaseSaved['unit_ids'] ?? [$leaseUnitId];
$leaseActive=array_column(array_filter($units,static fn($u)=>$u['unit_kind']!=='common'),null,'id');
$leaseUrl=$flowUrl('3').'&renta_paso='.($incomeInitial==='evidence'?'evidence':'concept').'&lease=';
$leaseAreaAvailable=[];
foreach ($units as $areaSourceUnit) foreach (['area_private_m2','area_adopted_m2','built_area_adopted_m2'] as $areaSourceKey)
    if (is_numeric($areaSourceUnit[$areaSourceKey] ?? '') && (float)$areaSourceUnit[$areaSourceKey]>0) $leaseAreaAvailable[]=$areaSourceUnit['id'].':'.$areaSourceKey;
$leaseAntecedentAvailable=($record['rent_amount'] ?? '')!=='' && ($record['rent_amount'] ?? null)!==null && !empty($record['rent_period']);
$leaseYear=\App\Services\LeaseMonthlyHistory::year($_GET['lease_year'] ?? null,$leaseValues);
$leaseValues['monthly_history']=is_array($leaseValues['monthly_history'])?$leaseValues['monthly_history']:[];
foreach (['rent_adjustments','admin_adjustments','vacancy_periods'] as $key) $leaseValues[$key]=is_array($leaseValues[$key])?$leaseValues[$key]:[];
for ($month=1;$month<=12;$month++) {
    $key=sprintf('%04d-%02d',$leaseYear,$month);
    $leaseValues['monthly_history'][$key]=array_replace(array_fill_keys(array_keys(\App\Services\LeaseMonthlyHistory::fields()),''),$leaseValues['monthly_history'][$key] ?? []);
}
?>
<div class="mt-5 space-y-3">
    <h3 class="text-xl font-semibold">Ficha del arriendo · contratos y unidades relacionadas</h3>
    <p class="text-sm">Un contrato puede comprender varias unidades. Abre la ficha existente para relacionarlas; el canon se registra una sola vez.</p>
    <div class="flex flex-wrap gap-2" aria-label="Fichas de arriendo">
        <?php foreach ($leaseRegister as $key=>$entry): ?>
        <a class="btn-secondary" href="<?= e($leaseUrl.$key) ?>#historial-arriendo" <?= $key===$leaseId?'aria-current="page"':'' ?>><?= e(LeaseRegister::title($entry)) ?><?= isset($leaseRelated[$key])?' · esta unidad':'' ?></a>
        <?php endforeach; ?>
        <a class="btn-secondary" href="<?= e($leaseUrl.bin2hex(random_bytes(16))) ?>#historial-arriendo">Agregar otro contrato</a>
    </div>
</div>
<details class="mt-4" <?= !in_array($leaseOccupation['occupancy'] ?? '',['vacant','owner','works'],true) || $leaseSaved || $leaseOwnOld?'open':'' ?>>
<summary class="min-h-11 cursor-pointer font-semibold">Historial automático y ficha del arriendo</summary>
<p class="mt-2 text-sm">Si no hay arriendo vigente, conserva aquí los contratos anteriores y los pagos documentados de PH. No necesitas inventar un canon ni llenar fechas de un contrato inexistente.</p>
<form id="lease-form" class="mt-5 space-y-5" method="post" action="<?= e(url($basePath.'/renta/arriendo')) ?>"
    data-module-autosave data-save-in-place data-autosave-endpoint="<?= e(url($basePath.'/renta/arriendo')) ?>"
    data-lease-form="<?= e(json_encode(['values'=>$leaseValues,'issues'=>(object)$leaseErrors,'id'=>$leaseId,'historyYear'=>$leaseYear,'cutoff'=>$record['value_date'] ?? '', 'today'=>date('Y-m-d'),'ipc'=>\App\Services\LeaseReferenceFields::ipc(), 'occupancy'=>$leaseOccupation['occupancy'] ?? '', 'areaAvailable'=>$leaseAreaAvailable,'antecedentAvailable'=>$leaseAntecedentAvailable],JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)) ?>"
    x-data="leaseForm()" @ga:validation="validation($event.detail)" @ga:saved="saved($event.detail)" @input="ledgerDirty=true"
    @ga:income-context.window="if ($event.detail.record==='<?= e($record['id']) ?>') cutoff=$event.detail.value_date">
    <?= csrf_field() ?><input type="hidden" name="component" value="<?= e($componentKey) ?>"><input type="hidden" name="lease_id" value="<?= e($leaseId) ?>">
    <input type="hidden" name="version" value="<?= (int)($leaseOwnOld['version'] ?? $record['lease_version'] ?? 0) ?>">
    <input type="hidden" :name="'lease_payload'" :value="JSON.stringify(values)">
    <?php if (!empty($leaseOwnOld['message'])): ?><p class="rounded-lg bg-amber-50 p-3" role="alert"><?= e($leaseOwnOld['message']) ?></p><?php endif; ?>
    <?php require __DIR__.'/lease-reference-setup.php'; ?>
    <?php require __DIR__.'/lease-history.php'; ?>
    <details class="rounded-xl border p-4"><summary class="min-h-11 cursor-pointer font-semibold">Contrato, canon vigente y soportes · datos compartidos</summary>
    <fieldset class="rounded-xl border p-4"><legend class="px-2 font-semibold">1. Contrato y alcance</legend>
        <div class="grid gap-4 md:grid-cols-2"><?php foreach (['reference','status'] as $leaseField) require __DIR__.'/lease-field.php'; ?></div>
        <p class="mt-4 text-sm" id="lease-units-help">Marca las unidades cubiertas. Para arriendos parciales, identifica el espacio en el campo siguiente. Debe incluir la unidad abierta.</p>
        <div class="mt-2 flex flex-wrap gap-3">
            <?php foreach ($leaseActive as $key=>$entry): ?>
            <label class="flex min-h-11 items-center gap-2"><input type="checkbox" name="lease_units[]" value="<?= e($key) ?>" <?= in_array($key,$leaseLinked,true)?'checked':'' ?>
                aria-describedby="lease-units-help lease-units-error" :aria-invalid="Boolean(issues.lease_units)"><?= e($entry['label']) ?></label>
            <?php endforeach; ?>
            <?php foreach (array_diff($leaseLinked,array_keys($leaseActive)) as $inactive): ?>
            <input type="hidden" name="lease_units[]" value="<?= e($inactive) ?>"><p class="text-sm">Relación histórica con unidad inactiva conservada.</p>
            <?php endforeach; ?>
        </div>
        <p id="lease-units-error" class="text-sm text-red-700" role="alert" x-text="issues.lease_units || ''"><?= e($leaseErrors['lease_units'] ?? '') ?></p>
        <?php $leaseField='scope'; require __DIR__.'/lease-field.php'; ?>
        <?php require __DIR__.'/lease-area.php'; ?>
    </fieldset>
    <fieldset class="rounded-xl border p-4"><legend class="px-2 font-semibold">2. Canon y pagos documentados</legend>
        <?php $leaseField='economic_source'; require __DIR__.'/lease-field.php'; ?>
        <p class="mt-3 rounded-lg bg-teal-50 p-3 text-sm" x-show="values.economic_source==='assignment'">Esta ficha consulta el canon conservado del encargo. Revísalo o corrígelo en «Antecedente económico del encargo», debajo de la ficha. No se copia como otro ingreso.</p>
        <div class="mt-4 grid gap-4 md:grid-cols-2" x-show="values.economic_source!=='assignment'">
            <?php foreach (LeaseFields::ECONOMIC as $leaseField) require __DIR__.'/lease-field.php'; ?>
        </div>
        <div class="mt-4 grid gap-4 md:grid-cols-2"><?php foreach (['effective_date','received_differs'] as $leaseField) require __DIR__.'/lease-field.php'; ?></div>
        <div class="mt-4 grid gap-4 md:grid-cols-2" x-show="values.received_differs==='yes' || values.received_amount || values.received_period">
            <?php foreach (['received_amount','received_period'] as $leaseField) require __DIR__.'/lease-field.php'; ?>
        </div>
    </fieldset>
    <details class="rounded-xl border p-4"><summary class="min-h-11 cursor-pointer font-semibold">3. Inicio, vigencia e incrementos pactados</summary>
        <p class="my-3 text-sm">El inicio y el canon inicial se consultan en la configuración del historial, arriba. La referencia automática no sustituye el canon vigente documentado.</p>
        <div class="grid gap-4 md:grid-cols-2"><?php foreach (['adjustment_index_reference','term','renewals','adjustment_rule','adjustment_frequency','adjustment_next_date'] as $leaseField) require __DIR__.'/lease-field.php'; ?></div>
    </details>
    <details class="rounded-xl border p-4"><summary class="min-h-11 cursor-pointer font-semibold">4. Condiciones especiales y responsables de gastos</summary>
        <div class="grid gap-4 md:grid-cols-2"><?php foreach (['inclusions','grace','expenses'] as $leaseField) require __DIR__.'/lease-field.php'; ?></div>
    </details>
    </details>
    <details class="rounded-xl border p-4"><summary class="min-h-11 cursor-pointer font-semibold">Soportes y pendientes del contrato</summary>
        <p class="text-sm">Esta lista señala datos faltantes en la ficha; no acredita cumplimiento ni sustituye la revisión de documentos.</p>
        <ul class="my-3 list-disc pl-5"><template x-for="item in pendingItems" :key="item"><li x-text="item"></li></template></ul>
        <div class="grid gap-4 md:grid-cols-2"><?php foreach (['support','notes'] as $leaseField) require __DIR__.'/lease-field.php'; ?></div>
    </details>
    <div class="flex flex-wrap items-center gap-3"><button type="submit" class="btn-primary">Guardar ficha / reintentar</button><p data-autosave-status role="status" class="text-sm">Sin cambios pendientes. Autoguardado como borrador.</p></div>
    <p class="text-xs text-slate-600">El guardado se confirma al responder el servidor. Sin conexión, conserva la página abierta hasta confirmar el guardado.</p>
</form>
</details>
<?php require __DIR__.'/lease-antecedent.php'; ?>
