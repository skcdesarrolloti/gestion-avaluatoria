<?php $globalStage = in_array($stage, ['components','decision','integration','report'], true); ?>
<section class="mt-6 rounded-xl border bg-white p-4" aria-label="Camino del capítulo 8">
    <p class="font-semibold">Camino del expediente: A. Inmuebles → B. Matriz y método → C. Insumos y comparables → D. Análisis de las muestras → E. Integración</p>
    <details class="mt-2">
        <summary class="min-h-11 cursor-pointer font-semibold text-teal-800" title="Explicación del recorrido">? ¿Cómo se relacionan estos pasos con M1–M5?</summary>
        <p class="text-sm leading-6">A muestra los inmuebles y anexos. En B consultas la matriz y documentas el método de cada componente. C abre sus insumos: para Mercado, portales, inmobiliarias, pegado, matriz y mapas. D lleva al análisis de esas mismas muestras. Repite C y D para cada componente; E integra sus resultados. M1–M5 conservan la academia, selección, insumos, análisis y entregable del método.</p>
    </details>
    <nav class="mt-2 flex flex-wrap gap-2" aria-label="Organización del capítulo 8">
    <?php foreach (['components'=>'A · Inmuebles y anexos','decision'=>'B · Matriz y método','3'=>'C · Insumos y comparables','4'=>'D · Análisis de las muestras','integration'=>'E · Integración del avalúo','report'=>'Texto del numeral 8'] as $step => $label): ?>
        <a class="btn-secondary" <?= $stage === (string) $step || ($step === 'decision' && in_array($stage, ['1','2'], true)) || ((string) $step === '4' && $stage === '5') ? 'aria-current="page"' : '' ?> href="<?= e($flowUrl((string) $step)) ?>"><?= e($label) ?></a>
    <?php endforeach; ?>
    </nav>
    <?php if (!$globalStage): ?><p class="mt-3 font-semibold"><?= $componentKey === '' ? 'Banco de muestras: asigna cada muestra a su inmueble antes de analizar.' : 'Trabajo de ' . e($componentLabel) ?></p><?php endif; ?>
    <details class="mt-3"><summary class="min-h-11 cursor-pointer text-teal-800">Herramienta de apoyo · muestras anteriores</summary>
        <p class="mb-3 text-sm">Este banco conserva las muestras anteriores. Asígnalas a su inmueble para utilizarlas en M3; no es otra etapa del análisis.</p>
        <a class="btn-secondary" href="<?= e($flowUrl('3', 'mercado', '')) ?>">Muestras sin asignar (<?= count(\App\Services\MethodologyComparableScope::rows($allComparableRows, '')) ?>)</a>
    </details>
</section>
