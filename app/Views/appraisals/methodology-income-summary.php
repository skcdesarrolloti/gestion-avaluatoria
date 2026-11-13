<?php use App\Services\IncomeConcept; ?>
<h3 class="text-xl font-semibold">Preparación reunida para ambos métodos</h3>
<p>Esta lectura conserva antecedentes, decisiones y pendientes. Las respuestas no verifican documentos ni confirman por sí solas la viabilidad de un método.</p>
<dl class="grid gap-3 sm:grid-cols-2">
<?php foreach (IncomeConcept::questions() as $summaryKey=>$summaryQuestion): ?>
    <div class="min-w-0 rounded-lg bg-slate-50 p-3">
        <dt class="text-sm font-semibold"><?= e($summaryQuestion['label']) ?></dt>
        <dd class="mt-1 break-words" x-text="label(<?= e(json_encode($summaryKey)) ?>,<?= e(json_encode($summaryQuestion['values'],JSON_UNESCAPED_UNICODE)) ?>)"><?= e($summaryQuestion['values'][$summaryKey==='occupancy'?$incomeEffectiveOccupancy:($incomeValues[$summaryKey] ?? '')] ?? 'Por completar o verificar') ?></dd>
    </div>
<?php endforeach; ?>
</dl>
<p class="rounded-lg bg-amber-50 p-3 text-sm" <?= !$incomeConflict?'style="display:none"':'' ?> x-show="occupancyConflict">Ocupación pendiente de conciliación entre Renta y capítulo 3. Consulta las fuentes en el tema B.</p>
<div class="grid gap-4 sm:grid-cols-2">
    <div class="rounded-lg border p-4"><h4 class="font-semibold">Capitalización directa</h4><p class="mt-2 text-sm">Estos antecedentes preparan la investigación de una renta representativa, sus gastos y una tasa consistente con la renta utilizada.</p></div>
    <div class="rounded-lg border p-4"><h4 class="font-semibold">Flujo de caja descontado</h4><p class="mt-2 text-sm">Utilizará los mismos antecedentes y la secuencia de contratos, ocupación, gastos y cambios previstos por período.</p></div>
</div>
<details class="rounded-lg border px-4">
    <summary class="min-h-11 cursor-pointer py-3 font-semibold">(?) Qué se estudiará después</summary>
    <p class="text-sm">Tasas, vacancia, gastos, horizonte y valor terminal requieren investigación y soporte. Aquí no se adoptan porcentajes ni se calcula un valor.</p>
    <p class="mt-2 text-sm">Se conserva CV ≤ 7,5 % como criterio en la etapa de comparables para la adopción del promedio; no es una tasa de vacancia.</p>
    <a class="my-2 inline-flex min-h-11 items-center text-sm text-blue-800 underline" href="<?= e(url('igac/documento/resolucion-igac-941-2026')) ?>" target="_blank" rel="noopener">IGAC 941 de 2026 · artículos 21–26</a>
</details>
