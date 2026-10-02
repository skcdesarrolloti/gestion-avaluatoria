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
$searchTabs = [
    'buscar' => '1. Buscar',
    'captura' => '2. Portales y pegado',
    'matriz' => '3. Matriz de datos',
    'mapa' => '4. Mapas y evidencia',
];
?>
<section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8" x-data="{ searchTab: 'captura' }">
    <div class="mb-6">
        <p class="eyebrow">M3 Desarrollo operativo del método de mercado</p>
        <h2 class="mt-2 text-2xl font-semibold">Investigación, muestra y trazabilidad de mercado</h2>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
            Este bloque queda enfocado en comparación o mercado. Primero prepara la consulta conforme a los
            artículos 16 a 21 de la Resolución 941; luego ordena filtros, captura, georreferenciación,
            variables, fórmulas y criterios antes de pasar al análisis estadístico de M4.
        </p>
    </div>

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <p class="eyebrow">Activo sujeto</p>
            <h3 class="mt-2 text-xl font-semibold"><?= e($guide['type_label'] ?? 'Tipología pendiente') ?></h3>
            <p class="mt-2 text-sm leading-6 text-slate-600"><?= e(trim((string) ($record['titulo'] ?? '')) ?: 'Ficha sin título definido') ?></p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <p class="eyebrow">Mercado comparable</p>
            <h3 class="mt-2 text-xl font-semibold"><?= e($guide['business_label'] ?: 'Tipo de negocio pendiente') ?></h3>
            <p class="mt-2 text-sm leading-6 text-slate-600">La búsqueda debe conservar la misma operación: venta con venta, arriendo con arriendo.</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <p class="eyebrow">Derecho valorado</p>
            <h3 class="mt-2 text-xl font-semibold"><?= e($guide['right_label'] ?: 'Derecho pendiente') ?></h3>
            <p class="mt-2 text-sm leading-6 text-slate-600">Si cambia el derecho jurídico, se deja observación técnica antes de compararse.</p>
        </div>
    </div>

    <div class="mt-6 rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-sm leading-6 text-emerald-950">
        <strong>Regla de flujo:</strong>
        Las muestras pertenecen al componente indicado arriba. Revisa su comparabilidad antes de analizarlas en M4.
    </div>

    <nav class="mt-6 flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" aria-label="Pestañas del desarrollo M3">
        <?php foreach ($searchTabs as $key => $label): ?>
            <button type="button" class="min-h-11 shrink-0 rounded-lg px-4 py-2 text-sm font-semibold"
                :class="searchTab === '<?= e($key) ?>' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600'"
                @click="searchTab = '<?= e($key) ?>'"><?= e($label) ?></button>
        <?php endforeach; ?>
    </nav>

    <div class="mt-6">
        <div x-show="searchTab === 'buscar'">
            <?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-search-diseno.php'; ?>
            <div class="mt-6">
                <?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-search-buscador.php'; ?>
            </div>
            <div class="mt-6">
                <?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-search-filtros.php'; ?>
            </div>
        </div>
        <div id="captura-83" class="scroll-mt-6" x-show="['captura', 'matriz', 'mapa'].includes(searchTab)">
            <?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-search-captura.php'; ?>
        </div>

    </div>
</section>
