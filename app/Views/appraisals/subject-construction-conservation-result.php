<section class="space-y-4" x-show="activeConservation === 'resultado'">
    <div class="grid gap-4 md:grid-cols-3">
        <label class="label">Estado global adoptado
            <select class="input" name="unit_constructions[<?= e($unitId) ?>][conservation_summary][global_adopted]">
                <option value="">Usar propuesta del sistema</option>
                <?php foreach ($conservationStates as $state): ?>
                    <?php $value = (string) ($state['value'] ?? ''); ?>
                    <option value="<?= e($value) ?>" <?= (string) ($conservationResult['state_global_adopted'] ?? '') === $value ? 'selected' : '' ?>><?= e($value . ' - ' . (string) ($state['label'] ?? '')) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="label md:col-span-2">Justificación si modifica la propuesta
            <input class="input" maxlength="1200" name="unit_constructions[<?= e($unitId) ?>][conservation_summary][change_justification]" value="<?= e((string) ($conservationResult['change_justification'] ?? '')) ?>" placeholder="Explica por qué adoptas un estado diferente o condicionado.">
        </label>
    </div>
    <?php if ($savedGroups): ?>
        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-100 text-xs uppercase text-slate-600">
                    <tr><th class="p-3">Grupo</th><th class="p-3">Factores</th><th class="p-3">Índice</th><th class="p-3">Peso global</th><th class="p-3">Estado</th><th class="p-3">Conclusión principal</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($savedGroups as $group): ?>
                        <tr>
                            <td class="p-3 font-semibold"><?= e((string) ($group['label'] ?? '')) ?></td>
                            <td class="p-3"><?= e(count($group['items'] ?? [])) ?></td>
                            <td class="p-3"><?= e(isset($group['score']) ? number_format((float) $group['score'], 2, ',', '') : 'Pendiente') ?></td>
                            <td class="p-3"><?= e(number_format((float) ($group['weight'] ?? 1), 2, ',', '')) ?></td>
                            <td class="p-3"><?= e(\App\Support\AppraisalConservationCatalog::stateLabel((string) ($group['state'] ?? ''))) ?></td>
                            <td class="p-3"><?= e((string) ($group['conclusion'] ?? '')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    <?php if ($conservationItems): ?>
        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-100 text-xs uppercase text-slate-600">
                    <tr><th class="p-3">Factor</th><th class="p-3">Grupo</th><th class="p-3">Calificación usada</th><th class="p-3">Peso</th><th class="p-3">Lectura del factor</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                <?php foreach ($conservationItems as $item): ?>
                    <?php
                    if (($item['applicability'] ?? '') === 'no_aplica') continue;
                    $used = (string) (($item['state_adopted'] ?? '') ?: ($item['state_proposed'] ?? ''));
                    ?>
                    <tr>
                        <td class="p-3 font-semibold"><?= e((string) ($item['subcomponent_label'] ?? '')) ?></td>
                        <td class="p-3"><?= e((string) ($item['group_label'] ?? '')) ?></td>
                        <td class="p-3"><?= e(\App\Support\AppraisalConservationCatalog::stateLabel($used)) ?></td>
                        <td class="p-3"><?= e(number_format((float) ($item['weight'] ?? 1), 2, ',', '')) ?></td>
                        <td class="p-3">Hallazgo: <?= e(\App\Support\AppraisalConservationCatalog::stateLabel((string) ($item['finding_state'] ?? ''))) ?> · Funcionalidad: <?= e(\App\Support\AppraisalConservationCatalog::stateLabel((string) ($item['functionality_floor'] ?? ''))) ?> · Intervención: <?= e(\App\Support\AppraisalConservationCatalog::stateLabel((string) ($item['intervention_state'] ?? ''))) ?>.</td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-4 text-sm leading-6 text-indigo-950">
        <strong>Método de cálculo:</strong> el estado sugerido del factor nace de hallazgo observable, funcionalidad e intervención aparente. Luego se calcula índice del grupo = suma(estado del factor × peso del factor) / suma(pesos). Pesos del factor: crítica 1,50; alta 1,25; media 1,00; baja 0,75. El global pondera grupos: estructura 2,00; instalaciones 1,50; envolvente 1,25; acabados y espacios funcionales 1,00; condiciones ambientales 0,75. Índice global actual: <strong><?= e(isset($conservationResult['score_global']) ? number_format((float) $conservationResult['score_global'], 2, ',', '') : 'pendiente') ?></strong>. Esta es una metodología interna de apoyo basada en la escala IGAC; el analista puede adoptar otro estado si lo justifica.
    </div>
    <div class="overflow-x-auto rounded-xl border border-amber-100 bg-amber-50/40">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-amber-100/70 text-xs uppercase text-amber-900">
                <tr><th class="p-3">Estado</th><th class="p-3">Criterio conceptual</th><th class="p-3">Contexto de uso</th></tr>
            </thead>
            <tbody class="divide-y divide-amber-100 bg-white/80">
                <?php foreach ($conservationStates as $state): ?>
                    <tr>
                        <td class="p-3 font-semibold text-slate-950"><?= e((string) ($state['value'] ?? '') . ' - ' . (string) ($state['label'] ?? '')) ?></td>
                        <td class="p-3"><?= e((string) ($state['criterion'] ?? '')) ?></td>
                        <td class="p-3"><?= e(trim((string) ($state['intervention'] ?? '') . ' ' . (string) ($state['use'] ?? ''))) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <label class="label">Texto aprobado para Entregable
        <textarea class="input min-h-40" rows="7" maxlength="8000" name="unit_constructions[<?= e($unitId) ?>][conservation_summary][approved_text]" data-conservation-approved data-conservation-auto="<?= $summaryConservationIsAuto ? '1' : '0' ?>" data-conservation-last-generated="<?= e($generatedConservationText) ?>" placeholder="El sistema propondrá el texto al diligenciar; puedes ajustarlo antes del entregable."><?= e($summaryConservationText) ?></textarea>
    </label>
    <div class="flex flex-wrap items-center gap-3">
        <button class="btn-secondary" type="button" data-conservation-regenerate>Regenerar texto con el nuevo estado</button>
        <p class="text-xs text-slate-600" data-conservation-summary-status>El texto se guarda con el autoguardado. Si lo editas manualmente, no se sobrescribe sin pulsar regenerar.</p>
    </div>
</section>
