<?php
$configItems = is_array($methodologyDecision['configuration'] ?? null) ? $methodologyDecision['configuration'] : [];
$workflowItems = is_array($methodologyDecision['workflow'] ?? null) ? $methodologyDecision['workflow'] : [];
$nextStep = is_array($methodologyDecision['next_step'] ?? null) ? $methodologyDecision['next_step'] : ['8.3 Desarrollo del método', ''];
$deliverable82 = trim((string) ($methodologyChapterData['sections'][2][1] ?? ''));
?>
<section class="rounded-2xl border border-emerald-100 bg-emerald-50 p-6 shadow-sm sm:p-8">
    <div class="grid gap-5 xl:grid-cols-[0.95fr_1.05fr]">
        <div>
            <p class="eyebrow">8.2 Selección metodológica</p>
            <h2 class="mt-2 text-2xl font-semibold text-emerald-950">Camino recomendado</h2>
            <p class="mt-2 text-sm leading-6 text-emerald-900">
                Primero se revisa la configuración del expediente y se adopta el método. La captura de muestras o insumos continúa en 8.3.
            </p>
            <div class="mt-5 rounded-xl border border-white/80 bg-white p-5">
                <p class="text-xs font-bold uppercase text-emerald-700">Método recomendado</p>
                <h3 class="mt-2 text-2xl font-semibold text-slate-950"><?= e((string) ($methodologyDecision['recommended_method'] ?? 'Pendiente')) ?></h3>
                <p class="mt-3 text-sm leading-6 text-slate-700"><?= e((string) ($methodologyDecision['reason'] ?? 'Completa la matriz para obtener una recomendación.')) ?></p>
                <div class="mt-4 rounded-lg bg-emerald-50 p-3 text-sm leading-5 text-emerald-950">
                    <strong><?= e((string) ($nextStep[0] ?? '8.3 Desarrollo del método')) ?>:</strong>
                    <?= e((string) ($nextStep[1] ?? '')) ?>
                </div>
                <button type="button" class="btn-primary mt-4 min-h-11" @click="methodologyTab = '83'">
                    Ir al desarrollo 8.3
                </button>
            </div>
        </div>
        <div class="grid gap-3 sm:grid-cols-2">
            <?php foreach ($configItems as $item): ?>
                <?php $ok = ($item['state'] ?? '') === 'Completo'; ?>
                <article class="rounded-xl border <?= $ok ? 'border-emerald-100 bg-white' : 'border-amber-200 bg-amber-50' ?> p-4">
                    <div class="flex items-start justify-between gap-3">
                        <p class="text-xs font-bold uppercase text-slate-500"><?= e((string) ($item['label'] ?? 'Dato')) ?></p>
                        <span class="rounded-full <?= $ok ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-100 text-amber-800' ?> px-2 py-0.5 text-[11px] font-semibold"><?= e((string) ($item['state'] ?? '')) ?></span>
                    </div>
                    <p class="mt-2 text-sm font-semibold text-slate-950"><?= e((string) ($item['value'] ?? '')) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if ($workflowItems !== []): ?>
        <div class="mt-6 grid gap-3 lg:grid-cols-4">
            <?php foreach ($workflowItems as $index => $item): ?>
                <article class="rounded-xl border border-emerald-100 bg-white p-4 text-sm leading-5">
                    <span class="inline-flex size-7 items-center justify-center rounded-full bg-emerald-800 text-xs font-bold text-white"><?= e((string) ($index + 1)) ?></span>
                    <h3 class="mt-3 font-semibold text-slate-950"><?= e((string) ($item[0] ?? 'Paso')) ?></h3>
                    <p class="mt-2 text-xs leading-5 text-slate-600"><?= e((string) ($item[1] ?? '')) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="mt-6 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950">
        <strong>Texto que irá al entregable 8.2:</strong>
        <p class="mt-2 whitespace-pre-wrap"><?= e($deliverable82) ?></p>
    </div>

    <details class="mt-6 rounded-xl border border-emerald-100 bg-white">
        <summary class="cursor-pointer px-4 py-3 text-sm font-semibold text-emerald-900">Ver matriz técnica de soporte</summary>
        <div class="overflow-x-auto border-t border-emerald-100">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-emerald-50 text-xs uppercase text-emerald-900">
                    <tr><th class="p-3">Criterio</th><th class="p-3">Dato</th><th class="p-3">Soporte 1.1</th><th class="p-3">Regla</th><th class="p-3">Método</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($methodologyDecisionRows as $row): ?>
                        <tr class="align-top">
                            <td class="p-3 font-semibold text-slate-950"><?= e((string) ($row[0] ?? '')) ?></td>
                            <td class="p-3 text-slate-700"><?= e((string) ($row[1] ?? '')) ?></td>
                            <td class="p-3 text-xs leading-5 text-slate-600"><?= e((string) ($row[2] ?? '')) ?></td>
                            <td class="p-3 text-slate-600"><?= e((string) ($row[3] ?? '')) ?></td>
                            <td class="p-3 font-semibold text-emerald-900"><?= e((string) ($row[4] ?? '')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </details>
</section>
