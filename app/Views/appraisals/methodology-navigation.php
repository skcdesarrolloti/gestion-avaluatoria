<section class="mt-5 rounded-xl border bg-white p-4" aria-label="Camino del capítulo 8">
    <p class="font-semibold">Camino del expediente: unidad → método registrado → academia → insumos → análisis → entregable.</p>
    <nav class="mt-3 flex flex-wrap gap-2" aria-label="Organización del capítulo 8">
        <a class="btn-secondary" href="<?= e($flowUrl('components')) ?>">Unidades y su recorrido</a>
        <a class="btn-secondary" href="<?= e($flowUrl('integration')) ?>">Integración del avalúo</a>
        <a class="btn-secondary" href="<?= e($flowUrl('report')) ?>">Texto del numeral 8</a>
    </nav>
    <details class="mt-3"><summary class="min-h-11 cursor-pointer text-teal-800">Herramientas del expediente · matriz y muestras anteriores</summary>
        <p class="mb-3 text-sm">La matriz técnica sigue disponible. El banco conserva las muestras anteriores para asignarlas expresamente a una unidad.</p>
        <a class="btn-secondary" href="<?= e($flowUrl('decision')) ?>">Matriz y método · consulta general</a>
        <?php if ($componentKey!==''): ?><a class="btn-secondary" href="<?= e($flowUrl('1','costo'). '&consult_method=costo') ?>">Consultar C1 · Academia del costo</a><?php endif; ?>
        <a class="btn-secondary" href="<?= e($flowUrl('3', 'mercado', '')) ?>">Muestras sin asignar (<?= count(\App\Services\MethodologyComparableScope::rows($allComparableRows, '')) ?>)</a>
    </details>
</section>
