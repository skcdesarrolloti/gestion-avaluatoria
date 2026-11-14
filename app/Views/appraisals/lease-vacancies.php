<details class="mt-3 rounded-xl border p-4">
    <summary class="min-h-11 cursor-pointer font-semibold">Vacancias · indicar únicamente los períodos sin ocupación</summary>
    <p class="my-3 text-sm">Una fila por intervalo, con ambas fechas incluidas. Se descuenta el canon de esos días en la referencia; la cuota PH continúa. No registrar pagos no significa que el inmueble estuviera vacío.</p>
    <template x-for="(period,key) in values.vacancy_periods" :key="key"><div class="mb-4 grid gap-3 border-b pb-3 md:grid-cols-2">
        <?php foreach (['start'=>'Inicio de vacancia','end'=>'Final de vacancia','source'=>'Fuente o explicación de la vacancia'] as $key=>$label): ?>
        <label class="label"><span><?= e($label) ?></span><input class="input" type="<?= $key==='source'?'text':'date' ?>" x-model="period.<?= $key ?>" :name="'lease[vacancy_periods]['+key+'][<?= $key ?>]'" <?= $key==='source'?'placeholder="Ej. Entrega de octubre e inicio del siguiente contrato"':'' ?>
            :aria-invalid="Boolean(issues['lease[vacancy_periods]['+key+'][<?= $key ?>]'])" :aria-describedby="'vacancy-'+key+'-<?= $key ?>-error'">
            <span class="text-red-800" :id="'vacancy-'+key+'-<?= $key ?>-error'" x-text="issues['lease[vacancy_periods]['+key+'][<?= $key ?>]'] || ''"></span></label>
        <?php endforeach; ?>
        <div><button class="btn-secondary" type="button" @click="clearAdjustment(key,'vacancy_periods')">Vaciar período</button></div>
    </div></template>
    <button class="btn-secondary" type="button" @click="addAdjustment('vacancy_periods')">Agregar período de vacancia</button>
</details>
