<?php $adjustmentGroup=$adjustmentGroup ?? 'rent_adjustments';$adjustmentTitle=$adjustmentTitle ?? 'Excepciones del canon'; ?>
<details class="mt-3 rounded-xl border p-4">
    <summary class="min-h-11 cursor-pointer font-semibold"><?= e($adjustmentTitle) ?></summary>
    <p class="my-3 text-sm">Registra solo lo que cambia: un porcentaje o un nuevo importe mensual y su soporte. Si coincide con un reajuste automático, lo reemplaza; en otra fecha, se aplica desde ese día y continúa con la regla elegida.</p>
    <div class="mt-3 overflow-x-auto" tabindex="0" aria-label="<?= e($adjustmentTitle) ?>, tabla desplazable">
        <table class="w-full text-sm"><thead><tr><?php foreach (['Fecha efectiva','Incremento (%)','O nuevo importe mensual (COP)','Soporte','Acción'] as $heading): ?><th class="p-2 text-left"><?= e($heading) ?></th><?php endforeach; ?></tr></thead>
        <tbody><template x-for="(adjustment,key) in values.<?= e($adjustmentGroup) ?>" :key="key"><tr>
            <?php foreach (['date'=>'Fecha efectiva','rate'=>'Incremento excepcional (%)','amount'=>'Nuevo importe mensual (COP)','source'=>'Soporte del cambio'] as $key=>$label): ?>
            <td class="p-2" style="min-width:<?= $key==='source'?'260':'170' ?>px"><label class="label"><span><?= e($label) ?></span>
                <input class="input" type="<?= $key==='date'?'date':'text' ?>" x-model="adjustment.<?= $key ?>" :name="'lease[<?= e($adjustmentGroup) ?>]['+key+'][<?= $key ?>]'" <?= $key==='date'?'':'placeholder="'.e($key==='source'?'Ej. Acta, cláusula o índice y año':'Vacío si se usa la otra modalidad').'"' ?>
                    :aria-invalid="Boolean(issues['lease[<?= e($adjustmentGroup) ?>]['+key+'][<?= $key ?>]'])" :aria-describedby="'<?= e($adjustmentGroup) ?>-'+key+'-<?= $key ?>-error'">
                <span class="text-red-800" :id="'<?= e($adjustmentGroup) ?>-'+key+'-<?= $key ?>-error'" x-text="issues['lease[<?= e($adjustmentGroup) ?>]['+key+'][<?= $key ?>]'] || ''"></span>
            </label></td><?php endforeach; ?>
            <td class="p-2"><button type="button" class="btn-secondary" @click="clearAdjustment(key,'<?= e($adjustmentGroup) ?>')">Vaciar fila</button></td>
        </tr></template></tbody></table>
    </div>
    <button type="button" class="btn-secondary mt-3" @click="addAdjustment('<?= e($adjustmentGroup) ?>')">Agregar excepción</button>
</details>
