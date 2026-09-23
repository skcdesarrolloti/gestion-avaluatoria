<?php
/** @var string $academyModule */
$academyRows = \App\Support\AppraisalSubjectNormativeAcademy::for($academyModule ?? '');
if (!$academyRows) return;
?>
<section class="mt-5 rounded-xl border border-indigo-100 bg-indigo-50 p-4 text-sm leading-6 text-indigo-950">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h3 class="font-semibold">Academia normativa del numeral <?= e($academyModule) ?></h3>
            <p class="mt-1 text-indigo-800">Norma a la mano: referencia mínima, aplicación práctica y efecto en el entregable.</p>
        </div>
        <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-indigo-800">NTS + Decreto 1420 + IVS</span>
    </div>
    <div class="mt-4 overflow-x-auto rounded-lg border border-indigo-100 bg-white">
        <table class="min-w-full text-left text-xs md:text-sm">
            <thead class="bg-indigo-50 uppercase text-indigo-800">
                <tr><th class="p-3">Referencia</th><th class="p-3">Cita clave</th><th class="p-3">Cómo se aplica aquí</th><th class="p-3">Qué pasa al entregable</th></tr>
            </thead>
            <tbody class="divide-y divide-indigo-100">
                <?php foreach ($academyRows as $row): ?>
                    <tr>
                        <td class="p-3 font-semibold text-indigo-900"><?= e($row['src']) ?></td>
                        <td class="p-3 italic"><?= e($row['quote']) ?></td>
                        <td class="p-3"><?= e($row['use']) ?></td>
                        <td class="p-3"><?= e($row['report']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
