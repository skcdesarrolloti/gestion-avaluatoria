<?php
$selectedGroups = \App\Services\ComparableIntake::groups($comparableRows, true);
$intakeGroups = array_filter(\App\Services\ComparableIntake::groups($comparableRows), static fn($group,$key) => isset($selectedGroups[$key]) || count(array_filter($group, static fn($row)=>($row['capture_confirmation'] ?? '')==='confirmed'))===count($group), ARRAY_FILTER_USE_BOTH);
$analysisRows = array_values(array_map(static function($group) { foreach ($group as $row) if (($row['research_primary'] ?? '')==='si') return $row; return $group[0]; },$intakeGroups));
$analysisContext=isset($units) ? \App\Services\ComparableSearchContext::record($record,$units,$componentKey) : $record;
$analysisRegime=in_array($analysisContext['regimen_ph'] ?? '',['si','no'],true) ? $analysisContext['regimen_ph'] : '';
foreach ($analysisRows as &$analysisRow) $analysisRow['regime_hint']=\App\Services\ComparableRegimeSuggestion::hint($analysisRow);
unset($analysisRow);
$analysisIndexes = array_column($analysisRows,'capture_index');
$analysisSubjectLocation=json_encode(array_intersect_key($subject ?? [],array_flip(['latitude','longitude'])),JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT|JSON_THROW_ON_ERROR);
?>
<details class="mt-4 rounded-xl border p-4" open>
    <summary class="min-h-11 cursor-pointer font-semibold">Tabla de análisis · <?= count($intakeGroups) ?> inmuebles recogidos</summary>
    <?php if ($intakeGroups === []): ?>
    <?php require __DIR__.'/methodology-analysis-preparation.php'; ?>
    <p class="mt-3 rounded-lg bg-amber-50 p-3">Selecciona inmuebles en Insumos para estudiarlos aquí. Los anuncios restantes siguen conservados.</p><?php else: ?>
    <form data-market-analysis class="mt-4" x-data="marketAnalysisTable(<?= e(json_encode($analysisRows,JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT|JSON_THROW_ON_ERROR)) ?>, '<?= e($analysisRegime) ?>', <?= e(json_encode($analysisContext['tipo_inmueble'] ?? '',JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT|JSON_THROW_ON_ERROR)) ?>)" method="post" action="<?= e(url($basePath . '/comparables')) ?>" data-module-autosave data-save-in-place data-comparable-json
        x-init="analysisModule='preparation'" data-autosave-endpoint="<?= e(url($basePath . '/comparables/autoguardar')) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="component_scope" value="<?= e($componentKey) ?>">
        <input type="hidden" name="version" value="<?= (int) ($record['comparables_version'] ?? 0) ?>">
        <input type="hidden" name="matrix_complete" value="1">
        <?php $locationKeys = ['latitude','longitude','location_verification','verification_detail','location_source'];
        $locationIndexes = $analysisIndexes;
        foreach ($comparableRows as $index=>$row):
            if (empty($row['published_location']) && empty($row['location_verification']) && ($row['latitude'] ?? '') !== '' && ($row['longitude'] ?? '') !== '')
                $row['published_location'] = $row['latitude'] . ', ' . $row['longitude'] . ' · referencia anterior sin verificar.';
            foreach ($row as $key=>$value):
            if (!is_scalar($value) || ($key === 'capture_index') || (in_array($key,['negotiation_discount','ph_regime','ph_regime_source','analysis_manual_factors'],true) && in_array($index,$analysisIndexes,true)) || ($key==='analysis_factor_selection' && $index===$analysisIndexes[0]) || (in_array($index, $locationIndexes, true) && in_array($key, $locationKeys, true))) continue; ?>
            <input type="hidden" name="comparables[<?= $index ?>][<?= e($key) ?>]" value="<?= e((string) $value) ?>">
        <?php endforeach; endforeach; ?>
        <?php foreach ($analysisRows as $row): ?><input type="hidden" name="comparables[<?= (int)$row['capture_index'] ?>][negotiation_discount]" value="<?= e((string)($row['negotiation_discount'] ?? '')) ?>" :value="analysisDiscounts['<?= e($row['id']) ?>']"><?php endforeach; ?>
        <?php foreach ($analysisRows as $position=>$row): foreach (['ph_regime','ph_regime_source','analysis_manual_factors'] as $field): ?><input type="hidden" name="comparables[<?= (int)$row['capture_index'] ?>][<?= $field ?>]" :value="analysisRows[<?= $position ?>].<?= $field ?>"><?php endforeach; endforeach; ?>
        <input type="hidden" name="comparables[<?= (int)$analysisIndexes[0] ?>][analysis_factor_selection]" :value="analysisSelection()">
        <div class="sm:sticky sm:top-0 z-20 mb-4 rounded-xl border border-teal-700 bg-white p-3 shadow-sm">
            <p class="mb-3 font-semibold text-teal-900" role="status" aria-live="polite">Estás en: M4 Análisis<?= !empty($componentLabel) ? ' · '.e($componentLabel) : '' ?> → <span x-text="analysisModule==='preparation' ? 'Preparar los datos → Objetivo y unidad de análisis' : analysisModule==='location' ? 'Preparar los datos → Coordenadas y mapa comparativo' : analysisModule==='statistics' ? 'Análisis estadístico → '+courseSteps[courseStep] : analysisModule==='regression' ? 'Modelo de regresión → '+({academy:'Academia y fórmulas',application:'Preparar y calcular',diagnostics:'Gráficos y datos atípicos'}[regressionTab]) : 'Preparar los datos → Muestras y depuración → '+({raw:'Información recogida',regime:'Depurar muestras',clean:'Factores del analista',result:'Resultado depurado'}[analysisView] || 'Información recogida')"></span></p>
            <nav class="flex flex-wrap gap-2" aria-label="Submenú del análisis">
                <button type="button" class="btn-secondary" :aria-current="['preparation','samples','location'].includes(analysisModule)?'page':null" :class="['preparation','samples','location'].includes(analysisModule)?'bg-teal-50 ring-2 ring-teal-700 font-bold':''" @click="analysisModule='preparation'">1. Preparar los datos</button>
                <button type="button" class="btn-secondary" :aria-current="analysisModule==='statistics'?'page':null" :class="analysisModule==='statistics'?'bg-teal-50 ring-2 ring-teal-700 font-bold':''" @click="analysisModule='statistics'">2. Análisis estadístico paso a paso</button>
                <button type="button" class="btn-secondary" :aria-current="analysisModule==='regression'?'page':null" :class="analysisModule==='regression'?'bg-teal-50 ring-2 ring-teal-700 font-bold':''" @click="analysisModule='regression'">3. Modelo de regresión</button>
            </nav>
            <?php require __DIR__.'/methodology-analysis-preparation-nav.php'; ?>
            <template x-if="analysisModule==='samples'&&analysisReviewReturn"><div class="mt-3 rounded-lg border bg-teal-50 p-3 space-y-2" role="status">
                <p><strong x-text="'Estás revisando: '+analysisReviewLabel()"></strong><span x-text="' · Venías de: '+analysisReviewOrigin()"></span></p>
                <p class="text-sm">Revisa o completa el inmueble en la tabla. Después regresa al mismo paso; si cambias datos, actualiza el cálculo allí.</p>
                <button type="button" class="btn-primary" @click="await analysisReturnFromReview()" x-text="'Volver a '+analysisReviewOrigin()"></button>
            </div></template>
        </div>
        <template x-if="analysisModule==='preparation'"><div><?php require __DIR__.'/methodology-analysis-preparation.php'; ?></div></template>
        <template x-if="analysisModule==='samples'"><div>
        <?php require __DIR__.'/methodology-analysis-data-table.php'; ?>
        <details class="mt-4"><summary class="min-h-11 cursor-pointer font-semibold">Ubicación y anuncios originales</summary>
        <?php foreach ($intakeGroups as $group): $row = $group[0]; $index = $row['capture_index']; ?>
        <article class="mt-4 rounded-xl border p-4">
            <h3 class="font-semibold"><?= e(($row['project_name'] ?? '') ?: ($row['property_type'] ?? 'Inmueble')) ?> · <?= e($row['neighborhood'] ?? '') ?></h3>
            <?php foreach ($group as $announcement): ?>
                <p class="mt-2 text-sm"><?= e($announcement['source_name'] ?? '') ?> · código <?= e($announcement['listing_code'] ?? '') ?> · oferta <?= e((string) ($announcement['price_amount'] ?? '')) ?> COP · área <?= e((string) ($announcement['area_m2'] ?? '')) ?> m²</p>
                <p class="whitespace-pre-wrap text-sm text-amber-900"><?= e($announcement['intake_note'] ?? '') ?></p>
                <?php require __DIR__ . '/methodology-intake-source-detail.php'; ?>
            <?php endforeach; ?>
        </article>
        <?php endforeach; ?>
        </details>
        </div></template>
        <?php foreach ($analysisRows as $position=>$locationRow): foreach ($locationKeys as $field): ?>
        <input type="hidden" name="comparables[<?= (int)$locationRow['capture_index'] ?>][<?= e($field) ?>]" :value="analysisRows[<?= $position ?>].<?= $field ?> || ''">
        <?php endforeach; endforeach; ?>
        <?php require __DIR__.'/methodology-analysis-location.php'; ?>
        <?php require __DIR__.'/methodology-analysis-course.php'; ?>
        <?php require __DIR__.'/methodology-analysis-regression.php'; ?>
        <p class="mt-3 text-sm" data-autosave-status>Autoguardado activo. Espera el guardado confirmado antes de exportar.</p>
        <button class="btn-primary mt-3" type="submit">Guardar análisis de muestras</button>
    </form>
    <?php endif; ?>
</details>
