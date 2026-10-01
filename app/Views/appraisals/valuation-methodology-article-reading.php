<?php $articleReading = \App\Services\Resolution941Reading::article($readingNumber); ?>
<?php if ($articleReading !== null): ?>
    <details class="mt-3 border-t border-indigo-100 pt-1">
        <summary class="min-h-11 cursor-pointer py-3 font-semibold text-indigo-800 focus-visible:outline-2 focus-visible:outline-indigo-700">
            Leer artículo <?= e((string) $readingNumber) ?> completo
        </summary>
        <div class="max-h-96 overflow-y-auto rounded-lg bg-slate-50 p-3 text-sm leading-6 text-slate-800"
            tabindex="0" role="region" aria-label="Texto completo del artículo <?= e((string) $readingNumber) ?>">
            <?php foreach ($articleReading['paragraphs'] as $paragraph): ?>
                <p class="mb-3 break-words"><?= e($paragraph) ?></p>
            <?php endforeach; ?>
        </div>
        <p class="mt-2 text-xs leading-5 text-slate-600">Resolución IGAC 941 de 2026 · páginas <?= e($articleReading['pages']) ?>.
            Transcripción para consulta; se conserva el contenido y se adapta el formato. Cierra la flecha para ocultarlo.</p>
        <a href="<?= e($articleReading['url']) ?>" target="_blank" rel="noopener" data-no-fetch
            class="inline-flex min-h-11 items-center text-sm font-semibold text-indigo-800 underline">Ver original del IGAC</a>
    </details>
<?php endif; ?>
