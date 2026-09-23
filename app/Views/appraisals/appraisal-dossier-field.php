<?php
$expedienteNumber = trim((string) ($record['expediente_number'] ?? ''));
?>
<div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-950 md:col-span-2">
    <label class="label">Número de expediente
        <input class="input bg-white font-semibold tracking-wide" data-expediente-number-output
            value="<?= e($expedienteNumber !== '' ? $expedienteNumber : 'Pendiente de asignar') ?>" readonly>
    </label>
    <p class="mt-2 text-xs leading-5 text-amber-900">
        Formato: código del perito + año + mes + consecutivo. Ejemplo: 02-2026-09-001.
        Se asigna automáticamente al guardar este módulo con perito responsable seleccionado.
    </p>
</div>
