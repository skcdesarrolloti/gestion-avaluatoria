<details class="mt-3 rounded-lg border border-teal-200 bg-teal-50 px-3">
    <summary class="flex min-h-11 cursor-pointer items-center gap-2 font-semibold text-teal-900"><span aria-hidden="true">(?)</span> Ayuda sobre esta pregunta</summary>
    <p class="mt-2 text-sm leading-6"><strong>Por qué se pregunta:</strong> <?= e($question['why']) ?></p>
    <p class="mt-2 text-sm leading-6"><strong>Ejemplo:</strong> <?= e($question['example']) ?></p>
    <details class="my-3 rounded-lg bg-white px-3">
        <summary class="min-h-11 cursor-pointer py-3 text-sm font-semibold">Ver soporte normativo</summary>
        <ul class="list-disc space-y-2 pl-5 text-sm"><?php foreach ($question['references'] as $reference): ?><li><?= e($reference) ?></li><?php endforeach; ?></ul>
        <p class="mt-3 text-sm">Consulta los apartados citados en las bibliotecas existentes:</p>
        <nav class="mt-2 flex flex-wrap gap-3 pb-3 text-sm" aria-label="Soportes de <?= e($question['label']) ?>">
            <a class="inline-flex min-h-11 items-center font-semibold text-blue-800 underline" href="<?= e(url('igac/documento/resolucion-igac-941-2026')) ?>" target="_blank" rel="noopener">Resolución IGAC 941</a>
            <a class="inline-flex min-h-11 items-center font-semibold text-blue-800 underline" href="<?= e(url('normas-internacionales-valuacion?grupo=400')) ?>" target="_blank" rel="noopener">IVS 400 · edición 2025</a>
            <?php if ($name==='scope'): ?>
            <a class="inline-flex min-h-11 items-center font-semibold text-blue-800 underline" href="<?= e(url('normas-internacionales-valuacion?grupo=G')) ?>" target="_blank" rel="noopener">IVS 101</a>
            <a class="inline-flex min-h-11 items-center font-semibold text-blue-800 underline" href="<?= e(url('normas-tecnicas-sectoriales/nts-s03-contenido-informe-valuacion')) ?>" target="_blank" rel="noopener">NTS S03</a>
            <?php elseif ($name==='support'): ?>
            <a class="inline-flex min-h-11 items-center font-semibold text-blue-800 underline" href="<?= e(url('normas-tecnicas-sectoriales/gts-e02-valuacion-arrendamiento')) ?>" target="_blank" rel="noopener">GTS E02</a>
            <a class="inline-flex min-h-11 items-center font-semibold text-blue-800 underline" href="<?= e(url('normas-tecnicas-sectoriales/nts-m01-metodologias-valuacion')) ?>" target="_blank" rel="noopener">NTS M01</a>
            <?php endif; ?>
        </nav>
    </details>
</details>
