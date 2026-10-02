<?php
use App\Services\MethodologyWorkflow as Workflow;
$currentStep = 'metodologia';
$methods = Workflow::METHODS;
$prefix = Workflow::PREFIXES[$method];
$factorGroups = $guide['factor_groups'] ?? [];
$methodologyGuides = array_values(array_filter($methodologyChapter['method_guides'], static fn ($item) => $item['key'] === $method));
$methodologyDecision = $methodologyChapter['decision'];
$methodologyDecisionRows = $methodologyDecision['rows'] ?? [];
$methodologyChapterData = $methodologyChapter;
$methodologyText = $methodologyChapter['text'] ?? '';
$methodologySections = $methodologyChapter['sections'] ?? [];
$methodologyReferences = $methodologyChapter['references'] ?? [];
$basePath = 'avaluos/' . $record['id'] . '/metodologia-valuatoria';
$flowUrl = static fn ($step, $m = null, $key = null) => url($basePath . '?' . http_build_query([
    'method' => $m ?? $method, 'stage' => $step, 'component' => $key ?? $componentKey]));
$componentLabel = $components[$componentKey]['label'] ?? 'Banco de muestras sin asignar';
?>
<p class="eyebrow">Capítulo 8 · Metodología valuatoria</p>
<h1 class="mt-2 text-3xl font-semibold">Metodología valuatoria</h1>
<p class="mt-3 text-slate-600">Inmuebles y anexos → método → M1–M5. Consulta la matriz del expediente y continúa con las muestras ya registradas.</p>
<?php require __DIR__ . '/step-nav.php'; ?>
<?php foreach (['methodology_message', 'methodology_error'] as $flash): $notice = \App\Core\Session::pullFlash($flash); if (!$notice) continue; ?>
    <p role="status" class="mt-4 rounded-xl border p-4"><?= e($notice) ?></p>
<?php endforeach; ?>
<?php require __DIR__ . '/methodology-navigation.php'; ?>
<?php if ($componentKey !== ''): ?>
<section class="mt-6 rounded-xl border border-teal-200 bg-teal-50 p-4">
    <p class="font-semibold">Componente en trabajo: <?= e($componentLabel) ?></p>
    <?php if ($stage !== '2'): ?><p class="mt-2 text-sm">Método guardado: <?= e($methods[$selected['method'] ?? ''] ?? 'Pendiente de selección') ?></p><?php endif; ?>
    <details class="mt-3"><summary class="min-h-11 cursor-pointer">Cambiar componente</summary>
        <div class="flex flex-wrap gap-2">
        <?php foreach ($components as $key => $component): ?>
            <a class="btn-secondary" href="<?= e($flowUrl($stage, ($flow[$key]['method'] ?? '') ?: 'mercado', $key)) ?>"><?= e($component['label']) ?></a>
        <?php endforeach; ?>
        <a class="btn-secondary" href="<?= e($flowUrl('3', 'mercado', '')) ?>">Banco sin asignar</a>
        </div>
    </details>
</section>
<?php endif; ?>
<?php if ($componentKey !== '' && in_array($stage, ['1','2','3','4','5'], true)): ?>
<nav class="mt-4 flex flex-wrap gap-2 rounded-xl bg-slate-100 p-2" aria-label="Métodos de valoración">
    <?php foreach ($methods as $key => $label): ?>
    <a class="min-h-11 rounded-xl px-4 py-3 font-semibold <?= $key === $method ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600' ?>"
       <?= $key === $method ? 'aria-current="page"' : '' ?> href="<?= e($flowUrl('1', $key)) ?>"><?= e($label) ?></a>
    <?php endforeach; ?>
</nav>
<nav class="mt-3 flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" aria-label="Etapas de <?= e($methods[$method]) ?>">
    <?php foreach (Workflow::STAGES as $key => $label): ?>
    <a class="min-h-11 shrink-0 rounded-xl px-4 py-3 text-sm font-semibold <?= (string) $key === $stage ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600' ?>"
       <?= (string) $key === $stage ? 'aria-current="page"' : '' ?> href="<?= e($flowUrl((string) $key)) ?>"><?= e($prefix . $key . ' ' . $label) ?></a>
    <?php endforeach; ?>
</nav>
<?php endif; ?>
<div class="mt-6">
<?php if ($stage === 'components' || $stage === 'integration'): ?>
    <?php require __DIR__ . '/methodology-components.php'; ?>
<?php elseif ($stage === 'decision'): ?>
    <?php require __DIR__ . '/valuation-methodology-decision.php'; ?>
<?php elseif ($stage === 'report'): ?>
    <?php require __DIR__ . '/valuation-methodology-deliverable-preview.php'; ?>
<?php elseif ($stage === '1'): ?>
    <?php require __DIR__ . '/valuation-methodology-method-guides.php'; ?>
<?php elseif ($stage === '2'): ?>
    <?php require __DIR__ . '/methodology-selection.php'; ?>
<?php elseif ($method !== 'mercado'): ?>
    <section class="rounded-xl border bg-white p-6"><h2 class="text-xl font-semibold"><?= e($prefix . $stage . ' ' . Workflow::STAGES[$stage]) ?></h2>
    <p class="mt-3">El método puede asignarse al componente y su academia está disponible. Su desarrollo operativo se realizará en la siguiente etapa. No se han calculado ni adoptado valores.</p></section>
<?php elseif ($stage === '3'): ?>
    <?php require __DIR__ . '/methodology-unassigned.php'; ?>
    <?php require __DIR__ . '/valuation-methodology-search.php'; ?>
<?php else: ?>
    <?php require __DIR__ . '/methodology-market-analysis.php'; ?>
<?php endif; ?>
</div>

<?php require __DIR__ . '/methodology-next.php'; ?>
