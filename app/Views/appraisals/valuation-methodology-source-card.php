<a class="rounded-lg border border-blue-100 bg-white p-3 text-sm leading-5 hover:border-blue-300"
    target="_blank" rel="noopener" href="<?= e((string) ($source['url'] ?? '#')) ?>">
    <span class="block text-xs font-bold uppercase text-blue-700"><?= e((string) ($source['kind'] ?? 'Fuente')) ?></span>
    <span class="mt-1 block font-semibold text-slate-950"><?= e((string) ($source['label'] ?? 'Fuente')) ?></span>
    <?php if (!empty($source['category'])): ?>
        <span class="mt-1 inline-flex rounded-full bg-blue-50 px-2 py-1 text-[11px] font-bold uppercase text-blue-800">
            <?= e((string) $source['category']) ?>
        </span>
    <?php endif; ?>
    <span class="mt-1 block text-xs text-slate-600"><?= e((string) ($source['instruction'] ?? '')) ?></span>
    <?php if (!empty($source['query'])): ?>
        <span class="mt-2 block rounded bg-slate-50 p-2 font-mono text-[11px] text-slate-700">
            <?= e((string) $source['query']) ?>
        </span>
    <?php endif; ?>
    <?php if (!empty($source['selection_factors']) && is_array($source['selection_factors'])): ?>
        <span class="mt-3 block text-xs font-bold uppercase text-slate-500">Factores de selección</span>
        <span class="mt-2 block space-y-1 text-xs leading-5 text-slate-700">
            <?php foreach ($source['selection_factors'] as $factor): ?>
                <span class="block">- <?= e((string) $factor) ?></span>
            <?php endforeach; ?>
        </span>
    <?php endif; ?>
    <span class="mt-2 block text-xs font-semibold text-blue-800">Abrir en otra pestaña y traer solo muestras verificables.</span>
</a>
