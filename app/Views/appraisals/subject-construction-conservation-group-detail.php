<tr class="bg-slate-50/70">
    <td class="p-3" colspan="6">
        <div class="rounded-lg border border-slate-200 bg-white p-3">
            <p class="mb-2 text-xs font-semibold uppercase text-slate-600">Factores que conforman <?= e((string) ($group['label'] ?? 'el grupo')) ?></p>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr><th class="p-2">Factor</th><th class="p-2">Estado usado</th><th class="p-2">Peso</th><th class="p-2">Aporte</th><th class="p-2">Lectura</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach (($group['items'] ?? []) as $item): ?>
                            <?php
                            if (($item['applicability'] ?? '') === 'no_aplica') continue;
                            $used = (string) (($item['state_adopted'] ?? '') ?: ($item['state_proposed'] ?? ''));
                            $state = (float) $used;
                            $weight = (float) ($item['weight'] ?? 1);
                            $contribution = $state > 0 ? $state * $weight : 0.0;
                            ?>
                            <tr>
                                <td class="p-2 font-semibold text-slate-800"><?= e((string) ($item['subcomponent_label'] ?? '')) ?></td>
                                <td class="p-2"><?= e(\App\Support\AppraisalConservationCatalog::stateLabel($used)) ?></td>
                                <td class="p-2"><?= e($fmt($weight)) ?></td>
                                <td class="p-2"><?= e($state > 0 ? $fmt($state) . ' × ' . $fmt($weight) . ' = ' . $fmt($contribution) : 'Pendiente') ?></td>
                                <td class="p-2">Hallazgo <?= e(\App\Support\AppraisalConservationCatalog::stateLabel((string) ($item['finding_state'] ?? ''))) ?> · Funcionalidad <?= e(\App\Support\AppraisalConservationCatalog::stateLabel((string) ($item['functionality_floor'] ?? ''))) ?> · Intervención <?= e(\App\Support\AppraisalConservationCatalog::stateLabel((string) ($item['intervention_state'] ?? ''))) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="mt-2 text-xs leading-5 text-slate-600">
                Índice del grupo = suma de aportes / suma de pesos. Resultado: <?= e($calc['formula']) ?> = <?= e($calc['score'] > 0 ? number_format((float) $calc['score'], 2, ',', '') : 'pendiente') ?>, que se redondea al estado IGAC más cercano.
            </p>
        </div>
    </td>
</tr>
