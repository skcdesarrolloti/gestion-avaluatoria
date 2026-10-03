<?php $costTopics=\App\Services\CostMethodAcademy::topics(); ?>
<section class="mb-5 rounded-2xl border bg-teal-50 p-5 sm:p-8" aria-labelledby="costo-academia-titulo">
    <h2 id="costo-academia-titulo" class="text-2xl font-semibold">Academia C1 · Método del costo</h2>
    <p class="mt-3 text-sm leading-6">Lectura por unidad. Aprende a distinguir costo a nuevo, depreciación, vida de referencia, vida remanente y vida prolongada antes de adoptar parámetros.</p>
    <p class="mt-3 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm"><strong>Los 100 años siguen como referencia para ciertas construcciones permanentes.</strong> No son una vida universal. La vida remanente y la prolongada requieren verificar sus condiciones; no se asignan automáticamente.</p>
    <p class="mt-3 text-sm">Elige un tema para ubicarlo y abre su flecha para leerlo.</p>
    <nav class="mt-4 flex flex-wrap gap-2" aria-label="Temas de academia C1">
        <?php foreach ($costTopics as $topic): ?><a class="inline-flex min-h-11 items-center rounded-lg border bg-white px-3 py-2 text-sm font-semibold text-blue-800 underline" href="#costo-academia-<?= e($topic['id']) ?>"><?= e($topic['title']) ?></a><?php endforeach; ?>
    </nav>
    <div class="mt-5 space-y-3">
        <?php foreach ($costTopics as $topic): ?>
        <details id="costo-academia-<?= e($topic['id']) ?>" class="scroll-mt-5 rounded-xl border bg-white p-4">
            <summary class="min-h-11 cursor-pointer font-semibold focus-visible:outline-2 focus-visible:outline-blue-700"><?= e($topic['title']) ?></summary>
            <p class="mt-2 text-xs font-semibold text-teal-800"><?= e($topic['reference']) ?></p>
            <ul class="mt-3 list-disc space-y-3 pl-5 text-sm leading-6"><?php foreach ($topic['items'] as $bullet): ?><li><?= e($bullet) ?></li><?php endforeach; ?></ul>
            <?php if (isset($topic['rows'])): ?>
            <div class="mt-4 overflow-x-auto rounded-lg border" tabindex="0" role="region" aria-label="Tabla de <?= e($topic['title']) ?>">
                <table class="w-full text-left text-sm">
                    <caption class="px-3 py-2 text-left text-xs">Síntesis académica: consulta la tabla original y sus condiciones antes de adoptar parámetros.</caption>
                    <thead class="bg-teal-50"><tr><?php foreach ($topic['headers'] as $heading): ?><th scope="col" class="px-3 py-3 font-semibold"><?= e($heading) ?></th><?php endforeach; ?></tr></thead>
                    <tbody><?php foreach ($topic['rows'] as $row): ?><tr class="border-t"><?php foreach ($row as $index=>$cell): ?><?php if ($index===0): ?><th scope="row" class="px-3 py-3 font-semibold"><?= e($cell) ?></th><?php else: ?><td class="px-3 py-3"><?= e($cell) ?></td><?php endif; ?><?php endforeach; ?></tr><?php endforeach; ?></tbody>
                </table>
            </div>
            <?php endif; ?>
            <a class="mt-3 inline-flex min-h-11 items-center text-sm font-semibold text-blue-800 underline" href="<?= e(isset($topic['page']) ? \App\Services\CostMethodAcademy::ANNEX_URL.'#page='.$topic['page'] : \App\Services\CostMethodAcademy::RESOLUTION_URL) ?>" target="_blank" rel="noopener" data-no-fetch>Consultar fuente de este apartado</a>
        </details>
        <?php endforeach; ?>
    </div>
    <details class="mt-4 rounded-xl border bg-white p-4"><summary class="min-h-11 cursor-pointer font-semibold">Texto completo · Método del costo · Artículos 27–30 de la Resolución 941</summary>
        <?php foreach ([27,28,29,30] as $readingNumber): require __DIR__.'/valuation-methodology-article-reading.php'; endforeach; ?>
    </details>
    <?php require __DIR__.'/methodology-cost-academy-full-reading.php'; ?>
    <p class="mt-3 text-xs leading-5">Referencia principal: Resolución IGAC 941 de 2026 y anexo técnico 2.3, tablas 2–7. Ejemplos didácticos sin adopción en el expediente. Fuentes editoriales: <a class="font-semibold text-blue-800 underline" href="https://www.sispac.com.co/empresa" target="_blank" rel="noopener" data-no-fetch>SISPAC</a>.</p>
</section>

