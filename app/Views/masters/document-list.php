<?php if (!$documents) return; ?>
<div class="mt-6 grid gap-4 md:grid-cols-2">
    <?php foreach ($documents as $document): ?>
        <?php
        $destination = $documentDestinations[(string) $document['destination']] ?? (string) $document['destination'];
        $modules = array_map(static fn ($code): string => $documentModules[(string) $code] ?? (string) $code, $document['modules']);
        ?>
        <article class="rounded-xl border border-slate-200 p-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-teal-800"><?= e($document['document_code']) ?></p>
                    <h3 class="mt-1 text-lg font-semibold"><?= e($document['title']) ?></h3>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700"><?= e($document['status']) ?></span>
            </div>
            <p class="mt-3 text-sm leading-6 text-slate-600"><?= e($document['summary'] ?: 'Sin descripción de utilidad registrada.') ?></p>
            <div class="mt-3 flex flex-wrap gap-2 text-xs">
                <span class="rounded-full bg-blue-50 px-3 py-1 font-semibold text-blue-800"><?= e($destination) ?></span>
                <?php foreach (array_slice($document['topics'], 0, 5) as $topic): ?>
                    <span class="rounded-full bg-slate-100 px-3 py-1 font-semibold text-slate-700"><?= e($topic) ?></span>
                <?php endforeach; ?>
            </div>
            <?php if ($modules): ?>
                <p class="mt-3 text-xs leading-5 text-slate-500"><strong>Módulos:</strong> <?= e(implode(', ', $modules)) ?></p>
            <?php endif; ?>
            <a class="btn-secondary mt-4" target="_blank" rel="noopener"
                href="<?= e(url('maestros/documentos/' . $document['id'] . '/archivo')) ?>">Abrir PDF</a>
        </article>
    <?php endforeach; ?>
</div>
