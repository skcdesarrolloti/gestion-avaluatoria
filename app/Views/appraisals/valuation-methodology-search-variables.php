<?php if ($factorGroups === []): ?>
    <p class="rounded-xl border border-dashed border-slate-300 p-4 text-sm text-slate-600">
        Completa la tipología del bien sujeto para proponer variables comparables.
    </p>
<?php else: ?>
    <div>
        <p class="eyebrow">Matriz por tipología</p>
        <h3 class="mt-2 text-2xl font-semibold">Características que deben parecerse</h3>
        <div class="mt-4 grid gap-3 lg:grid-cols-4">
            <?php foreach ($factorGroups as $group => $items): ?>
                <article class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase text-slate-500"><?= e($group) ?></p>
                    <ul class="mt-2 space-y-1 text-sm leading-5 text-slate-700">
                        <?php foreach ($items as $item): ?>
                            <li>- <?= e($item) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
