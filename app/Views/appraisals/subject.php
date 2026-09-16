<?php
$selected = static fn (string $name, string $value): string => (string) ($record[$name] ?? '') === $value ? 'selected' : '';
$field = static fn (string $name): string => (string) ($record[$name] ?? '');
$count = static fn (string $name): int => max(0, (int) ($record[$name] ?? 0));
$currentStep = 'sujeto';
$subjectActionBase = 'avaluos/' . $record['id'] . '/bien-sujeto';
?>
<a href="<?= e(url('valuaciones')) ?>" class="inline-flex min-h-11 items-center text-sm font-medium text-teal-800">← Valuaciones</a>
<div class="mt-3 flex flex-wrap items-start justify-between gap-5">
    <div>
        <p class="eyebrow">Bien sujeto</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight">Características y tipologías del inmueble</h1>
        <p class="mt-3 max-w-3xl text-slate-600">
            Aquí se documentan las unidades y anexos del predio, sus tipologías IGAC, fotos y
            descripciones editables para alimentar el informe.
        </p>
    </div>
    <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700">Sujeto del avalúo</span>
</div>
<?php require BASE_PATH . '/app/Views/appraisals/step-nav.php'; ?>

<div class="mt-7 space-y-7" x-data="{
    typologyHint: <?= e(json_encode($field('igac_typology_hint'), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
    igacCategory: <?= e(json_encode($field('igac_category'), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
    propertyUnits: <?= e((string) $count('igac_property_units_count')) ?>,
    annexUnits: <?= e((string) $count('igac_annex_units_count')) ?>
}">
    <?php require BASE_PATH . '/app/Views/appraisals/preclassification.php'; ?>
    <?php require BASE_PATH . '/app/Views/appraisals/unit-tabs.php'; ?>
</div>
