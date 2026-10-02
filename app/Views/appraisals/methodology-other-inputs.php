<?php $inputGuide = (new \App\Services\AppraisalMethodologyResolution941Guide())->inputsForMethod($method); ?>
<section class="rounded-xl border bg-white p-6">
    <h2 class="text-xl font-semibold"><?= e($prefix) ?>3 · Insumos de <?= e($methods[$method]) ?> · <?= e($componentLabel) ?></h2>
    <p class="mt-3">Reúne y verifica los siguientes soportes de esta unidad antes de desarrollar el análisis.</p>
    <ul class="mt-3 list-disc space-y-2 pl-5"><?php foreach ($inputGuide['items'] as $input): ?><li><?= e($input) ?></li><?php endforeach; ?></ul>
    <p class="mt-4 rounded-lg bg-amber-50 p-3">La captura técnica y el cálculo de este método todavía están pendientes de desarrollo. Esta lista prepara los insumos; no produce valores ni reemplaza un presupuesto o un estudio de factibilidad.</p>
    <a class="btn-secondary mt-4" href="<?= e($flowUrl('1')) ?>">Volver a artículos y recomendaciones</a>
</section>
