<?php
$configTabs = [
    'expediente' => 'Perito y expediente',
    'metodo' => 'Negocio y tipología',
    'renta' => 'Renta',
    'niif' => 'NIIF y PH',
    'notas' => 'Notas',
];
$unitDefinitionUnits = array_values(array_filter($units ?? [], static fn (array $unit): bool => ($unit['unit_kind'] ?? '') !== 'common'));
$igacCategories = $igacCategories ?? [];
$igacTypologiesByCategory = $igacTypologiesByCategory ?? [];
$constructionTypes = \App\Support\AppraisalConstructionTypeCatalog::types();
$valuationTreatments = \App\Support\AppraisalUnitValuationTreatmentCatalog::options();
$unitDisplay = static function (array $unit): string {
    $fallback = ($unit['unit_kind'] ?? '') === 'annex' ? 'Anexo ' : 'Unidad ';
    return trim((string) ($unit['label'] ?? '')) ?: $fallback . (int) ($unit['unit_index'] ?? 0);
};
$treatmentValue = static function (array $unit): string {
    return (string) (($unit['valuation_treatment'] ?? '')
        ?: \App\Support\AppraisalUnitValuationTreatmentCatalog::defaultFor((string) ($unit['unit_kind'] ?? ''), (string) ($unit['construction_type'] ?? '')));
};
$igacCategoryValue = static fn (array $unit): string =>
    \App\Support\AppraisalConstructionTypeCatalog::igacCategoryFor((string) ($unit['construction_type'] ?? ''))
        ?: (($unit['unit_kind'] ?? '') === 'annex' ? 'ANEXOS' : (string) ($unit['igac_category'] ?? ''));
$igacOptionsFor = static fn (string $category): array => $igacTypologiesByCategory[$category] ?? [];
$igacSearchPlaceholder = static fn (array $unit): string =>
    ($unit['unit_kind'] ?? '') === 'annex' ? 'Buscar anexo IGAC: piscina, depósito, kiosco...' : 'Buscar tipología IGAC de la unidad principal';
?>
<nav class="flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" aria-label="Bloques del numeral 1.1">
    <?php foreach ($configTabs as $key => $label): ?>
        <button type="button" class="min-h-10 shrink-0 rounded-lg px-3 py-2 text-xs font-semibold"
            :class="configTab === '<?= e($key) ?>' ? 'bg-blue-700 text-white shadow-sm' : 'bg-white text-blue-800'"
            @click="configTab = '<?= e($key) ?>'"><?= e($label) ?></button>
    <?php endforeach; ?>
</nav>
<div class="mt-4 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950">
    Diligencia un bloque a la vez. Esta configuración crea y describe las unidades desde 1.1; los numerales 3 y 8 la toman como base.
</div>

<div class="mt-5 grid gap-5 md:grid-cols-2" x-show="configTab === 'expediente'">
    <?php require __DIR__ . '/chapter-zero-responsible.php'; ?>
    <?php require BASE_PATH . '/app/Views/appraisals/appraisal-dossier-field.php'; ?>
</div>

<?php require __DIR__ . '/chapter-zero-composition.php'; ?>

<div class="mt-5 grid gap-5 md:grid-cols-2" x-show="configTab === 'renta'" x-cloak>
    <?php require BASE_PATH . '/app/Views/appraisals/chapter-zero-income-fields.php'; ?>
</div>

<div class="mt-5 grid gap-5 md:grid-cols-2" x-show="configTab === 'niif'" x-cloak>
    <?php foreach (['aplica_niif', 'regimen_ph'] as $name) {
        require BASE_PATH . '/app/Views/appraisals/chapter-zero-select-field.php';
    } ?>
</div>

<div class="mt-5 grid gap-5 md:grid-cols-2" x-show="configTab === 'notas'" x-cloak>
    <label class="label md:col-span-2">Notas de inspección y configuración
        <textarea class="input" name="inspection_notes" rows="4" maxlength="2000"
            placeholder="Anota dudas de campo, componentes del bien o alertas técnicas."><?= e($field('inspection_notes')) ?></textarea>
    </label>
</div>
