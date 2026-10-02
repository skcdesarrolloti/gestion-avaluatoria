<?php
$unitFlow = \App\Services\MethodologyWorkflow::saved($record);
$unitMethod = (string) ($unitFlow[(string) ($unit['id'] ?? '')]['method'] ?? '');
$unitMethodLabel = \App\Services\MethodologyWorkflow::METHODS[$unitMethod] ?? 'Por seleccionar';
$unitMethodUrl = url('avaluos/' . $record['id'] . '/metodologia-valuatoria?' . http_build_query([
    'component' => (string) ($unit['id'] ?? ''), 'stage' => '2',
]));
?>
<div class="rounded-lg border border-teal-200 bg-teal-50 p-4 md:col-span-2">
    <h5 class="font-semibold text-teal-950">Cómo se va a valorar este componente</h5>
    <p class="mt-2 text-sm">Método registrado: <strong><?= e($unitMethodLabel) ?></strong></p>
    <a class="btn-secondary mt-3" href="<?= e($unitMethodUrl) ?>">Elegir o revisar método de <?= e($unitDisplay($unit)) ?></a>
    <p class="mt-2 text-sm text-teal-950">Abre Método y alcance de este mismo componente en el capítulo 8. Allí decides Mercado, Costo, Renta o Residual. La tipología IGAC es una referencia constructiva, no la elección del método.</p>
</div>
