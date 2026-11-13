<?php
$monthNames=['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
$historyFields=\App\Services\LeaseMonthlyHistory::fields();
$historyUrl=$leaseUrl.$leaseId.'&renta_paso=concept&lease_year=';
?>
<details class="rounded-xl border p-4" id="historial-arriendo" <?= isset($_GET['lease_year'])?'open':'' ?>>
    <summary class="min-h-11 cursor-pointer font-semibold">Historial mensual · arriendo y administración · <?= $leaseYear ?></summary>
    <p class="mt-2 text-sm">Transcribe los importes de cada mes y valida sus soportes. La revisión corresponde solo a los importes registrados: los campos vacíos siguen pendientes y no significan cero. Esta captura no proyecta el canon ni aplica IPC.</p>
    <p class="mt-2 text-sm">La administración se valida manualmente con cuentas y comprobantes de PH. Separa cuota ordinaria, pago y expensas extraordinarias. Los valores generales de PH permanecen en el capítulo 3.</p>
    <nav class="my-4 flex flex-wrap items-center gap-3" aria-label="Año del historial de pagos">
        <?php if ($leaseYear>1900): ?><a class="btn-secondary" href="<?= e($historyUrl.($leaseYear-1)) ?>#historial-arriendo">← <?= $leaseYear-1 ?></a><?php endif; ?>
        <span class="font-semibold">Año <?= $leaseYear ?></span>
        <?php if ($leaseYear<2199): ?><a class="btn-secondary" href="<?= e($historyUrl.($leaseYear+1)) ?>#historial-arriendo"><?= $leaseYear+1 ?> →</a><?php endif; ?>
        <a class="btn-secondary" href="<?= e($historyUrl.date('Y')) ?>#historial-arriendo">Año actual</a>
    </nav>
    <input type="hidden" name="lease_year" value="<?= $leaseYear ?>">
    <div class="space-y-3">
        <?php foreach ($monthNames as $number=>$monthName):
            $month=sprintf('%04d-%02d',$leaseYear,$number+1);
            $monthlyValues=$leaseValues['monthly_history'][$month];
            $monthlyModel="values.monthly_history['".$month."']";
            $hasError=(bool)array_filter(array_keys($leaseErrors),static fn($key)=>str_starts_with($key,'lease[monthly_history]['.$month.']'));
        ?>
        <details class="rounded-lg border p-3" <?= $hasError?'open':'' ?>
            @input="if ($event.target.name !== 'lease[monthly_history][<?= $month ?>][review]') <?= e($monthlyModel) ?>.review='pending'">
            <summary class="min-h-11 cursor-pointer">
                <strong><?= e($monthName) ?></strong> ·
                <span x-text="<?= e($monthlyModel) ?>.review==='verified'?'Revisado con soportes':(<?= e($monthlyModel) ?>.rent_paid || <?= e($monthlyModel) ?>.admin_paid || <?= e($monthlyModel) ?>.admin_billed || <?= e($monthlyModel) ?>.admin_extra_paid ? 'Importes por verificar' : 'Sin importes documentados')"><?= ($monthlyValues['review'] ?? '')==='verified'?'Revisado con soportes':'Por completar o verificar' ?></span>
                <span class="block text-sm" x-show="<?= e($monthlyModel) ?>.rent_paid || <?= e($monthlyModel) ?>.admin_paid">
                    Arriendo recibido: <span x-text="<?= e($monthlyModel) ?>.rent_paid || 'Pendiente'"></span> COP · Administración pagada: <span x-text="<?= e($monthlyModel) ?>.admin_paid || 'Pendiente'"></span> COP
                </span>
            </summary>
            <div class="mt-3 grid gap-4 md:grid-cols-2">
                <?php $monthlyField='rent_paid'; $definition=$historyFields[$monthlyField]; require __DIR__.'/lease-history-field.php'; ?>
                <details class="rounded-lg border p-3 md:col-span-2" <?= ($record['regimen_ph'] ?? '')==='si' || array_filter(array_intersect_key($monthlyValues,array_flip(['admin_billed','admin_paid','admin_extra_paid','admin_payer'])))?'open':'' ?>>
                    <summary class="min-h-11 cursor-pointer font-semibold">Administración de PH · si aplica</summary>
                    <div class="grid gap-4 md:grid-cols-2"><?php foreach (['admin_billed','admin_paid','admin_extra_paid','admin_payer'] as $monthlyField): $definition=$historyFields[$monthlyField]; require __DIR__.'/lease-history-field.php'; endforeach; ?></div>
                </details>
                <?php foreach (['review','support','notes'] as $monthlyField): $definition=$historyFields[$monthlyField]; require __DIR__.'/lease-history-field.php'; endforeach; ?>
            </div>
        </details>
        <?php endforeach; ?>
    </div>
    <p class="mt-3 text-sm">Los pagos cubren las unidades relacionadas con esta ficha. No se copian a cada anexo. El reparto entre meses, responsables o unidades debe estar documentado.</p>
</details>
