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
    $record = ['id' => str_repeat('a', 32), 'tipo_inmueble' => 'lote', 'tipo_negocio' => 'venta'];
    $subject = []; $comparableRows = []; $marketNeighborhoods = []; $componentKey = '';
    $_SESSION['csrf'] ??= 'test-capture-tabs';
    ob_start(); require BASE_PATH . '/app/Views/appraisals/valuation-methodology-search.php'; $html = ob_get_clean();
    expect(str_contains($html, '1. Buscar por portal') && str_contains($html, '2. Revisar por portal')
        && str_contains($html, 'aria-label="Vistas de las muestras"'), 'C mantiene captura y bandeja con mapas dentro de las muestras');
    expect(str_contains($html,'3. Confirmados y factores') && str_contains($html,"searchTab === 'configuracion_portales'"), 'investigación por portal independiente de captura y bandeja');
    foreach (['ciencuadrasPaste', 'properatiPaste', 'mercadolibrePaste'] as $reader) {
        expect(str_contains($html, 'x-data="' . $reader . '"'), 'captura de oficina seleccionada mantiene lector ' . $reader);
    }
    expect(str_contains($html, 'Subir no repetidos') && str_contains($html, 'Ctrl+V')
        && str_contains($html, 'Pega la página de resultados') && substr_count($html, 'id="tabla-madre-83"') === 1,
        'pegado, selección sin repetidos y única matriz persisten al reorganizar C');
    expect(str_contains($html, 'sourceResultsPaste(') && str_contains($html, 'Asesorar Inmobiliaria')
        && str_contains($html, '4. Tabla de inmuebles') && str_contains($html, 'Inmuebles recogidos y atributos')
        && str_contains($html, 'Sujeto · capítulo 3'), 'fuentes locales tienen lector con revisión y tabla separada con sujeto de referencia');
    $guide = (new \App\Services\AppraisalComparableSearchGuide())->build(
        ['tipo_inmueble'=>'oficina', 'regimen_ph'=>'si', 'tipo_negocio'=>'venta'], [], [], []);
    ob_start(); require BASE_PATH . '/app/Views/appraisals/methodology-search-prompt.php'; $prompt = ob_get_clean();
    expect(str_contains($prompt, 'celda de parqueo') && str_contains($prompt, 'misma matrícula')
        && str_contains($prompt, 'no exime de la depuración'), 'consulta PH verifica composición sin eximir descuento por anexos similares');
    $comparableRows = [['source_name'=>'Prueba PH', 'ph_regime'=>'si', 'ph_parking_nature'=>'comun_exclusivo']];
    ob_start(); require BASE_PATH . '/app/Views/appraisals/valuation-methodology-search.php'; $html = ob_get_clean();
    expect(str_contains($html, '[ph_deposit_count]') && str_contains($html, 'PH · área privada, parqueaderos y depósitos')
        && str_contains($html, 'data-ph-subject="si"') && substr_count($html, 'id="tabla-madre-83"') === 1,
        'PH tiene campos estructurados y entrada propia en la misma matriz');
    preg_match('/<form id="tabla-madre-83".*?<\/form>/s', $html, $matrix);
    expect(substr_count($matrix[0] ?? '', '<tbody') === 1, 'tabla normativa fuera del formulario no interfiere con filas matriz/importación');
    expect(str_contains($html, '[negotiation_discount]') && str_contains($html, 'Descargar tabla completa en Excel (.xlsx)')
        && str_contains($html, 'Instrucciones precisas para Properati'), 'PH presenta negociación descarga e instrucciones por portal');
})();
