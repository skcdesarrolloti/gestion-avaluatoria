<?php $document = $document ?? []; ?>
<section class="space-y-6">
    <a class="text-sm font-semibold text-teal-800 hover:text-teal-900" href="<?= e(url('igac')) ?>">← Biblioteca IGAC</a>
    <article class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="eyebrow"><?= e((string) ($document['tipo_documento'] ?? 'Documento')) ?></p>
                <h1 class="mt-2 text-3xl font-semibold text-slate-950"><?= e((string) ($document['codigo'] ?? '')) ?></h1>
                <p class="mt-3 max-w-3xl text-lg text-slate-700"><?= e((string) ($document['nombre'] ?? '')) ?></p>
            </div>
            <span class="rounded-full bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700"><?= e((string) ($document['estado'] ?? '')) ?></span>
        </div>
        <p class="mt-5 max-w-4xl text-sm leading-6 text-slate-600"><?= e((string) ($document['descripcion_corta'] ?? '')) ?></p>
        <dl class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-lg bg-slate-50 p-4"><dt class="text-xs font-semibold uppercase text-slate-500">Código</dt><dd class="mt-1 text-sm font-semibold"><?= e((string) ($document['codigo'] ?? '')) ?></dd></div>
            <div class="rounded-lg bg-slate-50 p-4"><dt class="text-xs font-semibold uppercase text-slate-500">Versión</dt><dd class="mt-1 text-sm font-semibold"><?= e((string) ($document['version'] ?? '')) ?></dd></div>
            <div class="rounded-lg bg-slate-50 p-4"><dt class="text-xs font-semibold uppercase text-slate-500">Vigencia</dt><dd class="mt-1 text-sm font-semibold"><?= e((string) ($document['fecha_vigencia'] ?? '')) ?></dd></div>
            <div class="rounded-lg bg-slate-50 p-4"><dt class="text-xs font-semibold uppercase text-slate-500">Tipo</dt><dd class="mt-1 text-sm font-semibold"><?= e((string) ($document['tipo_documento'] ?? '')) ?></dd></div>
        </dl>
        <div class="mt-6 grid gap-5 lg:grid-cols-2">
            <section class="rounded-lg border border-slate-200 p-4">
                <h2 class="text-sm font-semibold uppercase text-slate-500">Temas relacionados</h2>
                <div class="mt-3 flex flex-wrap gap-2">
                    <?php foreach (($document['temas_relacionados'] ?? []) as $topic): ?>
                        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700"><?= e((string) $topic) ?></span>
                    <?php endforeach; ?>
                </div>
            </section>
            <section class="rounded-lg border border-slate-200 p-4">
                <h2 class="text-sm font-semibold uppercase text-slate-500">Módulos que lo utilizan</h2>
                <div class="mt-3 flex flex-wrap gap-2">
                    <?php foreach (($document['modulos_que_lo_utilizan'] ?? []) as $module): ?>
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700"><?= e((string) $module) ?></span>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
        <div class="mt-6 flex flex-wrap gap-3">
            <?php if ((string) ($document['fuente_oficial'] ?? '') !== ''): ?>
                <a class="btn-primary" href="<?= e((string) $document['fuente_oficial']) ?>" target="_blank" rel="noopener" data-no-fetch>Ver fuente oficial</a>
            <?php endif; ?>
            <?php if ((string) ($document['archivo'] ?? '') !== ''): ?>
                <a class="btn-secondary" href="<?= e((string) $document['archivo']) ?>" target="_blank" rel="noopener" data-no-fetch>Visualizar / descargar archivo</a>
            <?php endif; ?>
        </div>
    </article>
</section>
