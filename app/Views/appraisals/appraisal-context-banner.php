<?php
$expedienteNumber = trim((string) ($record['expediente_number'] ?? ''));
$expedienteLabel = $expedienteNumber !== '' ? $expedienteNumber : 'Pendiente de asignar';
$title = trim((string) ($record['titulo'] ?? ''));
$selects = \App\Support\AppraisalCatalog::selectFields();
$label = static fn (string $field, string $value): string => (string) ($selects[$field][4][$value] ?? '');
$typeLabel = $label('tipo_inmueble', (string) ($record['tipo_inmueble'] ?? '')) ?: 'Tipo de inmueble pendiente';
$subtypeLabel = $label('subtipo_funcional', (string) ($record['subtipo_funcional'] ?? ''));
$destinyLabel = $label('destinacion', (string) ($record['destinacion'] ?? ''));
$phLabel = $label('regimen_ph', (string) ($record['regimen_ph'] ?? ''));
$chips = array_filter([
    'Tipo: ' . $typeLabel,
    $subtypeLabel !== '' ? 'Subtipo: ' . $subtypeLabel : '',
    $destinyLabel !== '' ? 'Destinación: ' . $destinyLabel : '',
    $phLabel !== '' ? 'PH: ' . $phLabel : '',
]);
?>
<div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm leading-6 text-amber-950">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <strong>Expediente en trabajo: <span data-expediente-number-output><?= e($expedienteLabel) ?></span></strong>
            <span class="block text-amber-900"><?= e($title !== '' ? $title : 'Ficha sin título') ?></span>
            <div class="mt-2 flex flex-wrap gap-2">
                <?php foreach ($chips as $chip): ?>
                    <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-amber-800"><?= e($chip) ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <a class="rounded-full bg-white px-3 py-1 text-xs font-bold text-amber-800" href="<?= e(url('avaluos/' . $record['id'] . '/expediente')) ?>">Ver módulo 1</a>
    </div>
</div>
