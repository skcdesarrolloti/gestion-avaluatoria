<?php
$portalFields = is_array($guide['portal_fields'] ?? null) ? $guide['portal_fields'] : [];
$portalFilters = is_array($guide['portal_filters'] ?? null) ? $guide['portal_filters'] : [];
$sourceSearch = is_array($guide['source_search'] ?? null) ? $guide['source_search'] : [];
$portalSources = is_array($sourceSearch['portal_sources'] ?? null) ? $sourceSearch['portal_sources'] : [];
$agencySources = is_array($sourceSearch['agency_sources'] ?? null) ? $sourceSearch['agency_sources'] : [];
$officialSources = is_array($sourceSearch['official_sources'] ?? null) ? $sourceSearch['official_sources'] : [];
$captureProtocol = is_array($sourceSearch['capture_protocol'] ?? null) ? $sourceSearch['capture_protocol'] : [];
$adjustments = is_array($guide['adjustments'] ?? null) ? $guide['adjustments'] : (is_array($guide['homologation'] ?? null) ? $guide['homologation'] : []);
$nextStep = is_array($methodologyDecision['next_step'] ?? null) ? $methodologyDecision['next_step'] : ['8.3 Desarrollo del método', ''];
$formulaFamilies = [
    ['Mercado', 'Valor unitario = precio depurado / unidad de comparación; luego promedio, mediana, desviación, coeficiente de variación, rango y ajustes comparativos sustentados.'],
    ['Renta', 'Ingreso neto = canon bruto menos vacancia, administración no recuperable y gastos; valor = ingreso neto anual / tasa, o flujo descontado si aplica.'],
    ['Residual', 'Valor del suelo = ingresos esperados del producto menos costos directos, indirectos, financieros, utilidad, tiempos y riesgos del desarrollo.'],
    ['Costo', 'Valor = terreno + costo de reposición nuevo menos depreciación física, funcional y económica, con soporte de cantidades y precios.'],
];
$searchTabs = [
    'buscador' => 'Buscador',
    'filtros' => 'Filtros',
    'captura' => 'Captura',
    'matriz' => 'Matriz',
    'variables' => 'Variables',
    'formulas' => 'Fórmulas',
    'criterios' => 'Criterios',
];
?>
<section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8" x-data="{ searchTab: 'buscador' }">
    <div class="mb-6">
        <p class="eyebrow">8.3 Desarrollo operativo del método</p>
        <h2 class="mt-2 text-2xl font-semibold"><?= e((string) ($nextStep[0] ?? 'Búsqueda y preparación técnica')) ?></h2>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
            Este bloque inicia después de decidir el método en 8.2. Primero prepara la consulta y luego ordena
            filtros, captura, variables, fórmulas y criterios antes de pasar al análisis.
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
        <?= e((string) ($nextStep[1] ?? 'Define primero los insumos del método seleccionado.')) ?>
    </div>

    <nav class="mt-6 flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" aria-label="Pestañas del desarrollo 8.3">
        <?php foreach ($searchTabs as $key => $label): ?>
            <button type="button" class="min-h-11 shrink-0 rounded-lg px-4 py-2 text-sm font-semibold"
                :class="searchTab === '<?= e($key) ?>' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600'"
                @click="searchTab = '<?= e($key) ?>'"><?= e($label) ?></button>
        <?php endforeach; ?>
    </nav>

    <div class="mt-6">
        <div x-show="searchTab === 'buscador'">
            <?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-search-buscador.php'; ?>
        </div>
        <div x-show="searchTab === 'filtros'">
            <?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-search-filtros.php'; ?>
        </div>
        <div x-show="searchTab === 'captura'">
            <?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-search-captura.php'; ?>
        </div>
        <div x-show="searchTab === 'matriz'">
            <?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-search-matriz.php'; ?>
        </div>
        <div x-show="searchTab === 'variables'">
            <?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-search-variables.php'; ?>
        </div>
        <div x-show="searchTab === 'formulas'">
            <?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-search-formulas.php'; ?>
        </div>
        <div x-show="searchTab === 'criterios'">
            <?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-search-criterios.php'; ?>
        </div>
    </div>
</section>
