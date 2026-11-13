<?php
$leasePrefix='antecedent'; $antecedentOld=!empty($leaseOld['antecedent'])?$leaseOld:[];
$leaseValues=array_replace(array_intersect_key($record,array_flip(\App\Services\LeaseFields::ECONOMIC)),$antecedentOld['values'] ?? []);
$leaseErrors=$antecedentOld['errors'] ?? [];
$antecedentLinked=array_filter($leaseRegister,static fn($item)=>($item['economic_source'] ?? '')==='assignment');
?>
<details class="mt-5 rounded-xl border p-4" <?= $antecedentOld || ($leaseSaved['economic_source'] ?? '')==='assignment'?'open':'' ?>>
    <summary class="min-h-11 cursor-pointer py-3 font-semibold">Antecedente económico del encargo · conservado en su origen</summary>
    <p class="text-sm"><?= $antecedentLinked?'Relacionado con: '.e(\App\Services\LeaseRegister::title(reset($antecedentLinked))):'Aún no relacionado con un contrato. Verifica a qué unidades corresponde antes de utilizarlo.' ?></p>
    <p class="mt-2 text-sm">Este es el dato registrado anteriormente en el capítulo 1. Aquí se corrige el mismo registro. El capítulo 1 lo consulta; no se suma a los contratos.</p>
    <form class="mt-4 space-y-4" id="lease-antecedent" method="post" action="<?= e(url($basePath.'/renta/antecedente')) ?>"
        data-module-autosave data-save-in-place data-autosave-endpoint="<?= e(url($basePath.'/renta/antecedente')) ?>"
        data-lease-form="<?= e(json_encode(['values'=>$leaseValues,'issues'=>(object)$leaseErrors],JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)) ?>"
        x-data="leaseForm()" @ga:validation="validation($event.detail)">
        <?= csrf_field() ?><input type="hidden" name="component" value="<?= e($componentKey) ?>"><input type="hidden" name="lease_id" value="<?= e($leaseId) ?>">
        <input type="hidden" name="version" value="<?= (int)($antecedentOld['version'] ?? $record['version']) ?>">
        <?php if ($antecedentOld): ?><p role="alert" class="rounded-lg bg-amber-50 p-3"><?= e($antecedentOld['message']) ?></p><?php endif; ?>
        <div class="grid gap-4 md:grid-cols-2"><?php foreach (\App\Services\LeaseFields::ECONOMIC as $leaseField) require __DIR__.'/lease-field.php'; ?></div>
        <button type="submit" class="btn-secondary">Guardar antecedente / reintentar</button><p data-autosave-status role="status" class="text-sm">Sin cambios pendientes.</p>
    </form>
</details>
