<?php $globalStage = in_array($stage, ['components','decision','integration','report'], true); ?>
<section class="mt-6 rounded-xl border bg-white p-4" aria-label="Camino del capítulo 8">
    <p class="font-semibold">Camino del expediente: A. Revisar inmuebles → B. Orientar el método → C. Analizar cada inmueble → D. Integrar</p>
    <details class="mt-2">
        <summary class="min-h-11 cursor-pointer font-semibold text-teal-800" title="Explicación del recorrido">? ¿Cómo se relacionan estos pasos con M1–M5?</summary>
        <p class="text-sm leading-6">A y B corresponden al expediente completo. En C eliges un inmueble o anexo y recorres su método: academia, selección, insumos, análisis y entregable. Repite C para cada componente. En D reúnes sus resultados y revisas el texto del numeral 8. La matriz orienta la decisión; el método se guarda en M2, C2, R2 o Re2.</p>
    </details>
    <nav class="mt-2 flex flex-wrap gap-2" aria-label="Organización del capítulo 8">
    <?php foreach (['components'=>'A · Inmuebles y anexos','decision'=>'B · Matriz y método','integration'=>'D · Integración del avalúo','report'=>'Texto del numeral 8'] as $step => $label): ?>
        <a class="btn-secondary" <?= $stage === $step ? 'aria-current="page"' : '' ?> href="<?= e($flowUrl($step)) ?>"><?= e($label) ?></a>
        <?php if ($step === 'decision'): ?><a class="btn-secondary" <?= !$globalStage ? 'aria-current="page"' : '' ?> href="<?= e($flowUrl('decision') . '#elegir-componente') ?>">C · Analizar inmueble</a><?php endif; ?>
    <?php endforeach; ?>
    </nav>
    <a class="btn-secondary mt-3" href="<?= e($flowUrl('3', 'mercado', $componentKey)) ?>">Portales y pegado de comparables → M3</a>
    <?php if (!$globalStage): ?><p class="mt-3 font-semibold">C · <?= $componentKey === '' ? 'Asignar muestras a un inmueble' : 'Trabajo de ' . e($componentLabel) ?></p><?php endif; ?>
    <details class="mt-3"><summary class="min-h-11 cursor-pointer text-teal-800">Herramienta de apoyo · muestras anteriores</summary>
        <p class="mb-3 text-sm">Este banco conserva las muestras anteriores. Asígnalas a su inmueble para utilizarlas en M3; no es otra etapa del análisis.</p>
        <a class="btn-secondary" href="<?= e($flowUrl('3', 'mercado', '')) ?>">Muestras sin asignar (<?= count(\App\Services\MethodologyComparableScope::rows($allComparableRows, '')) ?>)</a>
    </details>
</section>
