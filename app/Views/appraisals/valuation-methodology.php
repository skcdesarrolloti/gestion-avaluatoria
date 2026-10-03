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
$sourceUnitKey = $components[$componentKey]['parent_key'] ?? $componentKey;
$costAcademy = $method === 'costo' && in_array($stage, ['1', 'components'], true);
$costAcademyTheoryOnly = $costAcademy && ($selected['method'] ?? '') !== 'costo';
?>
<p class="eyebrow">Capítulo 8 · Metodología valuatoria</p>
<h1 class="mt-2 text-3xl font-semibold">Metodología valuatoria</h1>
<p class="mt-3 text-slate-600">Configura qué vas a valorar y elige uno o varios métodos. Cada recorrido conserva sus propios insumos, análisis y entregable.</p>
<?php require __DIR__ . '/step-nav.php'; ?>
<?php foreach (['methodology_message', 'methodology_error'] as $flash): $notice = \App\Core\Session::pullFlash($flash); if (!$notice) continue; ?>
    <p role="status" class="mt-4 rounded-xl border p-4"><?= e($notice) ?></p>
<?php endforeach; ?>
<?php require __DIR__ . '/methodology-navigation.php'; ?>
<?php require __DIR__ . '/methodology-unit-tabs.php'; ?>
<?php require __DIR__.'/methodology-step-articles.php'; ?>
<div class="mt-6">
<?php if ($stage === 'plan'): ?>
    <?php require __DIR__.'/methodology-plan.php'; ?>
<?php elseif ($costAcademy): ?>
    <?php require __DIR__ . '/methodology-cost-academy.php'; ?>
<?php elseif ($stage === 'components' || $stage === 'integration'): ?>
    <?php require __DIR__ . '/methodology-components.php'; ?>
<?php elseif ($stage === 'decision'): ?>
    <?php require __DIR__ . '/valuation-methodology-decision.php'; ?>
<?php elseif ($stage === 'report'): ?>
    <?php require __DIR__ . '/valuation-methodology-deliverable-preview.php'; ?>
<?php elseif ($stage === '1'): ?>
    <?php require __DIR__ . '/methodology-components.php'; ?>
<?php elseif ($stage === '2'): ?>
    <?php require __DIR__ . '/methodology-selection.php'; ?>
<?php elseif ($stage === '3' && $method === 'renta'): ?>
    <?php require __DIR__ . '/methodology-unassigned.php'; require __DIR__ . '/valuation-methodology-search.php'; ?>
<?php elseif ($stage === '3' && in_array($method, ['costo', 'residual'], true)): ?>
    <?php require __DIR__ . '/methodology-other-inputs.php'; ?>
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

<?php if (!$costAcademyTheoryOnly && $stage!=='plan'): require __DIR__ . '/methodology-next.php'; endif; ?>
