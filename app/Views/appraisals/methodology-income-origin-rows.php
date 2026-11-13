<dl class="mt-3 grid gap-3 sm:grid-cols-2">
<?php foreach ($incomeOriginRows as $originRow): ?>
    <div class="min-w-0 rounded-lg bg-slate-50 p-3">
        <dt class="text-sm font-semibold"><?= e($originRow['label']) ?></dt>
        <dd class="mt-1 whitespace-pre-wrap break-words"><?= e(trim((string)$originRow['value'])!==''?$originRow['value']:'Por completar en el origen') ?></dd>
        <dd class="mt-2 text-xs text-slate-600"><?= e($originRow['source']) ?></dd>
        <?php if (!empty($originRow['support'])): ?><dd class="mt-1 whitespace-pre-wrap break-words text-sm">Soporte: <?= e($originRow['support']) ?></dd><?php endif; ?>
        <?php if (isset($originRow['url'])): ?><dd><a class="inline-flex min-h-11 items-center text-sm font-semibold text-blue-800 underline" href="<?= e(url($originRow['url'])) ?>"><?= trim((string)$originRow['value'])!==''?'Consultar o corregir en origen':'Completar en origen' ?></a></dd><?php endif; ?>
    </div>
<?php endforeach; ?>
</dl>
