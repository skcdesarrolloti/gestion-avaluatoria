<?php
$selectedGroups = \App\Services\ComparableIntake::groups($comparableRows, true);
$intakeGroups = array_filter(\App\Services\ComparableIntake::groups($comparableRows), static fn($group,$key) => isset($selectedGroups[$key]) || count(array_filter($group, static fn($row)=>($row['capture_confirmation'] ?? '')==='confirmed'))===count($group), ARRAY_FILTER_USE_BOTH);
$analysisRows = array_values(array_map(static function($group) { foreach ($group as $row) if (($row['research_primary'] ?? '')==='si') return $row; return $group[0]; },$intakeGroups));
$analysisContext=isset($units) ? \App\Services\ComparableSearchContext::record($record,$units,$componentKey) : $record;
$analysisRegime=in_array($analysisContext['regimen_ph'] ?? '',['si','no'],true) ? $analysisContext['regimen_ph'] : '';
foreach ($analysisRows as &$analysisRow) $analysisRow['regime_hint']=\App\Services\ComparableRegimeSuggestion::hint($analysisRow);
unset($analysisRow);
$analysisIndexes = array_column($analysisRows,'capture_index');
?>
<details class="mt-4 rounded-xl border p-4" open>
    <summary class="min-h-11 cursor-pointer font-semibold">Tabla de análisis · <?= count($intakeGroups) ?> inmuebles recogidos</summary>
    <?php if ($intakeGroups === []): ?><p class="mt-3 rounded-lg bg-amber-50 p-3">Selecciona inmuebles en Insumos para estudiarlos aquí. Los anuncios restantes siguen conservados.</p><?php else: ?>
    <form class="mt-4" x-data="marketAnalysisTable(<?= e(json_encode($analysisRows,JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT|JSON_THROW_ON_ERROR)) ?>, '<?= e($analysisRegime) ?>', <?= e(json_encode($analysisContext['tipo_inmueble'] ?? '',JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT|JSON_THROW_ON_ERROR)) ?>)" method="post" action="<?= e(url($basePath . '/comparables')) ?>" data-module-autosave data-save-in-place data-comparable-json
        data-autosave-endpoint="<?= e(url($basePath . '/comparables/autoguardar')) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="component_scope" value="<?= e($componentKey) ?>">
        <input type="hidden" name="version" value="<?= (int) ($record['comparables_version'] ?? 0) ?>">
        <input type="hidden" name="matrix_complete" value="1">
        <?php $locationKeys = ['latitude','longitude','location_verification','verification_detail','location_source'];
        $locationIndexes = array_map(static fn ($group) => $group[0]['capture_index'], $intakeGroups);
        foreach ($comparableRows as $index=>$row):
            if (empty($row['published_location']) && empty($row['location_verification']) && ($row['latitude'] ?? '') !== '' && ($row['longitude'] ?? '') !== '')
                $row['published_location'] = $row['latitude'] . ', ' . $row['longitude'] . ' · referencia anterior sin verificar.';
            foreach ($row as $key=>$value):
            if (!is_scalar($value) || ($key === 'capture_index') || (in_array($key,['negotiation_discount','ph_regime','ph_regime_source'],true) && in_array($index,$analysisIndexes,true)) || ($key==='analysis_factor_selection' && $index===$analysisIndexes[0]) || (in_array($index, $locationIndexes, true) && in_array($key, $locationKeys, true))) continue; ?>
            <input type="hidden" name="comparables[<?= $index ?>][<?= e($key) ?>]" value="<?= e((string) $value) ?>">
        <?php endforeach; endforeach; ?>
        <?php foreach ($analysisRows as $row): ?><input type="hidden" name="comparables[<?= (int)$row['capture_index'] ?>][negotiation_discount]" value="<?= e((string)($row['negotiation_discount'] ?? '')) ?>" :value="analysisDiscounts['<?= e($row['id']) ?>']"><?php endforeach; ?>
        <?php foreach ($analysisRows as $position=>$row): foreach (['ph_regime','ph_regime_source'] as $field): ?><input type="hidden" name="comparables[<?= (int)$row['capture_index'] ?>][<?= $field ?>]" :value="analysisRows[<?= $position ?>].<?= $field ?>"><?php endforeach; endforeach; ?>
        <input type="hidden" name="comparables[<?= (int)$analysisIndexes[0] ?>][analysis_factor_selection]" :value="analysisSelection()">
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
            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                <?php foreach (['latitude'=>'Latitud confirmada','longitude'=>'Longitud confirmada'] as $key=>$label): ?>
                <label class="label"><?= e($label) ?><input class="input" name="comparables[<?= $index ?>][<?= e($key) ?>]" value="<?= e((string) ($row[$key] ?? '')) ?>" placeholder="<?= $key === 'latitude' ? '10.400000' : '-75.550000' ?>"></label>
                <?php endforeach; ?>
                <label class="label">Precisión verificada manualmente<select class="input" name="comparables[<?= $index ?>][location_verification]">
                    <?php foreach (\App\Services\ComparableCaptureDetail::options('location_verification') as $value=>$label): ?><option value="<?= e($value) ?>" <?= ($row['location_verification'] ?? '') === $value ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?>
                </select></label>
                <label class="label">Fuente de ubicación<input class="input" name="comparables[<?= $index ?>][location_source]" value="<?= e($row['location_source'] ?? '') ?>" placeholder="Dirección confirmada, visita, documento o informante"></label>
            </div>
            <label class="label mt-3">Verificación: responsable, fecha y soporte<textarea class="input" rows="2" maxlength="1600" name="comparables[<?= $index ?>][verification_detail]" placeholder="Quién confirmó el punto, cuándo y con qué evidencia"><?= e($row['verification_detail'] ?? '') ?></textarea></label>
            <?php if (in_array($row['location_verification'] ?? '', ['exact','approximate'], true) && ($row['verification_detail'] ?? '') !== '' && ($row['location_source'] ?? '') !== '' && is_numeric($row['latitude'] ?? '') && is_numeric($row['longitude'] ?? '')):
                $mapCoordinates = (float) $row['latitude'] . ',' . (float) $row['longitude']; ?>
                <details class="mt-3" data-verified-map><summary class="min-h-11 cursor-pointer text-blue-700">Mapa · <?= ($row['location_verification'] ?? '') === 'exact' ? 'punto exacto verificado' : 'referencia aproximada verificada' ?></summary>
                    <iframe loading="lazy" class="mt-2 h-64 w-full rounded-lg border" title="Ubicación verificada manualmente" referrerpolicy="no-referrer" src="<?= e('https://maps.google.com/maps?q=' . rawurlencode($mapCoordinates) . '&output=embed') ?>"></iframe>
                </details>
            <?php else: ?><p class="mt-3 text-sm text-amber-900">Ubicación pendiente de verificación manual y soporte. No se muestra un punto como confirmado.</p><?php endif; ?>
        </article>
        <?php endforeach; ?>
        </details>
        <p class="mt-3 text-sm" data-autosave-status>Autoguardado activo. Actualiza esta vista después de confirmar el guardado para consultar el mapa.</p>
        <button class="btn-primary mt-3" type="submit">Guardar análisis de muestras</button>
    </form>
    <?php endif; ?>
</details>
