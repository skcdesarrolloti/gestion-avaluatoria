<?php $tip = static fn (string $text): string => '<span class="help-dot" title="' . e($text) . '">?</span>'; ?>
<div class="grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
    <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
        <p class="text-xs font-bold uppercase text-blue-800">Filtros iniciales <?= $tip('Son los filtros mínimos para salir a buscar: operación, ciudad, barrio o microsector, tipología y rango físico. Se toman del expediente y del bien sujeto.') ?></p>
        <?php if ($portalFilters === []): ?>
            <p class="mt-3 text-sm text-blue-950">Completa el expediente y el bien sujeto para formar los filtros de portal.</p>
        <?php else: ?>
            <dl class="mt-3 grid gap-2 text-sm">
                <?php foreach ($portalFilters as $filter): ?>
                    <div class="rounded-lg bg-white/80 p-3">
                        <dt class="text-xs font-semibold uppercase text-blue-700"><?= e((string) ($filter['label'] ?? 'Filtro')) ?></dt>
                        <dd class="mt-1 font-semibold text-slate-950"><?= e((string) ($filter['value'] ?? '')) ?></dd>
                    </div>
                <?php endforeach; ?>
            </dl>
        <?php endif; ?>
    </div>
    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
        <p class="text-xs font-bold uppercase text-slate-500">Campos que conviene capturar de cada portal <?= $tip('Úsalos como checklist al leer cada aviso. Si falta precio, área, fuente o ubicación mínima, probablemente no sirve como muestra verificable.') ?></p>
        <div class="mt-3 grid gap-2 sm:grid-cols-2">
            <?php foreach ($portalFields as $field): ?>
                <span class="rounded-lg bg-white px-3 py-2 text-sm font-medium text-slate-700"><?= e($field) ?></span>
            <?php endforeach; ?>
        </div>
    </div>
</div>
