<?php
$subId = (string) $sub['id'];
$stateValue = $conservationValue($subId, 'state_adopted') ?: $legacyState($subId);
?>
<div class="rounded-xl border border-slate-200 bg-white p-4" data-conservation-subcomponent
    data-conservation-group="<?= e((string) $group['label']) ?>"
    data-conservation-group-id="<?= e((string) $group['id']) ?>"
    data-conservation-group-number="<?= e((string) $group['number']) ?>"
    data-conservation-criticality="<?= e((string) $sub['criticality']) ?>"
    data-conservation-label="<?= e((string) $sub['label']) ?>">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h5 class="font-semibold text-slate-950"><?= e((string) $sub['label']) ?></h5>
            <p class="mt-1 max-w-3xl text-xs leading-5 text-slate-600"><?= e((string) $sub['object']) ?></p>
        </div>
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
                <?php foreach ($conservationFunctionalities as $value => $definition): ?>
                    <option value="<?= e((string) $value) ?>" <?= $conservationValue($subId, 'functionality') === (string) $value ? 'selected' : '' ?>><?= e((string) ($definition['label'] ?? '')) ?></option>
                <?php endforeach; ?>
            </select>
            <span class="mt-2 block rounded-md border border-blue-100 bg-blue-50 p-3 text-xs leading-5 text-blue-950" data-conservation-functionality-definition><?= e($functionalityDefinitionText($conservationValue($subId, 'functionality'))) ?></span>
        </label>
        <label class="label">Intervención aparente
            <select class="input" name="unit_constructions[<?= e($unitId) ?>][conservation_items][<?= e($subId) ?>][intervention]">
                <option value="">No definida</option>
                <?php foreach ($conservationInterventions as $intervention): ?>
                    <?php $value = (string) ($intervention['level'] ?? ''); ?>
                    <option value="<?= e($value) ?>" <?= $conservationValue($subId, 'intervention') === $value ? 'selected' : '' ?>><?= e($value . ' - ' . (string) ($intervention['label'] ?? '')) ?></option>
                <?php endforeach; ?>
            </select>
            <span class="mt-2 block rounded-md border border-teal-100 bg-teal-50 p-3 text-xs leading-5 text-teal-950" data-conservation-intervention-definition><?= e($interventionDefinitionText($conservationValue($subId, 'intervention'))) ?></span>
        </label>
        <label class="label">Estado adoptado del elemento
            <select class="input" name="unit_constructions[<?= e($unitId) ?>][conservation_items][<?= e($subId) ?>][state_adopted]">
                <option value="">Proponer al guardar</option>
                <?php foreach ($conservationStates as $state): ?>
                    <?php $value = (string) ($state['value'] ?? ''); ?>
                    <option value="<?= e($value) ?>" <?= $stateValue === $value ? 'selected' : '' ?>><?= e($value . ' - ' . (string) ($state['label'] ?? '')) ?></option>
                <?php endforeach; ?>
            </select>
            <span class="mt-2 block rounded-md border border-amber-100 bg-amber-50 p-3 text-xs leading-5 text-amber-950" data-conservation-state-definition><?= e($stateDefinitionText($stateDefinition($stateValue))) ?></span>
        </label>
        <div class="rounded-md border border-slate-200 bg-slate-50 p-3 text-xs leading-5 text-slate-700 md:col-span-3">
            <strong>Lectura asistida:</strong> el hallazgo observable da la base; la funcionalidad fija un piso de coherencia; la intervención aparente sirve como contraste técnico. El estado adoptado por el analista prevalece y queda sustentado con observación y evidencia.
        </div>
        <label class="label md:col-span-2">Observación técnica
            <textarea class="input min-h-20" rows="2" maxlength="600" name="unit_constructions[<?= e($unitId) ?>][conservation_items][<?= e($subId) ?>][notes]" placeholder="Describe extensión, localización, causa aparente o salvedad."><?= e($conservationValue($subId, 'notes')) ?></textarea>
        </label>
        <label class="label">Evidencia
            <input class="input" maxlength="180" name="unit_constructions[<?= e($unitId) ?>][conservation_items][<?= e($subId) ?>][evidence]" value="<?= e($conservationValue($subId, 'evidence')) ?>" placeholder="Foto, documento o inspección visual">
        </label>
    </div>
</div>
