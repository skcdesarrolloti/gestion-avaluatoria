<?php
$currentStep = $currentStep ?? '';
$steps = [
    ['key' => 'expediente', 'label' => '1 · Expediente valuatorio', 'href' => url('avaluos/' . $record['id'] . '/expediente')],
    ['key' => 'sector', 'label' => '2 · Sector y entorno', 'href' => url('avaluos/' . $record['id'] . '/sector')],
    ['key' => 'sujeto', 'label' => '3 · Bien sujeto', 'href' => url('avaluos/' . $record['id'] . '/bien-sujeto')],
    ['key' => 'juridicas', 'label' => '4 · Características jurídicas', 'href' => url('avaluos/' . $record['id'] . '/caracteristicas-juridicas')],
    ['key' => 'entregable', 'label' => 'Entregable', 'href' => url('avaluos/' . $record['id'] . '/entregable')],
];
?>
<nav class="mt-7 flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" aria-label="Secciones del avalúo">
    <?php foreach ($steps as $step): ?>
        <a class="min-h-11 shrink-0 rounded-lg px-4 py-2 text-sm font-semibold <?= $currentStep === $step['key'] ? 'bg-blue-700 text-white shadow-sm' : 'bg-white text-blue-800 hover:border-blue-700' ?>"
            href="<?= e($step['href']) ?>">
            <?= e($step['label']) ?>
        </a>
    <?php endforeach; ?>
</nav>
<?php require BASE_PATH . '/app/Views/appraisals/appraisal-context-banner.php'; ?>
