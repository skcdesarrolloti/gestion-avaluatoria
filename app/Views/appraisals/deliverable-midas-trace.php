<?php
$trace = is_array($midasTrace ?? null) ? $midasTrace : ['rows' => [], 'active' => [], 'text' => ''];
$rows = is_array($trace['rows'] ?? null) ? $trace['rows'] : [];
$active = is_array($trace['active'] ?? null) ? $trace['active'] : [];
?>
<section class="mt-8 rounded-2xl border border-emerald-100 bg-emerald-50 p-6 shadow-sm sm:p-8">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Biblioteca MIDAS</p>
            <h2 class="mt-2 text-2xl font-semibold text-emerald-950">Trazabilidad incorporada al entregable</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-emerald-900">
                Los documentos comunes cargados en MIDAS quedan referenciados aquí para dejar constancia de
                qué capítulo alimentan. La conclusión técnica sigue siendo editable en cada módulo.
            </p>
        </div>
        <span class="rounded-full bg-white px-4 py-2 text-sm font-bold text-emerald-800">
            <?= e((string) count($active)) ?> grupo(s) con soporte
        </span>
    </div>
    <textarea class="input mt-5 min-h-40 bg-white font-mono text-sm leading-6" rows="8" readonly><?= e((string) ($trace['text'] ?? '')) ?></textarea>
    <div class="mt-5 grid gap-4 lg:grid-cols-2">
        <?php foreach ($rows as $row): ?>
            <?php $loaded = (int) ($row['count'] ?? 0) > 0; ?>
            <article class="rounded-xl border border-white/80 bg-white/80 p-4 text-sm leading-6">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <h3 class="font-semibold text-slate-950"><?= e((string) ($row['group'] ?? 'MIDAS')) ?></h3>
                    <span class="rounded-full px-3 py-1 text-xs font-semibold <?= $loaded ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>">
                        <?= e((string) ($row['count'] ?? 0)) ?> documento(s)
                    </span>
                </div>
                <p class="mt-2 text-slate-700"><?= e((string) ($row['target'] ?? '')) ?></p>
                <p class="mt-2 text-xs font-semibold text-blue-800"><?= e((string) ($row['note'] ?? '')) ?></p>
                <?php if (!empty($row['samples'])): ?>
                    <p class="text-anywhere mt-2 text-xs text-slate-500">
                        Ejemplos: <?= e(implode('; ', array_map('strval', (array) $row['samples']))) ?>
                    </p>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
</section>
