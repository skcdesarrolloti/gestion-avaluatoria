<?php $commonReadings = \App\Services\Resolution941CommonReading::articles(); ?>
<section class="mt-4 rounded-xl border bg-white p-4" x-data="{ commonArticle: '1' }">
    <label class="block font-semibold">Artículo transversal que deseas consultar
        <select class="input mt-2" x-model="commonArticle">
            <option value="" disabled>Selecciona un artículo</option>
            <?php foreach ($commonReadings as $number => [$commonTitle, $commonSummary]): ?><option value="<?= $number ?>"><?= e($number . ' · ' . $commonTitle) ?></option><?php endforeach; ?>
        </select>
    </label>
    <?php foreach ($commonReadings as $number => [$commonTitle, $commonSummary]): ?>
    <div x-show="commonArticle === '<?= $number ?>'" <?= $number !== 1 ? 'x-cloak' : '' ?>>
        <h5 class="mt-4 font-semibold"><?= e('Artículo ' . $number . ' · ' . $commonTitle) ?></h5>
        <p class="mt-2 text-sm"><?= e($commonSummary) ?></p>
        <?php $readingNumber = $number; require __DIR__ . '/valuation-methodology-article-reading.php'; ?>
    </div>
    <?php endforeach; ?>
</section>
