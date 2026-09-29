<a class="rounded-lg border border-blue-100 bg-white p-3 text-sm leading-5 hover:border-blue-300"
    target="_blank" rel="noopener" href="<?= e((string) ($source['url'] ?? '#')) ?>">
    <span class="block text-xs font-bold uppercase text-blue-700"><?= e((string) ($source['kind'] ?? 'Fuente')) ?></span>
    <span class="mt-1 block font-semibold text-slate-950"><?= e((string) ($source['label'] ?? 'Fuente')) ?></span>
    <span class="mt-1 block text-xs text-slate-600"><?= e((string) ($source['instruction'] ?? '')) ?></span>
    <?php if (!empty($source['query'])): ?>
        <span class="mt-2 block rounded bg-slate-50 p-2 font-mono text-[11px] text-slate-700">
            <?= e((string) $source['query']) ?>
        </span>
    <?php endif; ?>
</a>
