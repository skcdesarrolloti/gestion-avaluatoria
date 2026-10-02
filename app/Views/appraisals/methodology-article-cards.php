<div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
    <?php foreach ($academyCards as $article): ?>
    <section class="rounded-xl border bg-white p-4 text-sm leading-6">
        <p class="font-semibold text-indigo-700"><?= e($article['number']) ?></p>
        <h5 class="mt-1 font-semibold"><?= e($article['title']) ?></h5>
        <p class="mt-2"><?= e($article['summary']) ?></p>
        <?php $readingNumber = (int) preg_replace('/\D/', '', $article['number']); require __DIR__ . '/valuation-methodology-article-reading.php'; ?>
        <div class="mt-3 flex flex-wrap gap-2"><?php foreach ($article['highlights'] as $highlight): ?><mark class="rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold"><?= e($highlight) ?></mark><?php endforeach; ?></div>
    </section>
    <?php endforeach; ?>
</div>
