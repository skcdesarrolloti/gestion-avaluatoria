<section class="rounded-xl border bg-slate-50 p-4" aria-labelledby="reference-setup-title">
    <h3 class="text-xl font-semibold" id="reference-setup-title">Generar el historial automáticamente</h3>
    <p class="my-3 text-sm">Indica el inicio, el canon inicial y su regla. Se genera cada mes hasta <?= e(date('m/Y')) ?>, sin digitar facturas ni pagos. Referencia compartida por capitalización directa y FCD.</p>
    <div class="grid gap-4 md:grid-cols-2"><?php foreach (['start_date','initial_rent_amount','initial_rent_period','history_rule'] as $leaseField) require __DIR__.'/lease-field.php'; ?></div>
    <div class="mt-4 grid gap-4 md:grid-cols-2" x-show="values.history_rule==='ipc'"><?php foreach (['history_ipc_points','history_calendar'] as $leaseField) require __DIR__.'/lease-field.php'; ?></div>
    <div class="mt-4" x-show="values.history_rule==='annual' || (values.history_rule==='ipc' && values.history_calendar==='custom')"><?php $leaseField='first_adjustment_date';require __DIR__.'/lease-field.php'; ?></div>
    <details class="mt-3"><summary class="min-h-11 cursor-pointer py-3 font-semibold">Administración PH, IVA y final del contrato</summary>
        <p class="mb-3 text-sm">La cuota inicial de PH es el único importe por completar para reconstruir su serie. Se incrementa cada enero con IPC; una cuota distinta se registra abajo como excepción. Si conoces una cuota desde otra fecha, indícala. IVA: registra la tarifa aplicable o cero.</p>
        <div class="grid gap-4 md:grid-cols-2"><?php foreach (['history_admin_amount','history_admin_date','history_admin_rule','history_vat_rate','end_date'] as $leaseField) require __DIR__.'/lease-field.php'; ?></div>
    </details>
    <details class="mt-3"><summary class="min-h-11 cursor-pointer py-3 font-semibold">Academia · cómo se reconstruye y qué IPC se utiliza</summary>
        <?php $normKey='history';require __DIR__.'/income-norm-tip.php'; ?>
        <p class="mt-3 text-sm">Cada reajuste multiplica el canon anterior por 1 + (IPC + puntos) / 100. Por ejemplo, IPC 13,12 % + 2 puntos equivale a 15,12 %. En enero se usa el IPC nacional de diciembre del año anterior. PH usa solo IPC. Revisa el mes y la regla pactados.</p>
        <p class="mt-3 text-sm">Los cambios se redondean a centavos; inicio, final y vacancias parciales se distribuyen por días calendario. El mes actual se muestra completo como referencia. La historia no prueba recaudo, ni alimenta sola los flujos o porcentajes de costos. Sin información de vacancia se supone continuidad, no ocupación verificada.</p>
        <p class="mt-3 text-sm">Fuente: <a class="inline-flex min-h-11 items-center text-blue-800 underline" href="<?= e(\App\Services\LeaseReferenceFields::ipc()['source']) ?>" target="_blank" rel="noopener">DANE · IPC nacional anual de diciembre, 2016–2025 (Excel, abre aparte)</a>. Si falta un año, la serie queda pendiente; registra una excepción con el índice respaldado.</p>
        <div class="mt-3 overflow-x-auto"><table class="w-full text-sm"><caption class="text-left font-semibold">Incrementos automáticos aplicados al canon</caption><thead><tr><th class="p-2 text-left">Fecha</th><th class="p-2 text-left">Fuente y puntos</th><th class="p-2 text-left">Incremento</th></tr></thead>
            <tbody><template x-for="row in referenceHistory.sources" :key="row.date"><tr><td class="p-2" x-text="row.date"></td><td class="p-2" x-text="row.source"></td><td class="p-2" x-text="row.rate+' %'"></td></tr></template></tbody></table></div>
    </details>
    <?php $adjustmentGroup='rent_adjustments';$adjustmentTitle='Excepciones del canon · solo si difieren de la regla';require __DIR__.'/lease-adjustments.php'; ?>
    <?php $adjustmentGroup='admin_adjustments';$adjustmentTitle='Excepciones de administración PH';require __DIR__.'/lease-adjustments.php'; ?>
    <?php require __DIR__.'/lease-vacancies.php'; ?>
</section>
