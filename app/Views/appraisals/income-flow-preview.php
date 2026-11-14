<?php
$ivPreviewSnapshot=$ivSaved['flow_preview'] ?? null;
$ivPreview=$ivPreviewSnapshot['result'] ?? null;
$ivPreviewMoney=static fn($n)=>$n===null?'Pendiente':'$'.number_format((float)$n,2,',','.');
$ivPreviewCurrent=$ivPreviewSnapshot && ($ivPreviewSnapshot['fingerprint'] ?? '')===\App\Services\IncomeValuationContext::fingerprint($ivSaved,$ivContext);
?>
<section class="mt-5 rounded-2xl border bg-white p-5 sm:p-8" x-ref="previewResults" data-income-flow-preview x-show="incomeTab==='evidence' && incomePane==='projection'" x-cloak>
    <h3 class="text-xl font-semibold">Flujo mensual proyectado · base de los dos métodos</h3>
    <?php if (!$ivPreview): ?><p class="mt-3 text-sm">Define canon, horizonte e incrementos y pulsa «Mostrar / actualizar flujo mensual». No necesitas inventar administración, otros gastos ni tasas para ver los ingresos.</p>
    <?php else: ?>
    <p class="mt-3 text-sm">Fecha del avalúo: <?= e($ivPreview['date'] ?: 'Pendiente') ?>. <?= $ivPreview['example']?'Escenario de prueba; no acredita un contrato ni renta de mercado verificados.':'' ?></p>
    <?php if (!$ivPreviewCurrent): ?><p class="mt-3 rounded-lg bg-amber-50 p-3 text-sm">Tabla anterior: cambiaron las entradas o el origen. Actualiza el flujo antes de usarla.</p><?php endif; ?>
    <p class="mt-3 rounded-lg bg-amber-50 p-3 text-sm" x-show="previewStale">Hay cambios desde esta tabla. Pulsa «Mostrar / actualizar flujo mensual».</p>
    <?php if ($ivPreview['errors']): ?><ul class="mt-3 list-disc pl-5"><?php foreach ($ivPreview['errors'] as $issue): ?><li><?= e($issue) ?></li><?php endforeach; ?></ul>
    <?php else: ?>
    <div class="mt-4 grid gap-4 md:grid-cols-2">
        <article class="rounded-xl border p-4"><h4 class="font-semibold">Capitalización directa</h4>
            <p class="mt-2 text-sm">Canon de partida × 12: <strong><?= e($ivPreviewMoney($ivPreview['annual_gross'])) ?></strong> brutos/año.</p>
            <p class="mt-2 text-sm">Ingreso neto representativo: <?= e($ivPreviewMoney($ivPreview['annual_net'])) ?>. La tasa debe corresponder a esa misma base de ingreso. Este monto anual no es el valor del inmueble.</p>
        </article>
        <article class="rounded-xl border p-4"><h4 class="font-semibold">Flujo de caja descontado</h4>
            <p class="mt-2 text-sm"><?= count($ivPreview['months']) ?> cierres mensuales. Ingresos brutos del horizonte: <strong><?= e($ivPreviewMoney($ivPreview['total_gross'])) ?></strong>.</p>
            <p class="mt-2 text-sm">Esta suma aún no está descontada. El FCD requiere costos, tasa de descuento y tratamiento de salida sustentados.</p>
        </article>
    </div>
    <ul class="mt-3 list-disc pl-5 text-sm"><?php foreach ($ivPreview['pending'] as $issue): ?><li><?= e($issue) ?></li><?php endforeach; ?></ul>
    <p class="mt-3 text-sm">Desde el día siguiente al avalúo; primer mes parcial por días calendario si corresponde. Canon sin IVA ni administración cobrada a terceros. Solo se descuentan costos del propietario cuando están revisados.</p>
    <div class="relative mt-4 overflow-x-auto" style="max-height:560px;overflow-y:auto" tabindex="0" role="region" aria-label="Tabla de proyección mensual preliminar en COP">
    <table class="w-full border-collapse text-sm"><caption class="sr-only">Proyección mensual preliminar compartida por directa y FCD</caption>
        <thead class="bg-slate-50" style="position:sticky;top:0;z-index:1"><tr><?php foreach (['Mes','Canon potencial','Después de vacancia y no pago','Costos (incluida PH)','Reposiciones','Flujo neto sin descontar'] as $label): ?><th scope="col" class="border p-3 text-left"><?= e($label) ?></th><?php endforeach; ?></tr></thead>
        <tbody><?php foreach ($ivPreview['months'] as $row): ?><tr><th scope="row" class="border p-3 text-left" style="min-width:120px"><?= e($row['month']) ?><?php if ($row['fraction']<1): ?><small class="block">Fracción del mes</small><?php endif; ?></th>
            <?php foreach (['potential','effective','costs','capex','cash'] as $field): ?><td class="border p-3" style="min-width:150px"><?= e($ivPreviewMoney($row[$field])) ?></td><?php endforeach; ?>
        </tr><?php endforeach; ?></tbody>
    </table></div>
    <details class="mt-4"><summary class="min-h-11 cursor-pointer py-3 font-semibold">Academia · interpretación y supuestos de esta tabla</summary>
        <?php $normKey='history';require __DIR__.'/income-norm-tip.php'; ?>
        <p class="mt-3 text-sm">Directa toma un ingreso anual representativo; FCD utiliza la secuencia mensual y el valor de salida. Comparten datos, pero no son fórmulas iguales. Los incrementos futuros son supuestos, no IPC observado. Reposiciones cero indican que no se programaron eventos de inversión, no que nunca sean necesarias.</p>
        <?php foreach (['income_support','projection_support','loss_support','cost_support'] as $field): ?><p class="mt-3 text-sm"><?= e($ivPreviewSnapshot['inputs']['projection'][$field] ?? '') ?></p><?php endforeach; ?>
    </details>
    <?php endif;endif; ?>
</section>
