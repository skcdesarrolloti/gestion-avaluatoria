<section class="rounded-2xl border border-emerald-100 bg-emerald-50 p-6 shadow-sm sm:p-8">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">8.2 Selección metodológica</p>
            <h2 class="mt-2 text-2xl font-semibold text-emerald-950">Matriz de decisión del método</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-emerald-900">
                La recomendación sale de los datos del expediente y reutiliza los soportes visibles en el numeral 1.1.
                Si un campo está pendiente, el analista debe completarlo antes de cerrar el método en el informe.
            </p>
        </div>
        <span class="rounded-full bg-white px-4 py-2 text-sm font-bold text-emerald-800"><?= e((string) ($methodologyDecision['recommended_method'] ?? 'Pendiente')) ?></span>
    </div>
    <div class="mt-5 rounded-xl border border-white/80 bg-white p-4 text-sm leading-6 text-slate-700">
        <strong class="text-slate-950">Justificación sugerida:</strong>
        <?= e((string) ($methodologyDecision['reason'] ?? 'Completa la matriz para obtener una recomendación.')) ?>
    </div>
    <div class="mt-5 overflow-x-auto rounded-xl border border-emerald-100 bg-white">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-emerald-50 text-xs uppercase text-emerald-900">
                <tr><th class="p-3">Criterio</th><th class="p-3">Dato del expediente</th><th class="p-3">Soporte del 1.1</th><th class="p-3">Regla de lectura</th><th class="p-3">Método orientado</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($methodologyDecisionRows as $row): ?>
                    <tr class="align-top">
                        <td class="p-3 font-semibold text-slate-950"><?= e((string) ($row[0] ?? '')) ?></td>
                        <td class="p-3 text-slate-700"><?= e((string) ($row[1] ?? '')) ?></td>
                        <td class="p-3 text-xs leading-5 text-slate-600"><?= e((string) ($row[2] ?? '')) ?></td>
                        <td class="p-3 text-slate-600"><?= e((string) ($row[3] ?? '')) ?></td>
                        <td class="p-3 font-semibold text-emerald-900"><?= e((string) ($row[4] ?? '')) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if (trim((string) ($methodologyDecision['niif_note'] ?? '')) !== ''): ?>
        <div class="mt-5 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950">
            <strong>Nota NIIF:</strong> <?= e((string) $methodologyDecision['niif_note']) ?>
        </div>
    <?php endif; ?>
    <?php if (trim((string) ($methodologyDecision['special_template'] ?? '')) !== ''): ?>
        <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-950">
            <strong>Caso especial de homologación:</strong> <?= e((string) $methodologyDecision['special_template']) ?>
        </div>
    <?php endif; ?>
</section>
