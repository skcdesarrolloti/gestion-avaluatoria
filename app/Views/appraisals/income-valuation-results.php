<?php
$ivCalculation=$ivSaved['calculation'] ?? null;
$ivMoney=static fn($v)=>'$'.number_format((float)$v,0,',','.');
$ivPct=static fn($v)=>number_format((float)$v*100,3,',','.').'%';
?>
<section class="mt-5 rounded-2xl border bg-white p-5 sm:p-8">
    <h2 class="text-2xl font-semibold">Resultados y sustentación</h2>
    <p class="mt-3">El resumen muestra lo esencial. Abre el detalle para revisar tasas, flujos y fuentes antes de concluir.</p>
    <?php if (!$ivCalculation): ?>
    <p class="mt-4 rounded-lg bg-slate-50 p-4">Aún no hay cálculo. Completa Ingresos y gastos, registra tus alternativas de tasa y pulsa «Comparar tasas y calcular los dos métodos».</p>
    <?php else:
        $ivResult=$ivCalculation['result'];$ivCurrent=$ivCalculation['fingerprint']===\App\Services\IncomeValuationContext::fingerprint($ivSaved,$ivContext);
    ?>
    <p class="mt-3 text-sm">Cálculo guardado: <?= e($ivCalculation['at']) ?> · <?= $ivCurrent?'Corresponde a los datos guardados actuales.':'Resultado anterior: cambiaron los supuestos o el contexto. Recalcula antes de usarlo.' ?></p>
    <p class="mt-2 text-sm">Fecha de aplicación: <?= e($ivResult['date'] ?? 'Consultar entradas guardadas') ?>.</p>
    <?php if (!empty($ivResult['warnings'])): ?><ul class="mt-3 list-disc space-y-2 rounded-lg bg-amber-50 p-5 text-sm"><?php foreach ($ivResult['warnings'] as $warning): ?><li><?= e($warning) ?></li><?php endforeach; ?></ul><?php endif; ?>
    <div class="mt-4 grid gap-4 md:grid-cols-2">
    <?php foreach (['direct'=>'Capitalización directa','dcf'=>'Flujo de caja descontado'] as $ivMethod=>$ivTitle): $ivR=$ivResult[$ivMethod]; ?>
        <article class="rounded-xl border p-4"><h3 class="text-xl font-semibold"><?= e($ivTitle) ?></h3>
            <?php if ($ivR['value']!==null): ?><p class="mt-3 text-2xl font-semibold text-teal-800"><?= e($ivMoney($ivR['value'])) ?></p>
                <?php if ($ivMethod==='direct'): ?><p class="mt-2 text-sm">Ingreso anual <?= $ivR['basis']==='gross'?'bruto':'neto' ?> <?= e($ivMoney($ivR['annual_income'])) ?> ÷ <?= e($ivPct($ivR['rate'])) ?></p>
                <?php else: ?><p class="mt-2 text-sm">Flujos presentes <?= e($ivMoney($ivR['pv_flows'])) ?> + salida presente <?= e($ivMoney($ivR['pv_terminal'])) ?>. Descuento <?= e($ivPct($ivR['discount'])) ?>.</p>
                    <?php if ($ivR['value']>0): ?><p class="mt-2 text-sm">La salida representa <?= e($ivPct($ivR['pv_terminal']/$ivR['value'])) ?> del resultado: revisa su sensibilidad y soporte.</p><?php endif; ?>
                <?php endif; ?>
            <?php else: ?><p class="mt-2 font-semibold">Pendiente de sustento</p><ul class="mt-2 list-disc space-y-2 pl-5 text-sm"><?php foreach ($ivR['errors'] as $issue): ?><li><?= e($issue) ?></li><?php endforeach; ?></ul><?php endif; ?>
        </article>
    <?php endforeach; ?>
    </div>
    <details class="mt-4 rounded-xl border p-4"><summary class="min-h-11 cursor-pointer font-semibold">Comparación de tasas y justificaciones</summary>
        <div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead><tr><th class="p-2">Alternativa</th><th class="p-2">Función</th><th class="p-2">Tasa anual</th><th class="p-2">Verificación</th></tr></thead><tbody>
        <?php foreach ($ivResult['rates'] as $rateId=>$rate): ?><tr class="border-t"><td class="p-2"><?= e($rate['name'] ?: 'Sin nombre') ?></td><td class="p-2"><?= e(['direct'=>'Directa','discount'=>'Descuento','exit'=>'Salida'][$rate['purpose']] ?? 'Por definir') ?></td><td class="p-2"><?= $rate['value']===null?'Pendiente':e($ivPct($rate['value'])) ?></td><td class="p-2"><?= e(implode(' ',$rate['errors'])) ?><?= in_array($rateId,array_intersect_key($ivCalculation['inputs']['adoption'],array_flip(['direct_id','discount_id','exit_id'])),true)?'Elegida por el analista':'' ?></td></tr><?php endforeach; ?>
        </tbody></table></div>
        <?php foreach (['direct_reason','discount_reason','exit_reason','conclusion'] as $key): ?><p class="mt-3 text-sm"><strong><?= e(\App\Services\IncomeValuationFields::group('adoption')[$key]['label']) ?>:</strong> <?= e($ivCalculation['inputs']['adoption'][$key] ?? 'Pendiente') ?></p><?php endforeach; ?>
    </details>
    <?php if ($ivResult['dcf']['value']!==null): require __DIR__.'/income-valuation-flow.php'; endif; ?>
    <details class="mt-4 rounded-xl border p-4"><summary class="min-h-11 cursor-pointer font-semibold">Convenciones y soportes del cálculo guardado</summary>
        <p class="mt-3 text-sm"><?= e($ivResult['convention']) ?></p>
        <?php foreach (['income_support','projection_support','loss_support','cost_support'] as $key): ?><p class="mt-3 text-sm"><strong><?= e(\App\Services\IncomeValuationFields::group('projection')[$key]['label']) ?>:</strong> <?= e($ivCalculation['inputs']['projection'][$key] ?? '') ?></p><?php endforeach; ?>
        <?php foreach (['costs'=>'Costo','rates'=>'Tasa','events'=>'Evento'] as $group=>$label): foreach ($ivCalculation['inputs'][$group] as $row): ?><p class="mt-3 text-sm"><strong><?= e($label.' · '.($row['name'] ?? $row['month'] ?? '')) ?>:</strong> <?= e($row['source'] ?? '') ?></p><?php endforeach;endforeach; ?>
        <a class="btn-secondary mt-4" href="<?= e(url($basePath.'/renta/memoria?component='.rawurlencode($componentKey))) ?>" download>Descargar memoria de datos y fórmulas (JSON)</a>
    </details>
    <?php endif; ?>
    <p class="mt-4 text-sm">La etapa 5 conserva esta revisión de ambos métodos. Los resultados no sustituyen la conclusión del avaluador ni se suman al inmueble por separado.</p>
</section>
