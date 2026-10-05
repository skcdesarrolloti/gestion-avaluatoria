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
<section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8" x-data="{ sourcePortal: '', intakeStep: 'review', searchTab: '<?= ($_GET['research'] ?? '')==='1'?'investigacion':'captura' ?>' }">
    <nav class="mb-3 flex flex-wrap gap-2 rounded-xl bg-slate-100 p-2" aria-label="Pasos de la investigación">
        <button type="button" class="btn-secondary" :aria-pressed="searchTab==='captura'" :class="(searchTab==='captura') ? 'ring-2 ring-teal-700 bg-teal-50' : ''" @click="searchTab='captura'">1. Buscar por portal</button>
        <button type="button" class="btn-secondary" :aria-pressed="searchTab==='matriz' && intakeStep==='review'" :class="(searchTab==='matriz' && intakeStep==='review') ? 'ring-2 ring-teal-700 bg-teal-50' : ''" @click="searchTab='matriz'; intakeStep='review'; $dispatch('intake-navigate',{view:'review'})">2. Revisar por portal</button>
        <button type="button" class="btn-secondary" :aria-pressed="searchTab==='matriz' && intakeStep==='confirmed'" :class="(searchTab==='matriz' && intakeStep==='confirmed') ? 'ring-2 ring-teal-700 bg-teal-50' : ''" @click="searchTab='matriz'; intakeStep='confirmed'; $dispatch('intake-navigate',{view:'confirmed'})">3. Confirmados y factores</button>
    </nav>
    <details class="mb-4 rounded-xl border p-3">
        <summary class="min-h-11 cursor-pointer py-2 font-semibold">Herramientas de apoyo · guía de portales y planificación</summary>
        <p class="my-2 text-sm">Consulta qué publica cada portal o prepara qué atributos investigar. Puedes recoger y confirmar anuncios sin completar un plan.</p>
        <div class="flex flex-wrap gap-2">
            <button type="button" class="btn-secondary" :aria-pressed="searchTab==='configuracion_portales'" @click="searchTab='configuracion_portales'">Qué publica cada portal</button>
            <button type="button" class="btn-secondary" :aria-pressed="searchTab==='investigacion'" @click="searchTab='investigacion'">Planificar la investigación</button>
        </div>
    </details>
    <?php require __DIR__ . '/methodology-portal-profiles.php'; ?>
    <?php require __DIR__ . '/methodology-research-plan.php'; ?>
    <details x-show="searchTab === 'captura'" class="mb-4 rounded-xl border p-3"><summary class="min-h-11 cursor-pointer py-2 font-semibold">Consulta general de esta unidad · conservar instrucciones</summary><?php require __DIR__ . '/methodology-search-prompt.php'; ?></details>
    <div x-show="searchTab === 'investigacion'"><?php require __DIR__ . '/methodology-market-coverage.php'; ?></div>
    <div class="mb-6" x-show="searchTab === 'captura'">
        <p class="eyebrow"><?= e(($prefix ?? 'M') . '3 · Insumos del método ' . ($methods[$method ?? 'mercado'] ?? 'Mercado')) ?></p>
        <h2 class="mt-2 text-2xl font-semibold">Investigación, muestra y trazabilidad de mercado</h2>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
            Captura las referencias de la operación indicada para esta unidad. En comparación o mercado, consulta los
            artículos 16 a 21; para renta, los artículos 22 a 26 y 38. Conserva filtros, captura, georreferenciación,
            variables, fórmulas y criterios antes de pasar al análisis del método.
        </p>
    </div>

    <details x-show="searchTab === 'captura'" class="mb-4 rounded-xl border p-3"><summary class="min-h-11 cursor-pointer py-3 font-semibold">Ver contexto del inmueble y reglas de captura</summary>
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

    </details>
    <div class="mt-6" x-show="searchTab !== 'configuracion_portales' && searchTab !== 'investigacion'">
        <div id="captura-83" class="scroll-mt-6">
            <?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-search-captura.php'; ?>
        </div>
        <details x-show="searchTab === 'captura'" class="mt-4 rounded-xl border p-3"><summary class="min-h-11 cursor-pointer py-3 font-semibold">Preparar criterios, filtros y diseño de muestra (consulta opcional)</summary>
            <?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-search-diseno.php'; ?>
            <div class="mt-6">
                <?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-search-buscador.php'; ?>
            </div>
            <div class="mt-6">
                <?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-search-filtros.php'; ?>
            </div>
        </details>

    </div>
</section>
