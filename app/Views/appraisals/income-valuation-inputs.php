<section class="min-w-0 rounded-2xl border bg-white p-5 sm:p-8" x-show="incomeTab==='evidence' && incomePane!=='history'" x-cloak>
    <div x-show="incomePane==='projection'">
        <h3 class="text-xl font-semibold">Proyección futura · desde la fecha del avalúo</h3>
        <p class="mt-3">La historia permanece en su tabla. Aquí se prepara el escenario futuro compartido por los dos métodos.</p>
        <p class="mt-3">Fecha del avalúo: <strong x-text="context.value_date || 'Por completar en el encargo'"><?= e($ivContext['value_date'] ?: 'Por completar') ?></strong>. <button type="button" class="btn-secondary" @click="editIncomeOrigin()">Corregir fecha aquí</button></p>
        <?php $ivAcademy='costs';require __DIR__.'/income-valuation-academy.php'; ?>
        <div class="mt-4 grid gap-4 md:grid-cols-2">
            <div class="rounded-xl border p-4"><h4 class="font-semibold">Canon contractual documentado</h4>
                <p class="mt-2 text-sm" x-show="Object.keys(context.leases).length===0">No hay contrato vinculado a esta unidad. Completa su ficha si existe; no se exige inventar un contrato para un inmueble vacante.</p>
                <template x-for="(lease,id) in context.leases" :key="id"><p class="mt-3 text-sm"><strong x-text="lease.title"></strong><span class="block" x-text="money(lease.rent)+' COP/mes · '+(lease.start || 'Inicio pendiente')+' a '+(lease.end || 'Fin pendiente')"></span><span class="block" x-text="lease.scope"></span></p></template>
                <button type="button" class="btn-secondary mt-3" @click="openIncomePane('history')">Editar contrato e historial aquí</button>
            </div>
            <div class="rounded-xl border p-4"><?php $ivGroup='projection';$ivDynamic=false;$ivKey='market_rent';require __DIR__.'/income-valuation-field.php'; ?>
                <p class="mt-2 text-sm">Usa el canon adoptado y sustentado en la investigación de arriendos. No se traslada automáticamente el promedio de muestras ni se reemplazan pagos históricos.</p>
            </div>
        </div>
        <div class="mt-4 grid gap-4 md:grid-cols-2">
            <div x-show="!context.value_date"><?php $ivKey='date';require __DIR__.'/income-valuation-field.php'; ?></div>
            <?php $ivKey='income_source';require __DIR__.'/income-valuation-field.php'; ?>
            <div x-show="state.projection.income_source==='lease' && Object.keys(context.leases).length>0"><?php $ivKey='lease_id';require __DIR__.'/income-valuation-field.php'; ?></div>
            <?php $ivKey='income_support';require __DIR__.'/income-valuation-field.php'; ?>
        </div>
        <details class="mt-5 rounded-xl border p-4" open><summary class="min-h-11 cursor-pointer font-semibold">Calendario futuro · horizonte, incrementos y vencimiento</summary>
            <div class="mt-3 grid gap-4 md:grid-cols-2"><?php foreach (['months','growth','next_increase','after_contract','relet_date','projection_support'] as $ivKey) require __DIR__.'/income-valuation-field.php'; ?></div>
            <p class="mt-3 text-sm">La tabla mensual de la proyección se presenta en Resultados al calcular en la etapa 4. Cambiar supuestos conserva el cálculo anterior y lo marca para actualizar.</p>
        </details>
        <details class="mt-5 rounded-xl border p-4"><summary class="min-h-11 cursor-pointer font-semibold">Eventos mensuales futuros · gracia, gastos o reposiciones</summary>
            <?php $ivGroup='events';require __DIR__.'/income-valuation-rows.php'; ?>
        </details>
    </div>
    <div x-show="incomePane==='costs'">
        <h3 class="text-xl font-semibold">Costos con soporte · compartidos por los dos métodos</h3>
        <?php $ivAcademy='costs';require __DIR__.'/income-valuation-academy.php';$normKey='costs';require __DIR__.'/income-norm-tip.php'; ?>
        <?php require __DIR__.'/income-cost-table.php'; ?>
        <details class="mt-5 rounded-xl border p-4"><summary class="min-h-11 cursor-pointer font-semibold">Vacancia y pérdidas de ingreso · sin duplicar el calendario</summary>
            <div class="grid gap-4 md:grid-cols-2"><?php $ivGroup='projection';$ivDynamic=false;foreach (['vacancy','arrears','loss_support'] as $ivKey) require __DIR__.'/income-valuation-field.php'; ?></div>
        </details>
        <div class="mt-4 grid gap-4 md:grid-cols-2"><?php foreach (['cost_review','cost_support'] as $ivKey) require __DIR__.'/income-valuation-field.php'; ?></div>
    </div>
</section>
