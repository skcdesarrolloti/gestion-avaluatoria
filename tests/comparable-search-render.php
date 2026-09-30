<?php
declare(strict_types=1);

(static function (): void {
    $guide = (new \App\Services\AppraisalComparableSearchGuide())->build(
        ['tipo_inmueble' => 'oficina', 'tipo_negocio' => 'venta', 'municipio' => 'Cartagena'],
        ['city_name' => 'Cartagena', 'neighborhood_name' => 'Chambacú'], [], []);
    $expected = $guide;
    $methodologyGuides = [
        ['key' => 'mercado', 'label' => 'Mercado', 'parts' => []],
        ['key' => 'costo', 'label' => 'Costo', 'parts' => []],
    ];
    ob_start();
    try {
        require BASE_PATH . '/app/Views/appraisals/valuation-methodology-method-guides.php';
    } finally { ob_end_clean(); }
    expect($guide === $expected, 'renderizar academia 8.1 conserva datos del sujeto y busqueda 8.3');

    $sourceSearch = $guide['source_search'];
    $baseQuery = $sourceSearch['query'];
    $portalSources = $sourceSearch['portal_sources'];
    $agencySources = $sourceSearch['agency_sources'];
    ob_start();
    try {
        require BASE_PATH . '/app/Views/appraisals/valuation-methodology-source-links.php';
        $html = ob_get_contents();
    } finally { ob_end_clean(); }
    expect(str_contains($html, e($baseQuery)) && str_contains($html, 'Chambacú')
        && !str_contains($html, 'Búsqueda base pendiente:'),
        'fuentes de captura renderizan consulta concatenada tras academia');
    expect(str_contains($html, e($portalSources[0]['url'])),
        'enlace del portal conserva los criterios del expediente tras academia');
})();
