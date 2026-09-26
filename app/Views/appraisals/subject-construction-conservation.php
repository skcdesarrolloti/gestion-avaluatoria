<?php
$conservationGroups = \App\Support\AppraisalConservationCatalog::groups();
$conservationStates = \App\Support\AppraisalConservationCatalog::states();
$conservationInterventions = \App\Support\AppraisalConservationCatalog::interventions();
$conservationFunctionalities = \App\Support\AppraisalConservationCatalog::functionalities();
$conservationFactorTotal = array_sum(array_map(static fn (array $group): int => count($group['subcomponents'] ?? []), $conservationGroups));

$stateDefinition = static function (string $value) use ($conservationStates): array {
    foreach ($conservationStates as $state) {
        if ((string) ($state['value'] ?? '') === $value) return $state;
    }
    return [];
};
$stateDefinitionText = static function (array $state): string {
    if (!$state) return 'Selecciona un estado para ver el criterio técnico que sustentará la valoración.';
    return trim(($state['value'] ?? '') . ' - ' . ($state['label'] ?? '') . ': '
        . ($state['criterion'] ?? '') . ' ' . ($state['intervention'] ?? '') . ' ' . ($state['use'] ?? ''));
};
$functionalityDefinitionText = static fn (string $value): string =>
    (string) (($conservationFunctionalities[$value] ?? [])['scope'] ?? 'Selecciona funcionalidad para ver su alcance técnico.');
$interventionDefinitionText = static function (string $value) use ($conservationInterventions): string {
    foreach ($conservationInterventions as $row) {
        if ((string) ($row['level'] ?? '') === $value) {
            return trim(($row['description'] ?? '') . ' Ejemplos: ' . ($row['examples'] ?? '') . ' Relación orientativa: ' . ($row['state_relation'] ?? ''));
        }
    }
    return 'Selecciona intervención aparente para ver su alcance técnico.';
};

$conservationResult = json_decode((string) ($unit['conservation_result_json'] ?? '{}'), true);
$conservationResult = is_array($conservationResult) ? $conservationResult : [];
$conservationItems = is_array($conservationResult['items'] ?? null) ? $conservationResult['items'] : [];
$conservationValue = static fn (string $subId, string $field): string => (string) ($conservationItems[$subId][$field] ?? '');
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
<div class="mt-5 space-y-5" x-show="activeConstructionDetail === 'conservacion'" x-data="{ activeConservation: '<?= e($firstConservationGroup) ?>' }"
    data-conservation-state-definitions='<?= e(json_encode($conservationStates, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_THROW_ON_ERROR)) ?>'
    data-conservation-functionality-definitions='<?= e(json_encode($conservationFunctionalities, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_THROW_ON_ERROR)) ?>'
    data-conservation-intervention-definitions='<?= e(json_encode($conservationInterventions, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_THROW_ON_ERROR)) ?>'
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
                <span class="ml-2 rounded-full bg-white/80 px-2 py-0.5 text-[11px] text-slate-500"><?= e(count($group['subcomponents'] ?? [])) ?> factor(es)</span>
            </button>
        <?php endforeach; ?>
        <button class="min-h-10 shrink-0 rounded-lg px-3 py-2 text-xs font-semibold" type="button"
            @click="activeConservation = 'resultado'"
            :class="activeConservation === 'resultado' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:bg-white/70'">
            7.7 Resultado
            <span class="ml-2 rounded-full bg-white/80 px-2 py-0.5 text-[11px] text-slate-500"><?= e($conservationFactorTotal) ?> factor(es)</span>
        </button>
    </div>
    <?php foreach ($conservationGroups as $group): ?>
        <section class="space-y-4" x-show="activeConservation === '<?= e((string) $group['id']) ?>'">
            <h4 class="text-sm font-semibold uppercase tracking-wide text-teal-800"><?= e((string) $group['number']) ?> <?= e((string) $group['label']) ?></h4>
            <?php foreach (($group['subcomponents'] ?? []) as $sub): ?>
                <?php require BASE_PATH . '/app/Views/appraisals/subject-construction-conservation-card.php'; ?>
            <?php endforeach; ?>
        </section>
    <?php endforeach; ?>
    <?php require BASE_PATH . '/app/Views/appraisals/subject-construction-conservation-result.php'; ?>
</div>
