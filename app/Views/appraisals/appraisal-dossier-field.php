<?php
$expedienteNumber = trim((string) ($record['expediente_number'] ?? ''));
$dossierCreated = $expedienteNumber !== '';
?>
<div data-dossier-card class="rounded-xl border p-4 text-sm leading-6 md:col-span-2 <?= $dossierCreated ? 'border-emerald-200 bg-emerald-50 text-emerald-950' : 'border-amber-200 bg-amber-50 text-amber-950' ?>">
    <label class="label"><span data-dossier-state><?= $dossierCreated ? 'Expediente creado' : 'Número de expediente' ?></span>
        <input class="input bg-white font-semibold tracking-wide" data-expediente-number-output
            value="<?= e($expedienteNumber !== '' ? $expedienteNumber : 'Pendiente de asignar') ?>" readonly>
    </label>
    <p class="mt-2 text-xs leading-5 <?= $dossierCreated ? 'text-emerald-900' : 'text-amber-900' ?>" data-dossier-help>
        Formato: código del perito + año + mes + consecutivo. Ejemplo: 02-2026-09-001.
        Se crea solo cuando pulses el botón, con el perito responsable seleccionado.
    </p>
    <?php if ($expedienteNumber === ''): ?>
        <div data-create-dossier-panel>
        <button class="btn-primary mt-3" type="submit" name="create_expediente" value="1" data-create-dossier>
            Crear expediente
        </button>
        <p class="mt-2 text-xs leading-5 text-amber-900">
            El consecutivo queda fijo una vez creado. Guardar cambios no consume nuevos consecutivos.
        </p>
        </div>
    <?php endif; ?>
</div>
