<div class="rounded-2xl border border-slate-200 bg-white p-4">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <h4 class="text-sm font-bold text-blue-950"><?= e($tableTitle) ?></h4>
        <label class="inline-flex items-center gap-2 text-xs text-slate-500">
            <input class="h-4 w-4 rounded border-slate-300" type="checkbox">
            <span>Cuadro validado</span>
        </label>
    </div>
    <?php if (!$tableRows): ?>
        <p class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-500">
            Aún no se han identificado anotaciones para este cuadro.
        </p>
    <?php else: ?>
        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="min-w-[980px] w-full border-collapse text-xs">
                <thead class="bg-blue-50 text-left text-blue-950">
                    <tr>
                        <th class="border border-slate-200 px-3 py-2"># Anot.</th>
                        <th class="border border-slate-200 px-3 py-2">Estado</th>
                        <th class="border border-slate-200 px-3 py-2">Documento soporte</th>
                        <th class="border border-slate-200 px-3 py-2">Descripción del acto</th>
                        <th class="border border-slate-200 px-3 py-2">Especificación</th>
                        <th class="border border-slate-200 px-3 py-2">DE</th>
                        <th class="border border-slate-200 px-3 py-2">A</th>
                        <th class="border border-slate-200 px-3 py-2">Valor del acto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tableRows as $row): ?>
                        <?php [, $trafficLabel, $trafficRowClass, $trafficBadgeClass] = \App\Support\AppraisalLegalView::trafficLight($row); ?>
                        <tr class="align-top <?= e($trafficRowClass) ?>">
                            <td class="border border-slate-200 px-3 py-2 font-semibold"><?= e($row['orden'] ?? '') ?></td>
                            <td class="border border-slate-200 px-3 py-2">
                                <span class="inline-flex min-h-7 items-center rounded-full border px-2 py-1 text-[11px] font-bold <?= e($trafficBadgeClass) ?>">
                                    <?= e($trafficLabel) ?>
                                </span>
                            </td>
                            <td class="border border-slate-200 px-3 py-2"><?= e($row['documento'] ?? '') ?></td>
                            <td class="border border-slate-200 px-3 py-2"><?= e($row['descripcion_acto'] ?? '') ?></td>
                            <td class="border border-slate-200 px-3 py-2"><?= e($row['especificacion'] ?? '') ?></td>
                            <td class="border border-slate-200 px-3 py-2"><?= e($row['personaDe'] ?? '') ?></td>
                            <td class="border border-slate-200 px-3 py-2"><?= e($row['personaA'] ?? '') ?></td>
                            <td class="border border-slate-200 px-3 py-2"><?= e($row['valor'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
