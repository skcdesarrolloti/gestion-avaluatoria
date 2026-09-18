<?php
$technicalValue = static fn (string $key): string => (string) ($technical[$key] ?? '');
$technicalTotal = 0;
$technicalFilled = 0;
foreach ($phCatalog['technical'] as $group) {
    foreach ($group[1] as $key => $label) {
        $technicalTotal++;
        if (trim($technicalValue((string) $key)) !== '') $technicalFilled++;
    }
}
?>
<div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h3 class="text-lg font-semibold">Descripción técnica migrada de la avanzada PH</h3>
            <p class="mt-2 text-sm leading-6 text-slate-600">
                Estos campos estructuran la copropiedad como banco reutilizable y como soporte del informe.
                El lector documental llena vacíos, pero el analista valida antes del entregable.
            </p>
        </div>
        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-800">
            <?= $technicalFilled ?> de <?= $technicalTotal ?> campos diligenciados
        </span>
    </div>
    <div class="mt-5 space-y-5">
        <?php foreach ($phCatalog['technical'] as [$groupTitle, $fields]): ?>
            <details class="rounded-xl border border-slate-200 bg-white p-4" open>
                <summary class="cursor-pointer text-base font-semibold"><?= e($groupTitle) ?></summary>
                <div class="mt-4 grid gap-4 lg:grid-cols-2">
                    <?php foreach ($fields as $key => $label): ?>
                        <?php $isShort = in_array($key, ['numero_edificios', 'numero_unidades', 'ubicacion_unidad'], true); ?>
                        <label class="label"><?= e($label) ?>
                            <?php if ($isShort): ?>
                                <input class="input mt-2" name="ph[technical][<?= e($key) ?>]"
                                    value="<?= e($technicalValue((string) $key)) ?>"
                                    placeholder="Dato no identificado en la lectura preliminar">
                            <?php else: ?>
                                <textarea class="input mt-2 min-h-24" rows="3"
                                    name="ph[technical][<?= e($key) ?>]"
                                    placeholder="Dato no identificado en la lectura preliminar"><?= e($technicalValue((string) $key)) ?></textarea>
                            <?php endif; ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </details>
        <?php endforeach; ?>
    </div>
</div>
