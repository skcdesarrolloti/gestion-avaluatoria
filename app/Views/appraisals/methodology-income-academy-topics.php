<?php $incomeAcademySources=\App\Services\IncomeAcademySources::all(); ?>
<div class="mt-5">
    <h4 class="text-xl font-semibold">Aprender Renta con IGAC, IVS y normas sectoriales</h4>
    <p class="mt-2 text-sm leading-6">Abre un tema a la vez. Encontrarás qué significa, un ejemplo y qué reunir antes de diligenciar. Las referencias explican el fundamento de cada tema.</p>
    <details class="mt-3 rounded-lg bg-teal-50 p-3">
        <summary class="min-h-11 cursor-pointer font-semibold">(?) Cómo leer y aplicar estos soportes</summary>
        <p class="mt-2 text-sm leading-6">IGAC corresponde al marco colombiano según el ámbito del encargo. IVS aporta referencia internacional y NTS/GTS, soporte sectorial; registra cuándo corresponde aplicar cada una. Se presentan síntesis de fragmentos pertinentes, no citas literales ni normas completas.</p>
        <p class="mt-2 text-sm leading-6">Se identifican las ediciones estudiadas en las bibliotecas del aplicativo. La traducción IVS 2025 debe contrastarse con el original oficial en inglés antes de declarar conformidad. Consultar estos temas no acredita cumplimiento ni verifica documentos.</p>
    </details>
    <?php foreach (\App\Services\IncomeAcademy::topics() as $topicIndex=>$incomeTopic): ?>
    <details id="renta-tema-<?= e($incomeTopic['key']) ?>" class="mt-4 rounded-xl border p-4 sm:p-5">
        <summary class="min-h-11 cursor-pointer text-lg font-semibold text-teal-900"><?= $topicIndex+1 ?>. <?= e($incomeTopic['title']) ?></summary>
        <p class="mt-3 leading-6"><?= e($incomeTopic['explanation']) ?></p>
        <div class="mt-3 rounded-lg bg-teal-50 p-4 text-sm leading-6"><strong>Ejemplo para entenderlo:</strong> <?= e($incomeTopic['example']) ?></div>
        <h5 class="mt-4 font-semibold">Qué debes reunir o aclarar</h5>
        <ul class="mt-2 list-disc space-y-2 pl-5 text-sm leading-6"><?php foreach ($incomeTopic['collect'] as $item): ?><li><?= e($item) ?></li><?php endforeach; ?></ul>
        <p class="mt-4 text-sm leading-6"><strong>Cómo se conecta con Insumos:</strong> <?= e($incomeTopic['connection']) ?></p>
        <details class="mt-4 rounded-lg border bg-slate-50 p-3 sm:p-4">
            <summary class="min-h-11 cursor-pointer font-semibold">Soportes de este tema · IGAC, IVS y NTS/GTS</summary>
            <p class="mt-2 text-sm text-slate-600">Síntesis del fragmento pertinente y su utilidad. Abre el documento existente para leer el apartado citado.</p>
            <?php foreach ($incomeTopic['sources'] as $incomeSourceKey): $incomeSource=$incomeAcademySources[$incomeSourceKey]; ?>
            <article class="mt-3 rounded-lg border bg-white p-4">
                <p class="eyebrow"><?= e($incomeSource['family']) ?> · Edición <?= e($incomeSource['edition']) ?></p>
                <h6 class="mt-2 font-semibold"><?= e($incomeSource['name']) ?></h6>
                <p class="mt-1 text-sm font-semibold text-teal-900"><?= e($incomeSource['section']) ?></p>
                <p class="mt-3 text-sm leading-6"><strong>Lectura del apartado, en síntesis:</strong> <?= e($incomeSource['fragment']) ?></p>
                <p class="mt-2 text-sm leading-6"><strong>Para tu trabajo:</strong> <?= e($incomeSource['use']) ?></p>
                <a class="mt-2 inline-flex min-h-11 items-center font-semibold text-blue-800 underline" href="<?= e(url($incomeSource['url'])) ?>" target="_blank" rel="noopener">Consultar documento en el aplicativo</a>
                <?php if (isset($incomeSource['extra_url'])): ?><a class="mt-2 inline-flex min-h-11 items-center font-semibold text-blue-800 underline" href="<?= e(url($incomeSource['extra_url'])) ?>" target="_blank" rel="noopener"><?= e($incomeSource['extra_label']) ?></a><?php endif; ?>
            </article>
            <?php endforeach; ?>
        </details>
    </details>
    <?php endforeach; ?>
</div>
