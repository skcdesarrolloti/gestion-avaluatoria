<?php
/** @var array<int, array{src:string, asks:string, use:string}> $phAcademy */
/** @var array<int, array{type:string, tone:string, items:string}> $phDeliverableFilter */
$filterClass = static fn (string $tone): string => match ($tone) {
    'ok' => 'border-emerald-100 bg-emerald-50 text-emerald-950',
    'warn' => 'border-amber-100 bg-amber-50 text-amber-950',
    'risk' => 'border-red-100 bg-red-50 text-red-950',
    default => 'border-blue-100 bg-blue-50 text-blue-950',
};
?>
<div class="mt-5 grid gap-4 xl:grid-cols-[1.35fr_0.9fr]">
    <section class="rounded-xl border border-indigo-100 bg-indigo-50 p-4 text-sm leading-6 text-indigo-950">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h3 class="font-semibold">Academia normativa aplicada a propiedad horizontal</h3>
                <p class="mt-1 text-xs text-indigo-800">Guía corta para decidir qué debe ir al informe y qué queda como soporte PH.</p>
            </div>
            <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-indigo-700">Ley 675 + NTS + IVS</span>
        </div>
        <div class="mt-3 overflow-x-auto rounded-lg border border-indigo-100 bg-white">
            <table class="w-full min-w-[56rem] text-left text-xs leading-5">
                <thead class="bg-indigo-100/70 uppercase text-indigo-800">
                    <tr><th class="p-2">Referencia</th><th class="p-2">Qué pide al analista</th><th class="p-2">Cómo se aplica aquí</th></tr>
                </thead>
                <tbody class="divide-y divide-indigo-50 text-slate-700">
                    <?php foreach ($phAcademy as $row): ?>
                        <tr><td class="p-2 font-bold text-indigo-900"><?= e($row['src']) ?></td><td class="p-2"><?= e($row['asks']) ?></td><td class="p-2"><?= e($row['use']) ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
    <section class="rounded-xl border border-slate-200 bg-white p-4 text-sm leading-6">
        <h3 class="font-semibold text-slate-900">Filtro editorial del módulo 3.5</h3>
        <p class="mt-1 text-xs text-slate-600">Esto evita que el entregable copie todo el reglamento y mantiene solo criterio útil.</p>
        <div class="mt-3 grid gap-3">
            <?php foreach ($phDeliverableFilter as $row): ?>
                <div class="rounded-lg border p-3 <?= e($filterClass($row['tone'])) ?>">
                    <p class="font-bold"><?= e($row['type']) ?></p>
                    <p class="mt-1 text-xs leading-5"><?= e($row['items']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>
