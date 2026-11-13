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
$leaseErrors=$leaseOwnOld['errors'] ?? []; $leasePrefix='lease';
$leaseLinked=$leaseOwnOld['units'] ?? $leaseSaved['unit_ids'] ?? [$leaseUnitId];
$leaseActive=array_column(array_filter($units,static fn($u)=>$u['unit_kind']!=='common'),null,'id');
$leaseUrl=$flowUrl('3').'&lease=';
?>
<div class="mt-5 space-y-3">
    <h3 class="text-xl font-semibold">Contratos y unidades relacionadas</h3>
    <p class="text-sm">Un contrato puede comprender varias unidades. Abre la ficha existente para relacionarlas; el canon se registra una sola vez.</p>
    <div class="flex flex-wrap gap-2" aria-label="Fichas de arriendo">
        <?php foreach ($leaseRegister as $key=>$entry): ?>
        <a class="btn-secondary" href="<?= e($leaseUrl.$key) ?>#renta-concepto" <?= $key===$leaseId?'aria-current="page"':'' ?>><?= e(LeaseRegister::title($entry)) ?><?= isset($leaseRelated[$key])?' · esta unidad':'' ?></a>
        <?php endforeach; ?>
        <a class="btn-secondary" href="<?= e($leaseUrl.bin2hex(random_bytes(16))) ?>#renta-concepto">Agregar otro contrato</a>
    </div>
</div>
<form id="lease-form" class="mt-5 space-y-5" method="post" action="<?= e(url($basePath.'/renta/arriendo')) ?>"
    data-module-autosave data-save-in-place data-autosave-endpoint="<?= e(url($basePath.'/renta/arriendo')) ?>"
    data-lease-form="<?= e(json_encode(['values'=>$leaseValues,'issues'=>(object)$leaseErrors,'id'=>$leaseId],JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)) ?>"
    x-data="leaseForm()" @ga:validation="validation($event.detail)">
    <?= csrf_field() ?><input type="hidden" name="component" value="<?= e($componentKey) ?>"><input type="hidden" name="lease_id" value="<?= e($leaseId) ?>">
    <input type="hidden" name="version" value="<?= (int)($leaseOwnOld['version'] ?? $record['lease_version'] ?? 0) ?>">
    <?php if (!empty($leaseOwnOld['message'])): ?><p class="rounded-lg bg-amber-50 p-3" role="alert"><?= e($leaseOwnOld['message']) ?></p><?php endif; ?>
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
    <fieldset class="rounded-xl border p-4"><legend class="px-2 font-semibold">3. Vigencia e incrementos pactados</legend>
        <div class="grid gap-4 md:grid-cols-2"><?php foreach (['start_date','end_date','term','renewals','adjustment_rule','adjustment_frequency','adjustment_next_date'] as $leaseField) require __DIR__.'/lease-field.php'; ?></div>
    </fieldset>
    <fieldset class="rounded-xl border p-4"><legend class="px-2 font-semibold">4. Condiciones y soportes</legend>
        <div class="grid gap-4 md:grid-cols-2"><?php foreach (['inclusions','grace','expenses','support','notes'] as $leaseField) require __DIR__.'/lease-field.php'; ?></div>
    </fieldset>
    <div class="flex flex-wrap items-center gap-3"><button type="submit" class="btn-primary">Guardar ficha / reintentar</button><p data-autosave-status role="status" class="text-sm">Sin cambios pendientes. Autoguardado como borrador.</p></div>
    <p class="text-xs text-slate-600">El guardado se confirma al responder el servidor. Sin conexión, conserva la página abierta hasta confirmar el guardado.</p>
</form>
<?php require __DIR__.'/lease-antecedent.php'; ?>
