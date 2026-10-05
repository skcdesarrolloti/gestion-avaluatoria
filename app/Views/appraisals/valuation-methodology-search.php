<?php
$portalFields = is_array($guide['portal_fields'] ?? null) ? $guide['portal_fields'] : [];
$portalFilters = is_array($guide['portal_filters'] ?? null) ? $guide['portal_filters'] : [];
$sourceSearch = is_array($guide['source_search'] ?? null) ? $guide['source_search'] : [];
$portalSources = is_array($sourceSearch['portal_sources'] ?? null) ? $sourceSearch['portal_sources'] : [];
$agencySources = is_array($sourceSearch['agency_sources'] ?? null) ? $sourceSearch['agency_sources'] : [];
$officialSources = is_array($sourceSearch['official_sources'] ?? null) ? $sourceSearch['official_sources'] : [];
$captureProtocol = is_array($sourceSearch['capture_protocol'] ?? null) ? $sourceSearch['capture_protocol'] : [];
$adjustments = is_array($guide['adjustments'] ?? null) ? $guide['adjustments'] : (is_array($guide['homologation'] ?? null) ? $guide['homologation'] : []);
$sampleDesign = is_array($guide['sample_design'] ?? null) ? $guide['sample_design'] : [];
$factorTargets = is_array($sampleDesign['factor_targets'] ?? null) ? $sampleDesign['factor_targets'] : [];
$factorOptions = ['' => 'Seleccionar factor'];
foreach ($factorTargets as $target) $factorOptions[(string) ($target['key'] ?? '')] = (string) ($target['label'] ?? 'Factor');
$nextStep = is_array($methodologyDecision['next_step'] ?? null) ? $methodologyDecision['next_step'] : ['M3 Desarrollo del método', ''];
$formulaFamilies = [
    ['Mercado', 'Valor unitario = precio depurado / unidad de comparación. En M4 se revisa mediana, media recortada, dispersión, intervalo t de Student, outliers y MAPE si hay modelo.'],
    ['Renta', 'Ingreso neto = canon bruto menos vacancia, administración no recuperable y gastos; valor = ingreso neto anual / tasa, o flujo descontado si aplica.'],
    ['Residual', 'Valor del suelo = ingresos esperados del producto menos costos directos, indirectos, financieros, utilidad, tiempos y riesgos del desarrollo.'],
    ['Costo', 'Valor = terreno + costo de reposición nuevo menos depreciación física, funcional y económica, con soporte de cantidades y precios.'],
];
?>
<section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8" x-data="{ sourcePortal: '', intakeStep: 'review', supportTab: 'context', searchTab: 'captura' }">
    <nav class="mb-3 flex flex-wrap gap-2 rounded-xl bg-slate-100 p-2" aria-label="Pasos de la investigación">
        <button type="button" class="btn-secondary" :aria-pressed="searchTab==='captura'" :class="(searchTab==='captura') ? 'ring-2 ring-teal-700 bg-teal-50' : ''" @click="searchTab='captura'">1. Buscar por portal</button>
        <button type="button" class="btn-secondary" :aria-pressed="searchTab==='matriz' && intakeStep==='review'" :class="(searchTab==='matriz' && intakeStep==='review') ? 'ring-2 ring-teal-700 bg-teal-50' : ''" @click="searchTab='matriz'; intakeStep='review'; $dispatch('intake-navigate',{view:'review'})">2. Revisar por portal</button>
        <button type="button" class="btn-secondary" :aria-pressed="searchTab==='matriz' && intakeStep==='confirmed'" :class="(searchTab==='matriz' && intakeStep==='confirmed') ? 'ring-2 ring-teal-700 bg-teal-50' : ''" @click="searchTab='matriz'; intakeStep='confirmed'; $dispatch('intake-navigate',{view:'confirmed'})">3. Confirmados y factores</button>
    </nav>
    <?php require __DIR__ . '/methodology-search-support.php'; ?>
    <div class="mt-6" x-show="['captura','matriz','mapa'].includes(searchTab)">
        <div id="captura-83" class="scroll-mt-6">
            <?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-search-captura.php'; ?>
        </div>
    </div>
    <button type="button" class="mt-4 min-h-11 text-sm font-semibold text-teal-800 underline" @click="$refs.searchSupport.showModal()">Consultar contexto, portales o planificación</button>
</section>
