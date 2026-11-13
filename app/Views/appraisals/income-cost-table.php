<?php $ivGroup='costs';$ivDynamic=true;$ivCompact=true;unset($ivIndex); ?>
<p class="mt-3 text-sm">Conserva importes reales cuando estén documentados. La sugerencia histórica es gasto del propietario / canon recibido × 100, sobre los mismos meses revisados y cerrados al avalúo; muestra su cobertura. No representa automáticamente un año estabilizado ni una tarifa por tipo.</p>
<label class="label mt-3">Historial para consultar sugerencias
    <select class="input" x-model="historyLease"><option value="">Contrato de la proyección o único contrato disponible</option><template x-for="(lease,id) in context.leases" :key="id"><option :value="id" x-text="lease.title"></option></template></select>
</label>
<p class="mt-2 text-sm">También puedes documentar una referencia externa del mismo tipo, ciudad y período. La 941 identifica rubros; cada porcentaje requiere fuente, base y aceptación del analista.</p>
<button type="button" class="btn-secondary mt-3" @click="addCatalogCosts()">Agregar rubros de la 941 que falten</button>
<div class="relative mt-4 overflow-x-auto" tabindex="0" aria-label="Costos, porcentajes y soportes, tabla desplazable">
<table class="w-full border-collapse text-sm"><thead class="bg-slate-50"><tr><?php foreach (['Rubro y sugerencia','Responsable','Valor adoptado','Base','Aumento anual','Soporte adoptado','Acción'] as $label): ?><th scope="col" class="border p-3 text-left"><?= e($label) ?></th><?php endforeach; ?></tr></thead>
<tbody><template x-for="(row,index) in state.costs" :key="index"><tr>
    <td class="border p-3" style="min-width:290px">
        <?php $ivKey='name';require __DIR__.'/income-valuation-field.php'; ?>
        <p class="mt-3 text-sm" x-text="costSuggestion(row)?'Sugerencia sustentada: '+money(costSuggestion(row).rate)+' % · '+(costSuggestion(row).basis==='effective'?'ingreso efectivo':'renta potencial'):'Sin porcentaje sustentado disponible'"></p>
        <details class="mt-2"><summary class="min-h-11 cursor-pointer font-semibold">Ver cálculo / documentar referencia</summary>
            <?php $ivKey='category';require __DIR__.'/income-valuation-field.php'; ?>
            <p class="text-xs" x-text="costSuggestion(row)?.source || costCatalog[row.category]?.help || 'Clasifica el rubro y documenta su fuente.'"></p>
            <?php foreach (['reference_percent','reference_basis','reference_source'] as $ivKey) require __DIR__.'/income-valuation-field.php'; ?>
        </details>
        <button type="button" class="btn-secondary mt-2" @click="applyCostSuggestion(row)" :disabled="!costSuggestion(row)">Usar porcentaje y soporte sugeridos</button>
    </td>
    <?php foreach (['payer','amount','basis','growth','source'] as $ivKey): ?><td class="border p-3" style="min-width:<?= $ivKey==='source'?'280':'190' ?>px"><?php require __DIR__.'/income-valuation-field.php'; ?></td><?php endforeach; ?>
    <td class="border p-3"><button type="button" class="btn-secondary" @click="removeRow('costs',index)">Retirar del borrador</button></td>
</tr></template></tbody></table></div>
<p class="mt-2 text-sm" x-show="state.costs.length===0">No hay costos registrados. Agrega los rubros aplicables; los no aplicables requieren explicación.</p>
<button type="button" class="btn-secondary mt-3" @click="addRow('costs')">Agregar otro costo</button>
<noscript><?php $ivDynamic=false;foreach ($ivState['costs'] as $ivIndex=>$entry): ?><div class="mt-3 grid gap-4 md:grid-cols-2"><?php foreach (array_keys(\App\Services\IncomeValuationFields::group('costs')) as $ivKey) require __DIR__.'/income-valuation-field.php'; ?></div><?php endforeach; ?><button type="submit" class="btn-secondary" name="operation" value="add_costs">Agregar costo</button></noscript>
<?php $ivDynamic=false;unset($ivIndex,$ivCompact); ?>
