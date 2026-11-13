<?php
$ivDcf=$ivResult['dcf'];$ivYears=[];
foreach ($ivDcf['months'] as $row) {
    $year=substr($row['month'],0,4);$ivYears[$year] ??= ['potential'=>0,'costs'=>0,'cash'=>0];
    foreach (['potential','cash'] as $field) $ivYears[$year][$field]+=$row[$field];
    $ivYears[$year]['costs']+=$row['costs']+$row['extra']+$row['capex'];
}
$ivScale=max(1,...array_map(static fn($r)=>max(abs($r['potential']),abs($r['costs']),abs($r['cash'])),$ivYears));
?>
<details class="mt-4 rounded-xl border p-4" open><summary class="min-h-11 cursor-pointer font-semibold">Comportamiento anual del flujo</summary>
    <p class="text-sm">Ingresos potenciales, egresos del propietario (incluidas reposiciones) y flujo neto. El primer y último año pueden ser parciales. La salida se presenta aparte.</p>
    <div class="mt-4 space-y-4">
    <?php foreach ($ivYears as $year=>$totals): ?><div><p class="font-semibold"><?= e($year) ?></p>
        <?php foreach (['potential'=>['Ingreso potencial','#0f766e'],'costs'=>['Egresos','#b45309'],'cash'=>['Flujo neto','#1d4ed8']] as $key=>[$label,$color]): ?>
        <div class="mt-1 grid gap-1 sm:grid-cols-2"><span class="text-sm"><?= e($label.': '.$ivMoney($totals[$key])) ?></span>
            <svg viewBox="0 0 300 16" role="img" aria-label="<?= e($label.' '.$year.': '.$ivMoney($totals[$key])) ?>" class="h-4 w-full"><rect width="300" height="16" fill="#f1f5f9"/><rect width="<?= number_format(abs($totals[$key])/$ivScale*300,2,'.','') ?>" height="16" fill="<?= $totals[$key]<0?'#b91c1c':$color ?>"/></svg>
        </div><?php endforeach; ?>
    </div><?php endforeach; ?>
    </div>
</details>
<details class="mt-4 rounded-xl border p-4"><summary class="min-h-11 cursor-pointer font-semibold">Serie mensual completa y valor de salida</summary>
    <div class="overflow-x-auto" tabindex="0" role="region" aria-label="Flujos mensuales en COP">
    <table class="w-full text-left text-sm"><caption class="py-3 text-left">COP nominales. Pérdidas = vacancia + no pago; egresos no incluyen financiación.</caption>
        <thead><tr><?php foreach (['Mes','Canon potencial','Pérdidas','Gastos','Extraordinarios','NOI','Reposiciones','Flujo','Valor presente'] as $label): ?><th class="p-2"><?= e($label) ?></th><?php endforeach; ?></tr></thead>
        <tbody><?php foreach ($ivDcf['months'] as $r): ?><tr class="border-t"><th class="p-2"><?= e($r['month']) ?></th>
            <?php foreach ([$r['potential'],$r['vacancy']+$r['arrears'],$r['costs'],$r['extra'],$r['noi'],$r['capex'],$r['cash'],$r['pv']] as $value): ?><td class="p-2"><?= e($ivMoney($value)) ?></td><?php endforeach; ?>
        </tr><?php endforeach; ?></tbody>
    </table></div>
    <p class="mt-4 text-sm">Ingreso terminal del año siguiente: <?= e($ivMoney($ivDcf['terminal_income'])) ?>. Tasa de salida: <?= $ivDcf['exit_rate']===null?'sin remanente':e($ivPct($ivDcf['exit_rate'])) ?>. Valor de salida: <?= e($ivMoney($ivDcf['terminal_value'])) ?>; gastos de venta: <?= e($ivMoney($ivDcf['selling_cost'])) ?>; salida descontada: <?= e($ivMoney($ivDcf['pv_terminal'])) ?>.</p>
</details>
