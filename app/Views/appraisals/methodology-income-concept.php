<?php
use App\Services\{IncomeSubjectContext,IncomeConcept,LeaseRegister,UnitOccupation};
if ($componentKey!=='' && ($selected['method'] ?? '')==='renta'):
$incomeSubject=IncomeSubjectContext::build($record,$subject ?? [],$units ?? [],$components[$componentKey] ?? [],$phProfile ?? []);
$leaseUnit=$components[$componentKey]['unit'];$leaseUnitId=(string)$leaseUnit['id'];
$leaseOccupation=UnitOccupation::read($leaseUnit);
$leaseLegacy=LeaseRegister::legacy($record,$leaseUnitId);
?>
<section id="renta-concepto" class="mb-6 rounded-2xl border bg-white p-5 sm:p-8" data-income-preparation-version="3">
    <p class="eyebrow">Renta · <?= e($componentLabel) ?></p>
    <h2 class="mt-2 text-2xl font-semibold">1. Inmueble y arriendo</h2>
    <p class="mt-3 rounded-lg bg-teal-50 p-3 font-semibold">Una ficha compartida por capitalización directa y flujo de caja descontado.</p>
    <p class="mt-3 text-sm">Registra lo que consta en contratos y soportes. Las fechas, importes e incrementos quedan como antecedentes; todavía no se calculan rentas, tasas ni valores.</p>
    <div class="mt-4 rounded-xl border bg-slate-50 p-4">
        <h3 class="font-semibold">Inmueble consultado del capítulo 3</h3>
        <p class="mt-2"><?= e($leaseUnit['label']) ?> · <?= e($leaseUnit['property_type'] ?: ($record['tipo_inmueble'] ?? '')) ?></p>
        <p class="mt-2 text-sm">Ocupación de la unidad: <strong><?= e(IncomeConcept::questions()['occupancy']['values'][$leaseOccupation['occupancy'] ?? ''] ?? 'Por completar en el capítulo 3') ?></strong></p>
        <?php if (empty($leaseOccupation['occupancy']) && !empty($subject['current_occupation'])): ?>
        <p class="mt-1 text-sm">Antecedente general del capítulo 3: <?= e(\App\Support\AppraisalSubjectCatalog::selects()['current_occupation'][1][$subject['current_occupation']] ?? $subject['current_occupation']) ?>. Falta precisar a cuáles unidades corresponde.</p>
        <?php endif; ?>
        <?php if (!empty($leaseOccupation['source'])): ?><p class="mt-1 text-sm"><?= e($leaseOccupation['source']) ?> · <?= e($leaseOccupation['date'] ?? '') ?></p><?php endif; ?>
        <a class="inline-flex min-h-11 items-center font-semibold text-blue-800 underline" href="<?= e(url($incomeSubject['source_url'].'&section=tipologias#ocupacion-'.$leaseUnitId)) ?>">Consultar o completar inmueble y ocupación en el capítulo 3</a>
        <details><summary class="min-h-11 cursor-pointer py-3 text-sm font-semibold">Consultar áreas, descripción y referencias del inmueble</summary>
            <?php $incomeOriginRows=$incomeSubject['rows']; require __DIR__.'/methodology-income-origin-rows.php'; ?>
            <p class="mt-2 text-sm">El piso de ubicación, la altura del edificio, los acabados y la PH se completan en el capítulo 3. Las áreas no se convierten automáticamente en área arrendable.</p>
        </details>
        <details><summary class="min-h-11 cursor-pointer py-3 text-sm font-semibold">Ubicación, características y PH · consultar en origen</summary>
            <?php $incomeOriginRows=array_values(array_filter($incomeSubject['context'],static fn($row)=>trim((string)$row['value'])!=='')); require __DIR__.'/methodology-income-origin-rows.php'; ?>
            <?php $incomeOriginRows=$incomeSubject['physical']; require __DIR__.'/methodology-income-origin-rows.php'; ?>
            <p class="mt-3 text-sm">Los datos generales del expediente y de la copropiedad requieren comprobar su alcance para esta unidad. No se atribuyen como gasto pagado.</p>
            <?php $incomeOriginRows=array_values(array_filter($incomeSubject['economic'],static fn($row)=>str_starts_with($row['source'],'3.5') && trim((string)$row['value'])!=='')); require __DIR__.'/methodology-income-origin-rows.php'; ?>
            <div class="mt-3 flex flex-wrap gap-3"><?php foreach ($incomeSubject['references'] as $reference): ?><a class="inline-flex min-h-11 items-center text-sm text-blue-800 underline" href="<?= e(url($reference['url'])) ?>"><?= e($reference['label']) ?></a><?php endforeach; ?></div>
        </details>
    </div>
    <a class="mt-2 inline-flex min-h-11 items-center font-semibold text-blue-800 underline" href="<?= e($flowUrl('1')) ?>#renta-orientacion">Consultar ayudas y soportes en Academia de Renta</a>
    <?php require __DIR__.'/lease-form.php'; ?>
    <?php require __DIR__.'/lease-legacy.php'; ?>
    <?php require __DIR__.'/lease-legacy-recovery.php'; ?>
</section>
<?php else: ?><p class="rounded-xl border bg-white p-5">Selecciona Renta para una unidad antes de diligenciar su ficha de arriendo.</p><?php endif; ?>
