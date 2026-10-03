<?php
$configItems = is_array($methodologyDecision['configuration'] ?? null) ? $methodologyDecision['configuration'] : [];
$workflowItems = is_array($methodologyDecision['workflow'] ?? null) ? $methodologyDecision['workflow'] : [];
$componentItems = is_array($methodologyDecision['components'] ?? null) ? $methodologyDecision['components'] : [];
$nextStep = is_array($methodologyDecision['next_step'] ?? null) ? $methodologyDecision['next_step'] : ['8.3 Desarrollo del método', ''];
$normativeInputs = is_array($methodologyDecision['normative_inputs'] ?? null) ? $methodologyDecision['normative_inputs'] : [];
$normativeInputItems = is_array($normativeInputs['items'] ?? null) ? $normativeInputs['items'] : [];
$normativeArticles = is_array($normativeInputs['article_cards'] ?? null) ? $normativeInputs['article_cards'] : [];
$normativeNotice = (string) ($methodologyDecision['normative_notice'] ?? '');
$firstComponent = in_array((string) ($componentKey ?? ''), array_column($componentItems, 'id'), true)
    ? (string) $componentKey : (string) ($componentItems[0]['id'] ?? '');
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
                <a class="btn-primary mt-4 min-h-11" href="<?= e($flowUrl('3')) ?>">
                    Ir a <?= e(\App\Services\MethodologyWorkflow::PREFIXES[$method ?? 'mercado'] ?? 'M') ?>3 · <?= ($method ?? '')==='costo'?'Insumos y presupuesto':'Insumos y comparables' ?>
                </a>
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

    <?php if ($normativeInputs !== []): ?>
        <div class="mt-6 rounded-xl border border-blue-100 bg-white p-4">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-bold uppercase text-blue-700">Insumo normativo para decidir</p>
                    <h3 class="mt-2 text-xl font-semibold text-slate-950">
                        Resolución 941: <?= e((string) ($normativeInputs['method'] ?? 'método')) ?>
                    </h3>
                    <p class="mt-1 text-sm font-semibold text-blue-800"><?= e((string) ($normativeInputs['articles'] ?? '')) ?></p>
                </div>
            </div>
            <?php if ($normativeNotice !== ''): ?>
                <p class="mt-3 rounded-lg bg-blue-50 p-3 text-sm leading-6 text-blue-950"><?= e($normativeNotice) ?></p>
                <?php $readingNumber = 15; require __DIR__ . '/valuation-methodology-article-reading.php'; ?>
            <?php endif; ?>
            <?php if ($normativeInputItems !== []): ?>
                <div class="mt-4 grid gap-3 md:grid-cols-2">
                    <?php foreach ($normativeInputItems as $item): ?>
                        <p class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm leading-6 text-slate-700"><?= e((string) $item) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if ($normativeArticles !== []): ?>
                <details class="mt-4 rounded-xl border border-blue-100 bg-blue-50">
                    <summary class="cursor-pointer px-4 py-3 text-sm font-bold text-blue-900">Leer artículos y resaltados del método</summary>
                    <div class="grid gap-3 border-t border-blue-100 p-4 md:grid-cols-2 xl:grid-cols-3">
                        <?php foreach ($normativeArticles as $article): ?>
                            <?php $highlights = is_array($article['highlights'] ?? null) ? $article['highlights'] : []; ?>
                            <article class="rounded-lg border border-white bg-white p-3 text-sm leading-6">
                                <p class="text-xs font-bold uppercase text-blue-700"><?= e((string) ($article['number'] ?? 'Artículo')) ?></p>
                                <h4 class="mt-1 font-semibold text-slate-950"><?= e((string) ($article['title'] ?? '')) ?></h4>
                                <p class="mt-2 text-slate-700"><?= e((string) ($article['summary'] ?? '')) ?></p>
                                <?php
                                $readingNumber = (int) preg_replace('/\D/', '', (string) ($article['number'] ?? ''));
                                require __DIR__ . '/valuation-methodology-article-reading.php';
                                ?>
                                <?php if ($highlights !== []): ?>
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        <?php foreach ($highlights as $highlight): ?>
                                            <mark class="rounded-full bg-amber-100 px-2 py-1 text-xs font-bold text-amber-900"><?= e((string) $highlight) ?></mark>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </details>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($componentItems !== []): ?>
        <div class="mt-6 rounded-xl border border-emerald-100 bg-white p-4"
            x-data="{ componentTab: '<?= e($firstComponent) ?>' }">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="eyebrow">Método por unidad o anexo</p>
                    <h3 class="mt-2 text-xl font-semibold text-slate-950">Composición metodológica del predio</h3>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                        Una unidad principal, una piscina, un depósito o un parqueadero pueden exigir rutas distintas.
                        Esta lectura prepara el 8.3 sin mezclar mercados ni costos.
                    </p>
                </div>
                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-800"><?= count($componentItems) ?> componente(s)</span>
            </div>
            <nav class="mt-4 flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" aria-label="Pestañas metodológicas por componente">
                <?php foreach ($componentItems as $component): ?>
                    <button type="button" class="min-h-11 shrink-0 rounded-lg px-4 py-2 text-sm font-semibold"
                        :class="componentTab === '<?= e((string) $component['id']) ?>' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600'"
                        @click="componentTab = '<?= e((string) $component['id']) ?>'"><?= e((string) $component['label']) ?></button>
                <?php endforeach; ?>
            </nav>
            <?php foreach ($componentItems as $component): ?>
                <article class="mt-4 grid gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4 md:grid-cols-3"
                    x-show="componentTab === '<?= e((string) $component['id']) ?>'">
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-500">Componente</p>
                        <h4 class="mt-2 text-lg font-semibold text-slate-950"><?= e((string) $component['label']) ?></h4>
                        <p class="mt-1 text-sm text-slate-600"><?= e((string) ($component['type_label'] ?: 'Tipología general')) ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase text-emerald-700">Ruta sugerida</p>
                        <h4 class="mt-2 text-lg font-semibold text-emerald-950"><?= e((string) $component['method']) ?></h4>
                        <p class="mt-1 text-sm text-slate-600"><?= e((string) $component['note']) ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase text-blue-700">Insumos para 8.3</p>
                        <p class="mt-2 text-sm leading-6 text-slate-700"><?= e((string) $component['inputs']) ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

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
