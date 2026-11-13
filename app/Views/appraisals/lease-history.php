<?php
$monthNames=['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
$historyFields=\App\Services\LeaseMonthlyHistory::fields();$historyUrl=$leaseUrl.$leaseId.'&lease_year=';
?>
<section class="rounded-xl border p-4" id="historial-arriendo">
    <h3 class="text-xl font-semibold">Historial mensual · <?= $leaseYear ?></h3>
    <?php $normKey='history';require __DIR__.'/income-norm-tip.php'; ?>
    <p class="mt-3 text-sm">Desde la primera fecha documentada del cliente. Canon reconstruido ≠ factura ≠ pago. Vacíos significan datos pendientes; un impago no demuestra vacancia. Corte del avalúo: <?= e(($record['value_date'] ?? '') ?: 'pendiente') ?>. Los meses posteriores se identifican aparte.</p>
    <nav class="my-4 flex flex-wrap items-center gap-3" aria-label="Año del historial de pagos">
        <?php if ($leaseYear>1900): ?><a class="btn-secondary" href="<?= e($historyUrl.($leaseYear-1)) ?>#historial-arriendo">← <?= $leaseYear-1 ?></a><?php endif; ?>
        <strong>Año <?= $leaseYear ?></strong>
        <?php if ($leaseYear<2199): ?><a class="btn-secondary" href="<?= e($historyUrl.($leaseYear+1)) ?>#historial-arriendo"><?= $leaseYear+1 ?> →</a><?php endif; ?>
        <?php if (!empty($leaseValues['start_date'])): ?><a class="btn-secondary" href="<?= e($historyUrl.substr($leaseValues['start_date'],0,4)) ?>#historial-arriendo">Inicio del contrato</a><?php endif; ?>
        <a class="btn-secondary" href="<?= e($historyUrl.substr(($record['value_date'] ?? '') ?: date('Y-m-d'),0,4)) ?>#historial-arriendo">Año del avalúo</a>
    </nav>
    <input type="hidden" name="lease_year" value="<?= $leaseYear ?>"><input type="hidden" name="return_step" :value="incomeTab" value="<?= e($incomeInitial) ?>">
    <p class="text-sm" x-text="ledgerSummary"></p>
    <p class="mt-2 text-xs">COP. Total del período = canon facturado + cuota PH + IVA. Total recibido = canon + administración recibida por el propietario + IVA recibido. No equivalen al ingreso neto del propietario.</p>
    <div class="relative mt-4 overflow-x-auto" tabindex="0" aria-label="Historial mensual, tabla desplazable">
    <table class="w-full border-collapse text-sm"><caption class="sr-only">Historial mensual editable del contrato <?= e($leaseValues['reference']) ?></caption>
        <thead class="bg-slate-50"><tr><?php foreach (['Mes','Ocupación','Canon reconstruido','Canon facturado','PH del mes','IVA facturado','Total del período','Canon recibido','Total recibido','Soportes y costos'] as $heading): ?><th scope="col" class="border p-3 text-left"><?= e($heading) ?></th><?php endforeach; ?></tr></thead>
        <tbody><?php foreach ($monthNames as $index=>$monthName):
            $month=sprintf('%04d-%02d',$leaseYear,$index+1);$monthlyValues=$leaseValues['monthly_history'][$month];$monthlyModel="values.monthly_history['".$month."']";
        ?>
        <tr @input="<?= e($monthlyModel) ?>.review='pending'">
            <th scope="row" class="border p-3 text-left"><?= e($monthName) ?><span class="block text-xs text-amber-800" x-show="ledgerRows[<?= $index ?>].future">Posterior al corte</span></th>
            <td class="border p-2" style="min-width:190px"><?php $monthlyField='occupancy';require __DIR__.'/lease-ledger-cell.php'; ?></td>
            <td class="border p-3" style="min-width:170px"><strong x-text="money(ledgerRows[<?= $index ?>].contract)">Pendiente</strong><span class="block text-xs" x-text="ledgerRows[<?= $index ?>].changes"></span></td>
            <?php foreach (['rent_billed','admin_billed','vat_billed'] as $monthlyField): ?><td class="border p-2" style="min-width:175px"><?php require __DIR__.'/lease-ledger-cell.php'; ?><?php if ($monthlyField==='vat_billed'): ?><span class="text-xs" x-show="ledgerRows[<?= $index ?>].vatEstimated">Estimado con tarifa documentada: <span x-text="money(ledgerRows[<?= $index ?>].vat)"></span></span><?php endif; ?></td><?php endforeach; ?>
            <td class="border p-3" x-text="money(ledgerRows[<?= $index ?>].totalBilled)">Pendiente</td>
            <td class="border p-2" style="min-width:175px"><?php $monthlyField='rent_paid';require __DIR__.'/lease-ledger-cell.php'; ?></td>
            <td class="border p-3" x-text="money(ledgerRows[<?= $index ?>].totalReceived)">Pendiente</td>
            <td class="border p-3"><button type="button" class="btn-secondary" @click="$el.closest('tbody').querySelector('#detalle-<?= $month ?>').open=!$el.closest('tbody').querySelector('#detalle-<?= $month ?>').open">Detalle de <?= e($monthName) ?></button></td>
        </tr>
        <tr><td colspan="10" class="border px-3">
            <details id="detalle-<?= $month ?>" <?= array_filter(array_keys($leaseErrors),static fn($k)=>str_contains($k,$month))?'open':'' ?> @input="if ($event.target.name!=='lease[monthly_history][<?= $month ?>][review]') <?= e($monthlyModel) ?>.review='pending'">
                <summary class="min-h-11 cursor-pointer py-3 text-xs">Validación de <?= e($monthName) ?> · <span x-text="<?= e($monthlyModel) ?>.review==='verified'?'Revisado con soportes':'Por verificar'"></span> · pagos, gastos y documentos</summary>
                <div class="grid gap-4 p-3 md:grid-cols-2"><?php foreach (array_diff(array_keys($historyFields),['occupancy','rent_billed','admin_billed','vat_billed','rent_paid']) as $monthlyField): $definition=$historyFields[$monthlyField];require __DIR__.'/lease-history-field.php';endforeach; ?></div>
                <p class="mb-3 text-sm">Gastos del propietario: <span x-text="money(ledgerRows[<?= $index ?>].ownerCost)"></span> · Canon recibido menos esos gastos: <strong x-text="money(ledgerRows[<?= $index ?>].net)"></strong>. Requiere los siete rubros revisados, con cero explícito cuando proceda. No es una rentabilidad porcentual.</p>
            </details>
        </td></tr><?php endforeach; ?></tbody>
    </table></div>
    <?php require __DIR__.'/lease-ledger-chart.php'; ?>
    <p class="mt-3 text-sm" x-show="ledgerDirty">Vista previa del borrador; consulta la confirmación de guardado antes de salir.</p>
</section>
