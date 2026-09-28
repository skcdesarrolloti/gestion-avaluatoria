<?php
/** @var array<int, array{src:string, tag:string, href:string, quote:string, use:string, report:string}> $normativeAcademy */
/** @var array<int, string> $valuationGuidance */
?>
<div class="mt-5 space-y-4">
    <section class="rounded-xl border border-indigo-100 bg-indigo-50 p-4 text-sm leading-6 text-indigo-950">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h3 class="font-semibold">Academia normativa aplicada a obsolescencias</h3>
                <p class="mt-1 text-xs text-indigo-800">Cita clave, utilidad y acceso a la biblioteca normativa que sustenta el criterio.</p>
            </div>
            <a class="rounded-full bg-white px-3 py-1 text-xs font-bold text-indigo-700" href="<?= e(url('maestros#biblioteca-documental')) ?>">Subir soporte</a>
        </div>
        <div class="mt-3 grid gap-3 md:grid-cols-2">
            <?php foreach ($normativeAcademy as $row): ?>
                <article class="rounded-lg border border-indigo-100 bg-white p-3 text-xs leading-5 text-slate-700">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <h4 class="font-bold text-indigo-900"><?= e($row['src']) ?></h4>
                        <span class="rounded-full bg-indigo-50 px-2 py-0.5 font-bold text-indigo-700"><?= e($row['tag']) ?></span>
                    </div>
                    <p class="mt-2 italic text-slate-800"><?= e($row['quote']) ?></p>
                    <p class="mt-2"><strong>Uso:</strong> <?= e($row['use']) ?></p>
                    <p class="mt-1"><strong>Entregable:</strong> <?= e($row['report']) ?></p>
                    <a class="mt-3 inline-flex rounded-lg border border-indigo-200 px-3 py-2 font-semibold text-indigo-800 hover:bg-indigo-50"
                        href="<?= e($row['href']) ?>">Consultar biblioteca</a>
                </article>
            <?php endforeach; ?>
        </div>
        <p class="mt-2 text-xs text-indigo-800"><strong>Lectura práctica:</strong> esta academia no reemplaza la norma completa; deja visible el criterio normativo mínimo que corresponde a obsolescencias.</p>
    </section>
    <section class="rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h3 class="font-semibold">Incidencia valuatoria y límites</h3>
                <p class="mt-1 text-xs text-blue-800">Reglas prácticas para decidir cuándo el hallazgo pasa al análisis económico.</p>
            </div>
            <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-blue-700">Criterio del perito</span>
        </div>
        <div class="mt-3 grid gap-3 md:grid-cols-3">
            <?php foreach ($valuationGuidance as $note): ?>
                <div class="rounded-lg border border-blue-100 bg-white p-3 text-xs leading-5 text-blue-950"><?= e($note) ?></div>
            <?php endforeach; ?>
        </div>
        <div class="mt-3 rounded-lg border border-blue-100 bg-white px-3 py-2 text-xs leading-5 text-blue-900">
            <strong>Uso rápido:</strong> revise, marque, soporte y luego decida si el hallazgo tiene efecto material en la valoración. La pantalla no inventa un descuento.
        </div>
    </section>
</div>
