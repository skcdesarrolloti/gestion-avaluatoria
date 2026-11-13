<?php use App\Services\IncomeConcept; ?>
<h3 class="text-xl font-semibold">Ocupación y condiciones del arriendo</h3>
<p>Consulta las respuestas registradas. Para corregirlas, abre la pregunta correspondiente y conserva su soporte.</p>
<?php if ($incomeSubject['occupancy']!==''): ?>
<div class="rounded-lg bg-slate-50 p-4" data-income-occupancy-source>
    <p class="font-semibold">Ocupación consultada del capítulo 3</p>
    <p class="mt-1"><?= e(IncomeConcept::questions()['occupancy']['values'][$incomeSubject['occupancy']]) ?></p>
    <p class="mt-2 text-sm">Corresponde a la única unidad completa del expediente. Se consulta en su origen, sin copiarla como otra respuesta.</p>
    <a class="inline-flex min-h-11 items-center text-sm text-blue-800 underline" href="<?= e(url($incomeSubject['source_url'].'#usos')) ?>">Consultar o corregir ocupación en 3.1</a>
</div>
<?php elseif (!$incomeSubject['same_scope']): ?>
<p class="rounded-lg bg-amber-50 p-3 text-sm">La ocupación global no se atribuye automáticamente a esta unidad o parte. Precisa la situación de este recorrido con su fuente y fecha.</p>
<?php endif; ?>
<p class="rounded-lg bg-amber-50 p-3 text-sm" <?= !$incomeConflict?'style="display:none"':'' ?> x-show="occupancyConflict" role="status">La ocupación guardada en Renta difiere del capítulo 3. Revisa unidad, alcance, fechas y soportes; se conservan ambas referencias sin sustituirlas automáticamente.</p>
<p class="rounded-lg bg-teal-50 p-3 text-sm" <?= $incomeEffectiveOccupancy!=='owner'?'style="display:none"':'' ?> x-show="occupancy==='owner'">El uso del propietario no acredita un canon cobrado. La posibilidad de arriendo se investigará después en el mercado.</p>
<details class="rounded-lg border px-4">
    <summary class="min-h-11 cursor-pointer py-3 font-semibold">Consultar canon, administración y antecedentes del encargo y PH</summary>
    <p class="text-sm">Son referencias del expediente y de la copropiedad; comprueba su correspondencia con esta unidad. No se adoptan como ingreso neto ni gasto del propietario.</p>
    <?php $incomeOriginRows=$incomeSubject['economic']; require __DIR__.'/methodology-income-origin-rows.php'; ?>
</details>
