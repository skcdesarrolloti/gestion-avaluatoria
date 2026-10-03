<?php [$stepReadingTitle,$stepReadingNumbers]=\App\Services\MethodologyStepArticles::forStep($stage,$method,($record['regimen_ph'] ?? '')==='si'); ?>
<details class="mt-5 rounded-xl border bg-white p-4" id="articulos-del-paso">
    <summary class="min-h-11 cursor-pointer font-semibold">Resolución 941 · Artículos completos para este paso: <?= e($stepReadingTitle) ?></summary>
    <p class="mt-2 text-sm leading-6">Abre el artículo que necesitas consultar y ciérralo al terminar. Esta selección orienta el paso actual; el analista verifica su aplicación al encargo y a los derechos valorados.</p>
    <?php foreach ($stepReadingNumbers as $readingNumber): require __DIR__.'/valuation-methodology-article-reading.php'; endforeach; ?>
</details>
