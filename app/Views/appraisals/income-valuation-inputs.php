<section class="rounded-2xl border bg-white p-5 sm:p-8" x-show="incomeTab==='evidence'" <?= $incomeInitial==='evidence'?'':'x-cloak' ?>>
    <h2 class="text-2xl font-semibold">3. Ingresos y gastos</h2>
    <p class="mt-3">Un solo conjunto de supuestos para los dos métodos. El inmueble se consulta del capítulo 3 y el contrato de la etapa 1. Canon separado de PH e IVA; no sumes de nuevo los anexos incluidos.</p>
    <button type="button" class="btn-secondary mt-3" @click="openStage('concept')">Consultar contrato, pagos y PH en origen</button>
    <?php $ivAcademy='costs';require __DIR__.'/income-valuation-academy.php'; ?>
    <h3 class="mt-5 text-xl font-semibold">Renta de partida</h3>
    <?php if ($ivContext['value_date']!==''): ?><p class="mt-3">Fecha del avalúo consultada del encargo: <strong><?= e($ivContext['value_date']) ?></strong>. <a class="text-blue-800 underline" href="<?= e(url('avaluos/'.$record['id'].'/expediente')) ?>">Consultar en origen</a></p><?php endif; ?>
    <div class="mt-4 grid gap-4 md:grid-cols-2">
        <?php $ivGroup='projection';$ivDynamic=false;foreach (['date','income_source','lease_id','market_rent','income_support'] as $ivKey) if ($ivKey!=='date' || $ivContext['value_date']==='') require __DIR__.'/income-valuation-field.php'; ?>
    </div>
    <details class="mt-5 rounded-xl border p-4"><summary class="min-h-11 cursor-pointer font-semibold">Calendario del flujo · horizonte, incrementos y vencimiento</summary>
        <p class="mb-3 text-sm">El canon de partida debe corresponder a la fecha del avalúo. El historial conserva pagos reales; esta sección proyecta un escenario futuro.</p>
        <div class="grid gap-4 md:grid-cols-2"><?php foreach (['months','growth','next_increase','after_contract','relet_date','projection_support'] as $ivKey) require __DIR__.'/income-valuation-field.php'; ?></div>
    </details>
    <details class="mt-5 rounded-xl border p-4"><summary class="min-h-11 cursor-pointer font-semibold">Vacancia y costos · quién paga, base y soporte</summary>
        <div class="grid gap-4 md:grid-cols-2"><?php foreach (['vacancy','arrears','loss_support'] as $ivKey) require __DIR__.'/income-valuation-field.php'; ?></div>
        <?php $ivGroup='costs';require __DIR__.'/income-valuation-rows.php'; ?>
        <div class="mt-4 grid gap-4 md:grid-cols-2"><?php $ivGroup='projection';$ivDynamic=false;foreach (['cost_review','cost_support'] as $ivKey) require __DIR__.'/income-valuation-field.php'; ?></div>
    </details>
    <details class="mt-5 rounded-xl border p-4"><summary class="min-h-11 cursor-pointer font-semibold">Eventos mensuales · gracia, gastos o reposiciones</summary>
        <?php $ivGroup='events';require __DIR__.'/income-valuation-rows.php'; ?>
    </details>
</section>
