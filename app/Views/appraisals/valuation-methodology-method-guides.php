<?php
$firstMethodGuide = (string) ($methodologyGuides[0]['key'] ?? 'mercado');
$firstMethodPart = (string) ($methodologyGuides[0]['parts'][0]['key'] ?? 'comprende');
?>
<?php if ($methodologyGuides !== []): ?>
    <section class="mt-5 rounded-xl border border-indigo-100 bg-white p-4"
        x-data="{ methodGuideTab: '<?= e($firstMethodGuide) ?>', methodGuidePart: '<?= e($firstMethodPart) ?>' }">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase text-indigo-700">Guía amplia para el analista</p>
                <h3 class="mt-2 text-xl font-semibold text-slate-950">Métodos de la Resolución IGAC 941</h3>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                    Lectura operativa por método. Esta guía no reemplaza el criterio profesional; ordena artículos,
                    insumos y controles antes de pasar a la selección del numeral 8.2.
                </p>
            </div>
            <a class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-800"
                href="<?= e(\App\Support\IgacDocumentLibrary::urlFor('resolucion-igac-941-2026')) ?>">Resolución 941</a>
        </div>
        <nav class="mt-4 flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" aria-label="Métodos Resolución 941">
            <?php foreach ($methodologyGuides as $guide): ?>
                <?php $guideKey = (string) ($guide['key'] ?? ''); ?>
                <button type="button" class="min-h-11 shrink-0 rounded-lg px-4 py-2 text-sm font-semibold"
                    :class="methodGuideTab === '<?= e($guideKey) ?>' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600'"
                    @click="methodGuideTab = '<?= e($guideKey) ?>'; methodGuidePart = 'comprende'">
                    <?= e((string) ($guide['label'] ?? 'Método')) ?>
                </button>
            <?php endforeach; ?>
        </nav>
        <?php foreach ($methodologyGuides as $guide): ?>
            <?php $guideKey = (string) ($guide['key'] ?? ''); $parts = is_array($guide['parts'] ?? null) ? $guide['parts'] : []; ?>
            <article class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4"
                x-show="methodGuideTab === '<?= e($guideKey) ?>'">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase text-indigo-700"><?= e((string) ($guide['articles'] ?? '')) ?></p>
                        <h4 class="mt-1 text-lg font-semibold text-slate-950">Método de <?= e((string) ($guide['label'] ?? '')) ?></h4>
                        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-700"><?= e((string) ($guide['summary'] ?? '')) ?></p>
                    </div>
                </div>
                <nav class="mt-4 flex gap-2 overflow-x-auto" aria-label="Detalle del método">
                    <?php foreach ($parts as $part): ?>
                        <?php $partKey = (string) ($part['key'] ?? ''); ?>
                        <button type="button" class="min-h-11 shrink-0 rounded-lg border px-3 py-2 text-xs font-bold"
                            :class="methodGuidePart === '<?= e($partKey) ?>' ? 'border-indigo-200 bg-white text-indigo-800 shadow-sm' : 'border-slate-200 bg-slate-100 text-slate-600'"
                            @click="methodGuidePart = '<?= e($partKey) ?>'">
                            <?= e((string) ($part['label'] ?? 'Detalle')) ?>
                        </button>
                    <?php endforeach; ?>
                </nav>
                <?php foreach ($parts as $part): ?>
                    <?php $partKey = (string) ($part['key'] ?? ''); $bullets = is_array($part['bullets'] ?? null) ? $part['bullets'] : []; ?>
                    <div class="mt-4 rounded-xl border border-white bg-white p-4" x-show="methodGuidePart === '<?= e($partKey) ?>'">
                        <h5 class="font-semibold text-slate-950"><?= e((string) ($part['label'] ?? 'Detalle')) ?></h5>
                        <ul class="mt-3 space-y-2 text-sm leading-6 text-slate-700">
                            <?php foreach ($bullets as $bullet): ?>
                                <li class="flex gap-2"><span class="mt-2 size-1.5 shrink-0 rounded-full bg-indigo-500"></span><span><?= e((string) $bullet) ?></span></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>
