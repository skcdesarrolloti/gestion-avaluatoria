<?php
$leaseSummary=\App\Services\LeaseRegister::read($record);
$leaseRoutes=\App\Services\MethodologyWorkflow::components($record,$units ?? []);
$leaseWorkflow=\App\Services\MethodologyWorkflow::saved($record);
$leaseKey='';foreach ($leaseRoutes as $key=>$item) if (($leaseWorkflow[$key]['method'] ?? '')==='renta') {$leaseKey=$key;break;}
$leaseSummaryUrl='avaluos/'.$record['id'].'/metodologia-valuatoria'.($leaseKey!==''?'?method=renta&stage=3&component='.rawurlencode($leaseKey):'?method=renta&stage=plan');
?>
<div class="md:col-span-2 rounded-2xl border bg-emerald-50 p-4">
    <h3 class="font-semibold">Arriendos del inmueble · consulta</h3>
    <p class="mt-2 text-sm">La descripción se completa en el capítulo 3. Los contratos y sus cánones se registran una sola vez en Renta y sirven para ambos métodos.</p>
    <?php foreach ($leaseSummary as $entry): $economic=\App\Services\LeaseRegister::economic($entry,$record); ?>
    <div class="mt-3 rounded-lg border bg-white p-3">
        <p class="font-semibold"><?= e(\App\Services\LeaseRegister::title($entry)) ?></p>
        <p class="text-sm">Canon informado (COP): <?= e((string)($economic['rent_amount'] ?? '')) ?: 'Por completar' ?> · <?= e((string)($economic['rent_period'] ?? '')) ?></p>
        <p class="text-xs">Las unidades relacionadas comparten este registro; no se suma un ingreso por cada anexo.</p>
    </div>
    <?php endforeach; ?>
    <details class="mt-3"><summary class="min-h-11 cursor-pointer py-3 font-semibold">Antecedente conservado del encargo</summary>
        <p class="text-sm">Producción de renta informada: <?= e(['si'=>'Sí','no'=>'No','pendiente'=>'Por verificar'][$field('income_producing')] ?? 'Sin dato') ?>.</p>
        <p class="text-sm">Canon (COP): <?= e($field('rent_amount')) ?: 'Sin dato' ?> · <?= e($field('rent_period')) ?></p>
        <p class="text-sm">Administración (COP): <?= e($field('ph_admin_fee_amount')) ?: 'Sin dato' ?>. IVA: <?= e($field('rent_charges_vat')) ?: 'Por verificar' ?>.</p>
        <p class="whitespace-pre-line text-sm"><?= e($field('income_notes')) ?></p>
    </details>
    <a class="btn-secondary mt-3" href="<?= e(url($leaseSummaryUrl)) ?>#renta-concepto"><?= $leaseKey!==''?'Consultar o completar ficha de arriendo':'Configurar recorrido de Renta' ?></a>
</div>
