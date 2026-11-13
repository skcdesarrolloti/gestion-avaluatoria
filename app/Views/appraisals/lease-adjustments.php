<details class="rounded-xl border p-4">
    <summary class="min-h-11 cursor-pointer font-semibold">Reconstruir el canon histórico · incrementos y modificaciones</summary>
    <p class="my-3 text-sm">La reconstrucción usa canon inicial, inicio y modificaciones con soporte. Aniversarios sin dato quedan pendientes. Cada modificación indica un porcentaje o un nuevo canon mensual, nunca ambos. Meses parciales: distribución por días calendario; la factura documentada se conserva aparte.</p>
    <?php $leaseField='history_rule';require __DIR__.'/lease-field.php';$normKey='rent';require __DIR__.'/income-norm-tip.php'; ?>
    <div class="mt-3 overflow-x-auto" tabindex="0" aria-label="Incrementos documentados, tabla desplazable">
        <table class="w-full text-sm"><thead><tr><?php foreach (['Fecha efectiva','Incremento (%)','O nuevo canon mensual (COP)','Índice, año, cláusula y soporte','Acción'] as $heading): ?><th class="p-2 text-left"><?= e($heading) ?></th><?php endforeach; ?></tr></thead>
        <tbody><template x-for="(adjustment,key) in values.rent_adjustments" :key="key"><tr>
            <?php foreach (['date'=>'Fecha efectiva','rate'=>'Incremento documentado (%)','amount'=>'Nuevo canon mensual (COP)','source'=>'Soporte del incremento'] as $key=>$label): ?>
            <td class="p-2" style="min-width:<?= $key==='source'?'260':'170' ?>px"><label class="label"><span><?= e($label) ?></span>
                <input class="input" type="<?= $key==='date'?'date':'text' ?>" x-model="adjustment.<?= $key ?>" :name="'lease[rent_adjustments]['+key+'][<?= $key ?>]'" <?= $key==='date'?'':'placeholder="'.e($key==='source'?'Ej. IPC del año documentado y cláusula':'Vacío si se usa la otra modalidad').'"' ?>
                    :aria-invalid="Boolean(issues['lease[rent_adjustments]['+key+'][<?= $key ?>]'])" :aria-describedby="'adjustment-'+key+'-<?= $key ?>-error'">
                <span class="text-red-800" :id="'adjustment-'+key+'-<?= $key ?>-error'" x-text="issues['lease[rent_adjustments]['+key+'][<?= $key ?>]'] || ''"></span>
            </label></td><?php endforeach; ?>
            <td class="p-2"><button type="button" class="btn-secondary" @click="clearAdjustment(key)">Vaciar fila</button></td>
        </tr></template></tbody></table>
    </div>
    <button type="button" class="btn-secondary mt-3" @click="addAdjustment()">Agregar modificación documentada</button>
    <p class="mt-2 text-sm">No se descarga ni presume IPC. Un importe sin fecha y soporte no permite reconstruir la serie.</p>
</details>
