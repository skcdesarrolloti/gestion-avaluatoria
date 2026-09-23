<?php
/** @var array<int, array{src:string, quote:string, use:string, report:string}> $normativeAcademy */
/** @var array<int, string> $valuationGuidance */
?>
<div class="mt-5 grid gap-4 xl:grid-cols-[1.35fr_0.9fr]">
    <section class="rounded-xl border border-indigo-100 bg-indigo-50 p-4 text-sm leading-6 text-indigo-950">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h3 class="font-semibold">Academia normativa aplicada a obsolescencias</h3>
                <p class="mt-1 text-xs text-indigo-800">Cita clave y aplicación práctica para sustentar sin volver pesado el módulo.</p>
            </div>
            <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-indigo-700">NTS + IVS</span>
        </div>
        <div class="mt-3 overflow-x-auto rounded-lg border border-indigo-100 bg-white">
            <table class="w-full min-w-[68rem] text-left text-xs leading-5">
                <thead class="bg-indigo-100/70 uppercase text-indigo-800">
                    <tr><th class="p-2">Referencia</th><th class="p-2">Cita clave</th><th class="p-2">Cómo se aplica aquí</th><th class="p-2">Qué pasa al entregable</th></tr>
                </thead>
                <tbody class="divide-y divide-indigo-50 text-slate-700">
                    <?php foreach ($normativeAcademy as $row): ?>
                        <tr>
                            <td class="p-2 font-bold text-indigo-900"><?= e($row['src']) ?></td>
                            <td class="p-2 italic text-slate-800"><?= e($row['quote']) ?></td>
                            <td class="p-2"><?= e($row['use']) ?></td>
                            <td class="p-2"><?= e($row['report']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="mt-2 text-xs text-indigo-800"><strong>Lectura práctica:</strong> esta academia no reemplaza la norma completa; deja visible el criterio normativo mínimo que corresponde a obsolescencias.</p>
    </section>
    <section class="rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950">
        <h3 class="font-semibold">Incidencia valuatoria y límites</h3>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            <?php foreach ($valuationGuidance as $note): ?><li><?= e($note) ?></li><?php endforeach; ?>
        </ul>
        <div class="mt-3 rounded-lg bg-white px-3 py-2 text-xs leading-5 text-blue-900"><strong>Uso rápido:</strong> revise, marque, soporte y luego decida si el hallazgo tiene efecto material en la valoración. La pantalla no inventa un descuento.</div>
    </section>
</div>
