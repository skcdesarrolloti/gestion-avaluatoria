<?php
$unassignedCount = count(\App\Services\MethodologyComparableScope::rows($allComparableRows, ''));
$treatments = ['separado' => 'Valor separado', 'integrado' => 'Incluido en otro componente', 'descriptivo' => 'Solo descriptivo'];
$propertyTypes = \App\Support\AppraisalCatalog::selectFields()['tipo_inmueble'][4];
$constructionTypes = \App\Support\AppraisalConstructionTypeCatalog::types();
$constructionTypes[''] = 'Por definir';
$sourceTreatments = \App\Support\AppraisalUnitValuationTreatmentCatalog::options();
?>
<section class="mt-6 rounded-2xl border bg-white p-5 sm:p-8">
    <h2 class="text-2xl font-semibold"><?= $stage === 'integration' ? 'Integración y control de cobertura' : 'Inmuebles y anexos del expediente' ?></h2>
    <p class="mt-3 text-slate-600">Estas son las mismas unidades y anexos registrados en el capítulo 1 y estudiados en el capítulo 3. Aquí se consultan para su análisis; los nombres, la composición y la tipología se actualizan en esos capítulos.</p>
    <div class="mt-4 flex flex-wrap gap-3">
        <a class="btn-secondary" href="<?= e(url('avaluos/' . $record['id'] . '/bien-sujeto?section=tipologias&from=metodologia#unidades-capitulo-3')) ?>">Ver capítulo 3 · Bien sujeto</a>
        <a class="btn-secondary" href="<?= e($flowUrl('3', 'mercado', '')) ?>">Banco sin asignar (<?= $unassignedCount ?>)</a>
    </div>
    <?php if ($components === []): ?><p role="status" class="mt-5 rounded-xl border border-dashed p-5">Todavía no hay unidades ni anexos registrados. Completa la composición del predio en el capítulo 1 y su estudio en el capítulo 3; aparecerán aquí al guardar.</p><?php endif; ?>
    <div class="mt-5 space-y-4">
    <?php foreach ($components as $key => $component):
        if ($stage !== 'integration' && $key !== ($componentKey ?: array_key_first($components))) continue;
        $item = $flow[$key] ?? [];
        $samples = \App\Services\MethodologyComparableScope::rows($allComparableRows, $key);
        $unit = $component['unit'];
        $type = \App\Services\ComparableSearchContext::record($record, $units, (string) $key)['tipo_inmueble'] ?? '';
    ?>
        <article class="rounded-xl border border-slate-200 p-5">
            <p class="text-sm text-slate-600"><?= ($unit['unit_kind'] ?? '') === 'annex' ? 'Anexo' : 'Inmueble' ?> · capítulos 1 y 3</p>
            <h3 class="text-lg font-semibold"><?= e($component['label']) ?></h3>
            <details class="mt-3"><summary class="min-h-11 cursor-pointer py-3 font-semibold">Características registradas en capítulos 1 y 3</summary>
            <dl class="mt-3 grid gap-3 text-sm sm:grid-cols-2 lg:grid-cols-3">
                <div><dt class="font-semibold">Tipo de inmueble</dt><dd><?= e($propertyTypes[$type] ?? ($type ?: 'Por definir')) ?></dd></div>
                <div><dt class="font-semibold">Tipo de construcción</dt><dd><?= e($constructionTypes[$unit['construction_type'] ?? ''] ?? 'Por definir') ?></dd></div>
                <div><dt class="font-semibold">Tipología IGAC registrada</dt><dd class="break-words"><?= e(($unit['igac_typology_hint'] ?? '') ?: 'Por definir') ?></dd></div>
                <div><dt class="font-semibold">Tratamiento registrado en el expediente</dt><dd><?= e($sourceTreatments[$unit['valuation_treatment'] ?? ''] ?? 'Por definir') ?></dd></div>
                <div><dt class="font-semibold">Estructura del método de esta unidad</dt><dd><?= e(\App\Support\UnitMethodStructure::options()[($unit['method_structure'] ?? '') ?: 'por_definir'] ?? 'Por definir') ?></dd></div>
            </dl></details>
            <?php if (!empty($unit['notes'])): ?><details class="mt-3"><summary class="min-h-11 cursor-pointer font-semibold">Descripción registrada</summary><p class="whitespace-pre-wrap break-words text-sm"><?= e($unit['notes']) ?></p></details><?php endif; ?>
            <p class="mt-2">Método: <strong><?= e($methods[$item['method'] ?? ''] ?? 'Por seleccionar') ?></strong></p>
            <p class="mt-2 text-sm"><?= e($treatments[$item['treatment'] ?? ''] ?? 'Tratamiento pendiente') ?> · <?= count($samples) ?> muestras asignadas</p>
            <p class="mt-2 whitespace-pre-wrap text-sm"><?= e($item['coverage'] ?? 'Falta definir el alcance y los elementos incluidos.') ?></p>
            <?php if ($stage === 'integration'): ?>
                <p class="mt-3 whitespace-pre-wrap text-sm"><?= e(($item['method'] ?? '') === 'mercado' ? ($item['conclusion'] ?? 'Conclusión pendiente.') : 'Desarrollo del método pendiente; no hay valor adoptado automáticamente.') ?></p>
                <?php if (($item['evidence_hash'] ?? '') !== \App\Services\MethodologyWorkflow::fingerprint($samples) && !empty($item['conclusion'])): ?>
                <p class="mt-2 font-semibold text-amber-800">Las muestras cambiaron: revisa la conclusión.</p>
                <?php endif; ?>
            <?php endif; ?>
            <?php if (in_array($stage, ['components', '1'], true)): ?>
                <?php require __DIR__ . '/methodology-unit-academy.php'; ?>
            <?php else: ?>
                <a class="btn-primary mt-4" href="<?= e($flowUrl('1', ($item['method'] ?? '') ?: 'mercado', $key)) ?>">Analizar <?= e($component['label']) ?> → Academia</a>
            <?php endif; ?>
        </article>
    <?php endforeach; ?>
    </div>
    <?php if ($stage === 'integration'): ?>
        <p class="mt-5 rounded-xl bg-amber-50 p-4">La integración conserva las conclusiones por componente. No presenta un total automático: los valores de Costo, Renta y Residual todavía están pendientes de desarrollo.</p>
    <?php endif; ?>
</section>
