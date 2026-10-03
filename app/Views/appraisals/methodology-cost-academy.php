<?php
use App\Services\CostMethodAcademy;
use App\Services\CostMethodReview;
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
                ? $flowUrl('2') : url('avaluos/'.$record['id'].'/bien-sujeto?'.http_build_query(['section'=>$check['destination'],'unit'=>$componentKey,'from'=>'metodologia']).'#unidades-capitulo-3')) ?>"><?= $ok?'Consultar':'Diligenciar / corregir' ?> en <?= e($check['source']) ?></a>
        </article>
        <?php endforeach; ?>
    </div>
    <a class="btn-secondary mt-4" href="<?= e($flowUrl('1'). '&consult_method=costo') ?>">Actualizar consulta C1</a>
</section>
<section class="mt-5 rounded-2xl border bg-teal-50 p-5 sm:p-8">
    <h2 class="text-xl font-semibold">Academia del método del costo</h2>
    <div class="mt-4 space-y-3">
        <?php foreach (CostMethodAcademy::topics() as $topic): ?>
        <details class="rounded-xl border bg-white p-4"><summary class="min-h-11 cursor-pointer font-semibold"><?= e($topic['title']) ?></summary>
            <ul class="list-disc space-y-2 pl-5 text-sm leading-6"><?php foreach ($topic['items'] as $bullet): ?><li><?= e($bullet) ?></li><?php endforeach; ?></ul>
        </details>
        <?php endforeach; ?>
    </div>
    <p class="mt-4 text-sm">Fuente del editor: <a class="font-semibold text-blue-800 underline" href="https://www.sispac.com.co/empresa" target="_blank" rel="noopener">SISPAC</a>. El contenido trasladado se revisa con sus soportes; un modelo estimado no se presenta como dato publicado.</p>
    <details class="mt-4 rounded-xl border bg-white p-4"><summary class="min-h-11 cursor-pointer font-semibold">Consultar artículos 27–30 de la Resolución 941</summary>
        <?php foreach ([27,28,29,30] as $readingNumber): require __DIR__.'/valuation-methodology-article-reading.php'; endforeach; ?>
    </details>
    <a class="btn-primary mt-4" href="<?= e($flowUrl('2')) ?>">Definir método y alcance de esta unidad</a>
    <p class="mt-3 text-sm">C3 reunirá insumos, C4 desarrollará cálculos y C5 presentará la memoria. Esta entrega prepara C1 y C2.</p>
</section>
