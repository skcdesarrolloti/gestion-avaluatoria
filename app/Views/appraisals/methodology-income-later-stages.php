<section class="rounded-2xl border bg-white p-5 sm:p-8" x-show="incomeTab==='evidence'" <?= $incomeInitial==='evidence'?'':'x-cloak' ?>>
    <h2 class="text-2xl font-semibold">3. Ingresos y gastos</h2>
    <p class="mt-3">Organiza los soportes antes de estimar una renta. El contrato, los pagos y las responsabilidades se registran en la ficha del arriendo de la etapa 1.</p>
    <ul class="mt-4 list-disc space-y-3 pl-5">
        <li><strong>Ingresos:</strong> distinguir canon pactado, cobros documentados y evidencia de arriendos comparables.</li>
        <li><strong>Vacancia y falta de pago:</strong> reunir antecedentes de ocupación y cobros con sus períodos y fuentes.</li>
        <li><strong>Gastos:</strong> identificar qué asume el propietario, qué corresponde al arrendatario y qué pagos se recuperan.</li>
        <li><strong>Soportes:</strong> relacionar contratos, liquidaciones, facturas y condiciones particulares del tipo de inmueble.</li>
    </ul>
    <p class="mt-4 rounded-lg bg-amber-50 p-3 text-sm">Preparación pendiente de desarrollar: aquí todavía no se calculan ingresos netos ni se adoptan porcentajes de vacancia o gastos.</p>
    <button type="button" class="btn-secondary mt-4" @click="openStage('concept')">Consultar o completar la ficha del arriendo</button>
    <button type="button" class="btn-secondary mt-4" @click="openStage('research')">Consultar la investigación de arriendos</button>
</section>
<section class="rounded-2xl border bg-white p-5 sm:p-8" x-show="incomeTab==='valuation'" <?= $incomeInitial==='valuation'?'':'x-cloak' ?>>
    <h2 class="text-2xl font-semibold">4. Valoración por los dos métodos</h2>
    <p class="mt-3">Ambos consultarán el mismo inmueble, contratos, muestras y soportes. Cada método tendrá sus parámetros y su resultado identificado.</p>
    <div class="mt-5 grid gap-4 sm:grid-cols-2">
        <div class="rounded-xl border p-4"><h3 class="text-xl font-semibold">Capitalización directa</h3>
            <p class="mt-3">Preparar la investigación de una renta representativa, sus gastos y una tasa de capitalización consistente con la renta utilizada.</p></div>
        <div class="rounded-xl border p-4"><h3 class="text-xl font-semibold">Flujo de caja descontado</h3>
            <p class="mt-3">Preparar la secuencia de contratos, ocupación, ingresos, gastos y cambios por período; sustentar horizonte, tasa de descuento y valor terminal.</p></div>
    </div>
    <p class="mt-4 rounded-lg bg-amber-50 p-3 text-sm">Los cálculos de ambos métodos están pendientes. No hay tasas predeterminadas, proyecciones ni valores adoptados en esta etapa.</p>
    <p class="mt-3 text-sm">La vacancia, la tasa de capitalización y la tasa de descuento requieren sustentaciones propias. El criterio CV ≤ 7,5 % se conserva para el análisis de la muestra.</p>
</section>
<section class="rounded-2xl border bg-white p-5 sm:p-8" x-show="incomeTab==='review'" <?= $incomeInitial==='review'?'':'x-cloak' ?>>
    <h2 class="text-2xl font-semibold">5. Revisión y sustentación</h2>
    <p class="mt-3">Prepara qué deberá explicar la memoria cuando se desarrollen y revisen los cálculos.</p>
    <ul class="mt-4 list-disc space-y-3 pl-5">
        <li>Inmueble, derecho y unidades comprendidas, según el encargo y el capítulo 3.</li>
        <li>Contratos, muestras y soportes examinados; información comprobada y pendiente.</li>
        <li>Supuestos y fuentes utilizados en cada método, con sus limitaciones.</li>
        <li>Resultados de capitalización directa y flujo descontado, sus diferencias y justificación.</li>
    </ul>
    <p class="mt-4 rounded-lg bg-amber-50 p-3 text-sm">La revisión de resultados y la memoria final de Renta están pendientes. Recorrer estas etapas no confirma información ni adopta un valor.</p>
    <button type="button" class="btn-secondary mt-4" @click="openStage('concept')">Volver a contratos y antecedentes conservados</button>
</section>
