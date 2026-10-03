<?php

use App\Services\CostMethodReview;
require __DIR__.'/methodology-cost-academy-reading.php';
if (($selected['method'] ?? '') !== 'costo' || $componentKey === '') return;
$costUnit=$components[$componentKey]['unit'] ?? [];
$costChecks=CostMethodReview::checks($costUnit,$selected);
$costOk=count(array_filter($costChecks,static fn($row)=>$row['state']==='ok'));
$costPending=count(array_filter($costChecks,static fn($row)=>$row['state']==='missing'));
?>
<section class="rounded-2xl border bg-white p-5 sm:p-8">
    <p class="eyebrow">C1 · Academia y revisión del costo</p>
    <h2 class="mt-2 text-2xl font-semibold"><?= e($componentLabel) ?></h2>
    <p class="mt-3 text-sm text-slate-600">Consulta los datos de esta unidad y define su alcance en C2. La academia no cambia el método registrado ni adopta precios o valores.</p>
    <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
        <strong class="text-emerald-900"><?= $costOk ?> datos registrados</strong> · <?= $costPending ?> pendientes · <?= count($costChecks)-$costOk-$costPending ?> no aplican
        <p class="mt-1 text-sm">Verde significa dato registrado para la revisión. La suficiencia de precios, derechos y soportes requiere análisis técnico.</p>
    </div>
    <div class="mt-4 space-y-3">
        <?php foreach ($costChecks as $check): $ok=$check['state']==='ok'; $na=$check['state']==='na'; ?>
        <article class="rounded-xl border <?= $ok?'border-emerald-200 bg-emerald-50':($na?'border-slate-200 bg-slate-50':'border-amber-200 bg-amber-50') ?> p-4">
            <h3 class="font-semibold"><?= $ok?'● Registrado':($na?'● No aplica':'● Pendiente') ?> · <?= e($check['label']) ?></h3>
            <p class="mt-1 whitespace-pre-wrap break-words text-sm"><?= e($check['value'] ?: 'Sin dato') ?></p>
            <p class="mt-1 text-xs"><?= e($check['source'].' · '.$check['help']) ?></p>
            <a class="mt-2 inline-flex min-h-11 items-center font-semibold text-blue-800 underline" href="<?= e($check['destination']==='scope'
                ? $flowUrl('2') : url('avaluos/'.$record['id'].'/bien-sujeto?'.http_build_query(['section'=>$check['destination'],'unit'=>$sourceUnitKey ?? $componentKey,'from'=>'metodologia','check_component'=>$componentKey]).'#unidades-capitulo-3')) ?>"><?= $ok?'Consultar':'Diligenciar / corregir' ?> en <?= e($check['source']) ?></a>
        </article>
        <?php endforeach; ?>
    </div>
    <a class="btn-secondary mt-4" href="<?= e($flowUrl('1'). '&consult_method=costo') ?>">Actualizar consulta C1</a>
</section>
<p class="mt-4 text-sm text-slate-600">C1 es consulta académica y revisión de datos. La selección y los cálculos se trabajan en sus etapas correspondientes.</p>
