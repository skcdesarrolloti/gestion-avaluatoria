<?php
$tabComponents = ($costAcademy ?? false)
    ? array_filter(\App\Services\MethodologyValuationPlan::working($components), static fn ($key) => ($flow[$key]['method'] ?? '') === 'costo', ARRAY_FILTER_USE_KEY)
    : \App\Services\MethodologyValuationPlan::working($components);
if ($tabComponents !== [] && !in_array($stage, ['plan', 'integration', 'report'], true)): ?>
<nav class="mt-5 flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" aria-label="Unidades y anexos del predio">
    <?php foreach ($tabComponents as $unitKey => $unitComponent):
        $unitMethod = ($flow[$unitKey]['method'] ?? '') ?: 'mercado';
        $activeUnit = $componentKey ?: array_key_first($components);
    ?>
    <a class="btn-secondary shrink-0 <?= $unitKey === $activeUnit ? 'bg-white text-orange-600 shadow-sm' : '' ?>"
        <?= $unitKey === $activeUnit ? 'aria-current="page"' : '' ?>
        href="<?= e($flowUrl('components', $unitMethod, $unitKey)) ?>"><?= e($unitComponent['label']) ?></a>
    <?php endforeach; ?>
</nav>
<?php if ($componentKey !== '' && !($costAcademyTheoryOnly ?? false)): ?>
<p class="mt-3 font-semibold"><?= e($componentLabel) ?> · Método registrado: <?= e($methods[$selected['method'] ?? ''] ?? 'Por seleccionar') ?></p>
<?php if ($method==='costo' && ($selected['method'] ?? '')!=='costo'): ?><p class="mt-2 text-sm text-teal-800">Consulta de academia Costo. Para trabajar C2, selecciona Costo en «Método del componente»; esta lectura conserva el método registrado.</p><?php endif; ?>
<?php endif; endif; ?>
