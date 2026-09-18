<?php
$matrixReadonly = (bool) ($matrixReadonly ?? false);
$matrixCompleteKey = $matrixReadonly ? 'cuadro_completo_impresion' : 'cuadro_completo_informe';
?>
<div class="rounded-2xl border border-blue-200 bg-blue-50/40 p-4">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-xs font-bold uppercase tracking-wide text-blue-800">Capítulo 4</p>
            <h3 class="text-lg font-bold text-blue-950">4.0 Identificación de las características jurídicas</h3>
            <p class="mt-1 max-w-4xl text-sm leading-6 text-slate-600">
                Esta matriz corresponde al capítulo jurídico registral del entregable. Resume la condición jurídica observable del inmueble a partir del certificado analizado, sin que ello constituya estudio de títulos.
            </p>
        </div>
        <label class="inline-flex items-center gap-2 text-xs text-slate-600">
            <input class="h-4 w-4 rounded border-slate-300" type="checkbox"
                <?= $matrixReadonly ? '' : 'name="' . e($matrixCompleteKey) . '"' ?> value="Sí"
                <?= $field($matrixCompleteKey) === 'Sí' ? 'checked' : '' ?> <?= $matrixReadonly ? 'disabled' : '' ?>>
            <span>Cuadro completo</span>
        </label>
    </div>
    <div class="overflow-x-auto rounded-xl border border-blue-200 bg-white">
        <table class="min-w-[1000px] w-full border-collapse text-sm">
            <thead class="bg-blue-800 text-left text-white">
                <tr><th class="px-4 py-3">Item</th><th class="px-4 py-3">Variable</th><th class="px-4 py-3">Descripción de la variable</th><th class="px-4 py-3">Valores</th></tr>
            </thead>
            <tbody>
                <?php foreach ($matrixRows as [$item, $variable, $description, $key, $type]): ?>
                    <tr class="align-top">
                        <td class="border border-blue-100 px-4 py-3 text-center font-bold"><?= e($item) ?></td>
                        <td class="border border-blue-100 px-4 py-3 font-bold text-blue-950"><?= e($variable) ?></td>
                        <td class="border border-blue-100 px-4 py-3 text-slate-700"><?= e($description) ?></td>
                        <td class="border border-blue-100 px-4 py-3">
                            <?php if ($matrixReadonly): ?>
                                <div class="min-h-12 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 whitespace-pre-wrap">
                                    <?= e($field($key) !== '' ? $field($key) : 'Pendiente de lectura o revisión manual') ?>
                                </div>
                            <?php elseif ($type === 'textarea'): ?>
                                <textarea class="input min-h-28" name="<?= e($key) ?>"><?= e($field($key)) ?></textarea>
                            <?php else: ?>
                                <input class="input" name="<?= e($key) ?>" value="<?= e($field($key)) ?>">
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p class="mt-3 text-sm text-slate-600"><strong>Nota:</strong> La anterior no constituye el estudio de títulos.</p>
</div>
