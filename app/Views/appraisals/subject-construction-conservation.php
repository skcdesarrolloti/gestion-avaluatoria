<?php
$conservationGroups = \App\Support\AppraisalConservationCatalog::groups();
$conservationStates = \App\Support\AppraisalConservationCatalog::states();
$conservationInterventions = \App\Support\AppraisalConservationCatalog::interventions();
$conservationResult = json_decode((string) ($unit['conservation_result_json'] ?? '{}'), true);
$conservationResult = is_array($conservationResult) ? $conservationResult : [];
$conservationItems = is_array($conservationResult['items'] ?? null) ? $conservationResult['items'] : [];
$conservationValue = static function (string $subId, string $field) use ($conservationItems): string {
    return (string) ($conservationItems[$subId][$field] ?? '');
};
$legacyState = static function (string $subId) use ($jsonValue, $unit): string {
    $map = ['sistema_portante' => 'estructura', 'muros' => 'paredes', 'cielos_rasos' => 'cielorraso'];
    $old = $jsonValue($unit, 'construction_conservation_json', $map[$subId] ?? $subId);
    return ['B' => '2', 'R' => '3', 'M' => '4', 'NA' => ''][$old] ?? '';
};
$savedGroups = is_array($conservationResult['groups'] ?? null) ? $conservationResult['groups'] : [];
$firstConservationGroup = (string) ($conservationGroups[0]['id'] ?? 'resultado');
$approvedConservationText = trim((string) ($unit['conservation_approved_text'] ?? ''));
$generatedConservationText = trim((string) ($unit['conservation_generated_text'] ?? ''));
$summaryConservationText = $approvedConservationText !== '' ? $approvedConservationText : $generatedConservationText;
$summaryConservationIsAuto = $approvedConservationText === '' || ($generatedConservationText !== '' && $approvedConservationText === $generatedConservationText);
?>
<div class="mt-5 space-y-5" x-show="activeConstructionDetail === 'conservacion'"
    x-data="{ activeConservation: '<?= e($firstConservationGroup) ?>' }"
    data-conservation-panel>
    <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <p><strong>Referencia técnica:</strong>
                <a class="font-semibold underline decoration-blue-300 underline-offset-4" href="<?= e(\App\Support\IgacDocumentLibrary::urlFor('resolucion-igac-941-2026')) ?>">Resolución IGAC 941 de 2026</a>
                · <a class="font-semibold underline decoration-blue-300 underline-offset-4" href="<?= e(\App\Support\IgacDocumentLibrary::urlFor('in-gct-pc03-01-v2')) ?>">IN-GCT-PC03-01 V2</a>
                · <a class="font-semibold underline decoration-blue-300 underline-offset-4" href="<?= e(\App\Support\IgacDocumentLibrary::urlFor('in-gct-pc01-06-v1')) ?>">IN-GCT-PC01-06 V1</a>.
            </p>
            <a class="btn-secondary bg-white" href="<?= e(url('igac?documentos=conservacion')) ?>">Ver biblioteca IGAC</a>
        </div>
        <p class="mt-2 text-blue-900">Califica condición física observable, hallazgos, funcionalidad e intervención. Este numeral no calcula edad, vida útil, Ross-Heidecke ni depreciación.</p>
    </div>
    <div class="flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2">
        <?php foreach ($conservationGroups as $group): ?>
            <button class="min-h-10 shrink-0 rounded-lg px-3 py-2 text-xs font-semibold" type="button"
                @click="activeConservation = '<?= e((string) $group['id']) ?>'"
                :class="activeConservation === '<?= e((string) $group['id']) ?>' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:bg-white/70'">
                <?= e((string) $group['number']) ?> <?= e((string) $group['label']) ?>
            </button>
        <?php endforeach; ?>
        <button class="min-h-10 shrink-0 rounded-lg px-3 py-2 text-xs font-semibold" type="button"
            @click="activeConservation = 'resultado'"
            :class="activeConservation === 'resultado' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:bg-white/70'">
            7.7 Resultado
        </button>
    </div>
    <?php foreach ($conservationGroups as $group): ?>
        <section class="space-y-4" x-show="activeConservation === '<?= e((string) $group['id']) ?>'">
            <h4 class="text-sm font-semibold uppercase tracking-wide text-teal-800"><?= e((string) $group['number']) ?> <?= e((string) $group['label']) ?></h4>
            <?php foreach (($group['subcomponents'] ?? []) as $sub): ?>
                <?php
                $subId = (string) $sub['id'];
                $stateValue = $conservationValue($subId, 'state_adopted') ?: $legacyState($subId);
                ?>
                <div class="rounded-xl border border-slate-200 bg-white p-4" data-conservation-subcomponent data-conservation-group="<?= e((string) $group['label']) ?>" data-conservation-group-number="<?= e((string) $group['number']) ?>" data-conservation-label="<?= e((string) $sub['label']) ?>">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div><h5 class="font-semibold text-slate-950"><?= e((string) $sub['label']) ?></h5>
                            <p class="mt-1 max-w-3xl text-xs leading-5 text-slate-600"><?= e((string) $sub['object']) ?></p></div>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600"><?= e((string) $sub['criticality']) ?></span>
                    </div>
                    <div class="mt-4 grid gap-4 md:grid-cols-3">
                        <label class="label">Aplicabilidad
                            <select class="input" name="unit_constructions[<?= e($unitId) ?>][conservation_items][<?= e($subId) ?>][applicability]">
                                <?php foreach (['aplica' => 'Aplica', 'no_aplica' => 'No aplica', 'no_verificable' => 'No verificable'] as $value => $label): ?>
                                    <option value="<?= e($value) ?>" <?= ($conservationValue($subId, 'applicability') ?: 'aplica') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label class="label">Tipo / material
                            <select class="input" name="unit_constructions[<?= e($unitId) ?>][conservation_items][<?= e($subId) ?>][material]">
                                <option value="">No verificado</option>
                                <?php foreach (\App\Support\AppraisalConservationCatalog::materialsFor($subId) as $option): ?>
                                    <?php $value = (string) ($option['option'] ?? ''); ?>
                                    <option value="<?= e($value) ?>" <?= $conservationValue($subId, 'material') === $value ? 'selected' : '' ?>><?= e($value) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label class="label">Hallazgo observable
                            <select class="input" name="unit_constructions[<?= e($unitId) ?>][conservation_items][<?= e($subId) ?>][finding]">
                                <option value="">Sin hallazgo registrado</option>
                                <?php foreach (\App\Support\AppraisalConservationCatalog::findingsFor($subId) as $finding): ?>
                                    <?php $value = (string) ($finding['finding'] ?? ''); ?>
                                    <option value="<?= e($value) ?>" <?= $conservationValue($subId, 'finding') === $value ? 'selected' : '' ?>><?= e($value) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label class="label">Funcionalidad
                            <select class="input" name="unit_constructions[<?= e($unitId) ?>][conservation_items][<?= e($subId) ?>][functionality]">
                                <?php foreach (['' => 'No verificada', 'normal' => 'Normal', 'observaciones' => 'Funcional con observaciones', 'limitada' => 'Limitada', 'no_funcional' => 'No funcional'] as $value => $label): ?>
                                    <option value="<?= e($value) ?>" <?= $conservationValue($subId, 'functionality') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label class="label">Intervención aparente
                            <select class="input" name="unit_constructions[<?= e($unitId) ?>][conservation_items][<?= e($subId) ?>][intervention]">
                                <option value="">No definida</option>
                                <?php foreach ($conservationInterventions as $intervention): ?>
                                    <?php $value = (string) ($intervention['level'] ?? ''); ?>
                                    <option value="<?= e($value) ?>" <?= $conservationValue($subId, 'intervention') === $value ? 'selected' : '' ?>><?= e($value . ' - ' . (string) ($intervention['label'] ?? '')) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label class="label">Estado adoptado del elemento
                            <select class="input" name="unit_constructions[<?= e($unitId) ?>][conservation_items][<?= e($subId) ?>][state_adopted]">
                                <option value="">Proponer al guardar</option>
                                <?php foreach ($conservationStates as $state): ?>
                                    <?php $value = (string) ($state['value'] ?? ''); ?>
                                    <option value="<?= e($value) ?>" <?= $stateValue === $value ? 'selected' : '' ?>><?= e($value . ' - ' . (string) ($state['label'] ?? '')) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label class="label md:col-span-2">Observación técnica
                            <textarea class="input min-h-20" rows="2" maxlength="600" name="unit_constructions[<?= e($unitId) ?>][conservation_items][<?= e($subId) ?>][notes]" placeholder="Describe extensión, localización, causa aparente o salvedad."><?= e($conservationValue($subId, 'notes')) ?></textarea>
                        </label>
                        <label class="label">Evidencia
                            <input class="input" maxlength="180" name="unit_constructions[<?= e($unitId) ?>][conservation_items][<?= e($subId) ?>][evidence]" value="<?= e($conservationValue($subId, 'evidence')) ?>" placeholder="Foto, documento o inspección visual">
                        </label>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>
    <?php endforeach; ?>
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
                <table class="min-w-full text-left text-sm"><thead class="bg-slate-100 text-xs uppercase text-slate-600"><tr><th class="p-3">Grupo</th><th class="p-3">Estado</th><th class="p-3">Conclusión principal</th></tr></thead>
                    <tbody class="divide-y divide-slate-200"><?php foreach ($savedGroups as $group): ?><tr><td class="p-3 font-semibold"><?= e((string) ($group['label'] ?? '')) ?></td><td class="p-3"><?= e(\App\Support\AppraisalConservationCatalog::stateLabel((string) ($group['state'] ?? ''))) ?></td><td class="p-3"><?= e((string) ($group['conclusion'] ?? '')) ?></td></tr><?php endforeach; ?></tbody>
                </table>
            </div>
        <?php endif; ?>
        <label class="label">Texto aprobado para Entregable
            <textarea class="input min-h-40" rows="7" maxlength="8000" name="unit_constructions[<?= e($unitId) ?>][conservation_summary][approved_text]" data-conservation-approved data-conservation-auto="<?= $summaryConservationIsAuto ? '1' : '0' ?>" data-conservation-last-generated="<?= e($generatedConservationText) ?>" placeholder="El sistema propondrá el texto al diligenciar; puedes ajustarlo antes del entregable."><?= e($summaryConservationText) ?></textarea>
        </label>
        <div class="flex flex-wrap items-center gap-3">
            <button class="btn-secondary" type="button" data-conservation-regenerate>Regenerar texto con el nuevo estado</button>
            <p class="text-xs text-slate-600" data-conservation-summary-status>El texto se guarda con el autoguardado. Si lo editas manualmente, no se sobrescribe sin pulsar regenerar.</p>
        </div>
    </section>
</div>
