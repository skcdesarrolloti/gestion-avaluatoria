<?php
$urbanUseRows = [
    ['PRINCIPAL', 'use_principal_text', 'bg-blue-600 text-white'],
    ['COMPATIBLE', 'use_compatible_text', 'bg-blue-50 text-slate-950'],
    ['COMPLEMENTARIO', 'use_complementary_text', 'bg-white text-slate-950'],
    ['RESTRINGIDO', 'use_restricted_text', 'bg-blue-50 text-slate-950'],
    ['PROHIBIDO', 'use_prohibited_text', 'bg-white text-slate-950'],
];
$hasUrbanUseTable = false;
foreach ($urbanUseRows as $row) if (trim((string) $value($row[1])) !== '') $hasUrbanUseTable = true;
?>
<section class="rounded-xl border border-blue-200 bg-white p-4">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <p class="text-xs font-semibold uppercase text-blue-800">Cuadro de usos MIDAS / Decreto 0977</p>
            <h3 class="mt-1 font-semibold text-slate-950">Reglamentación copiada para el informe</h3>
        </div>
        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
            <?= e($value('use_regulation_table') ?: 'Cuadro pendiente') ?>
        </span>
    </div>
    <?php if ($hasUrbanUseTable): ?>
        <div class="mt-4 overflow-x-auto rounded-lg border border-blue-200">
            <table class="min-w-full border-collapse text-sm">
                <tbody>
                    <?php foreach ($urbanUseRows as [$label, $key, $class]): ?>
                        <tr class="border-b border-blue-100 last:border-0">
                            <th class="w-44 align-top <?= e($class) ?> px-3 py-3 text-left font-semibold"><?= e($label) ?></th>
                            <td class="whitespace-pre-wrap px-3 py-3 leading-6 text-slate-800">
                                <?= e(trim((string) $value($key)) ?: 'Pendiente de pegar desde MIDAS') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="mt-4 rounded-lg bg-amber-50 p-3 text-sm font-semibold leading-6 text-amber-900">
            Pega el bloque completo de Uso del suelo desde el numeral 3 o en 5.1. Este cuadro debe quedar visible antes de cerrar el capítulo 5.
        </p>
    <?php endif; ?>
</section>
