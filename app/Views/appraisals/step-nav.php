<?php
$currentStep = $currentStep ?? '';
$steps = [
    ['key' => 'expediente', 'label' => '1 · Expediente valuatorio', 'href' => url('avaluos/' . $record['id'] . '/expediente')],
    ['key' => 'sector', 'label' => '2 · Sector y entorno', 'href' => url('avaluos/' . $record['id'] . '/sector')],
    ['key' => 'sujeto', 'label' => '3 · Bien sujeto', 'href' => url('avaluos/' . $record['id'] . '/bien-sujeto')],
    ['key' => 'entregable', 'label' => 'Entregable', 'href' => url('avaluos/' . $record['id'] . '/entregable')],
];
?>
<nav class="mt-7 flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" aria-label="Secciones del avalúo">
    <?php foreach ($steps as $step): ?>
        <?php if ($currentStep === 'expediente' && $step['key'] !== 'expediente'): ?>
            <button class="min-h-11 shrink-0 rounded-lg bg-white px-4 py-2 text-sm font-semibold text-blue-800 hover:border-blue-700"
                type="submit" form="expediente-form" name="next"
                value="<?= e($step['key'] === 'sujeto' ? 'subject' : $step['key']) ?>"><?= e($step['label']) ?></button>
        <?php else: ?>
            <a class="min-h-11 shrink-0 rounded-lg px-4 py-2 text-sm font-semibold <?= $currentStep === $step['key'] ? 'bg-blue-700 text-white shadow-sm' : 'bg-white text-blue-800 hover:border-blue-700' ?>"
                href="<?= e($step['href']) ?>">
                <?= e($step['label']) ?>
            </a>
        <?php endif; ?>
    <?php endforeach; ?>
</nav>
