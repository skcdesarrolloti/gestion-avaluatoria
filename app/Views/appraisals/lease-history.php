<section class="rounded-xl border p-4" id="historial-arriendo" aria-labelledby="reference-history-title">
    <h3 class="text-xl font-semibold" id="reference-history-title">Historial mensual automático · referencia</h3>
    <p class="mt-3 text-sm">Canon, administración e IVA separados. El total es una referencia mensual, no un pago recibido ni ingreso neto. La proyección futura conserva su propia tabla y supuestos.</p>
    <p class="mt-3 font-semibold" role="status" x-text="referenceHistory.message"></p>
    <ul class="my-3 list-disc pl-5 text-sm"><template x-for="issue in referenceHistory.issues" :key="issue"><li x-text="issue"></li></template></ul>
    <div class="mt-3" x-show="referenceHistory.rows.length">
        <label class="label" for="reference-year">Período que quieres ver</label>
        <select class="input" id="reference-year" x-model="referenceYear" aria-describedby="reference-year-help">
            <option value="">Todos los años desde el inicio</option><template x-for="year in referenceHistory.years" :key="year"><option :value="year" x-text="year"></option></template>
        </select>
        <p class="mt-1 text-xs" id="reference-year-help">Se generan todos los meses. Filtrar el año solo cambia la vista.</p>
        <div class="relative mt-4 overflow-x-auto" style="max-height:560px;overflow-y:auto" tabindex="0" aria-label="Referencia mensual automática, tabla desplazable">
        <table class="w-full border-collapse text-sm"><caption class="sr-only">Serie mensual calculada del arriendo en COP</caption>
            <thead class="bg-slate-50" style="position:sticky;top:0;z-index:1"><tr><?php foreach (['Mes','Ocupación de referencia','Canon (COP)','PH (COP)','IVA (COP)','Total (COP)','Reajustes'] as $heading): ?><th scope="col" class="border p-3 text-left"><?= e($heading) ?></th><?php endforeach; ?></tr></thead>
            <tbody><template x-for="row in referenceRows" :key="row.month"><tr>
                <th scope="row" class="border p-3 text-left" style="min-width:130px"><span x-text="row.month"></span><small class="block text-amber-800" x-show="row.afterCutoff">Posterior al avalúo</small><small class="block" x-show="row.current">Mes actual · referencia completa</small></th>
                <td class="border p-3" style="min-width:180px" x-text="row.state"></td>
                <?php foreach (['reference','admin','vat','total'] as $column): ?><td class="border p-3" style="min-width:140px"><span x-text="money(row.<?= $column ?>)"></span><?php if ($column==='admin'): ?><small class="block" x-show="row.adminManual">Importe del mes conservado</small><?php endif; ?></td><?php endforeach; ?>
                <td class="border p-3" style="min-width:230px" x-text="row.note || 'Sin cambio'"></td>
            </tr></template></tbody>
        </table></div>
        <details class="mt-3"><summary class="min-h-11 cursor-pointer py-3 font-semibold">Ver comportamiento del canon · gráfico mensual</summary>
            <p class="mb-3 text-xs">Altura proporcional al canon de referencia; un dato pendiente no se presenta como cero. Las vacancias informadas reducen el canon de esos días.</p>
            <div class="flex items-end gap-2 overflow-x-auto border-b p-3" tabindex="0" aria-label="Gráfico del canon mensual de referencia">
                <template x-for="row in referenceRows" :key="row.month"><div style="min-width:70px" class="text-center text-xs">
                    <span x-text="money(row.reference)"></span><div class="flex items-end justify-center" style="height:120px"><div class="bg-teal-700" style="width:36px" :style="'height:'+((row.reference || 0)/referenceMax*120)+'px'"></div></div><span x-text="row.month"></span>
                </div></template>
            </div>
        </details>
    </div>
    <p class="mt-3 text-sm" x-show="ledgerDirty">Vista previa del borrador; espera la confirmación de guardado antes de salir.</p>
</section>
<details class="rounded-xl border p-4"><summary class="min-h-11 cursor-pointer font-semibold">Pagos y comprobantes ya registrados · consulta opcional</summary>
    <p class="my-3 text-sm">Los datos anteriores se conservan. No es necesario llenar esta tabla para generar el historial automático. Solo registra facturas, pagos o costos si dispones de ellos y son útiles para el análisis.</p>
    <?php require __DIR__.'/lease-history-records.php'; ?>
</details>
