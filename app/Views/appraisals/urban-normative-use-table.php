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
$urbanUseExcerpt = static function (string $text): string {
    $clean = trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
    return mb_strlen($clean) > 240 ? mb_substr($clean, 0, 240) . '...' : $clean;
};
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
                            <?php $fullText = trim((string) $value($key)); ?>
                            <td class="px-3 py-3 leading-6 text-slate-800">
                                <?php if ($fullText !== ''): ?>
                                    <p><?= e($urbanUseExcerpt($fullText)) ?></p>
                                    <?php if (mb_strlen($fullText) > 240): ?>
                                        <details class="mt-2 rounded-lg border border-slate-200 bg-slate-50 p-2">
                                            <summary class="cursor-pointer text-xs font-semibold text-blue-800">Ver texto completo</summary>
                                            <p class="mt-2 whitespace-pre-wrap text-xs leading-5 text-slate-700"><?= e($fullText) ?></p>
                                        </details>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-slate-500">Pendiente de procesar desde MIDAS</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="mt-4 rounded-lg bg-amber-50 p-3 text-sm font-semibold leading-6 text-amber-900">
            Procesa la lectura completa de MIDAS desde el numeral 2. Este cuadro debe quedar visible antes de cerrar el capítulo 5.
        </p>
    <?php endif; ?>
</section>
