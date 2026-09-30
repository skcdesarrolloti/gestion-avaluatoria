<?php
declare(strict_types=1);
(static function (): void {
    $url = App\Services\MetrocuadradoAreaSearch::url('Bocagrande');
    $item = ['title' => 'Oficina de prueba', 'link' => '/inmueble/venta-oficina-cartagena-de-indias-bocagrande/123-M456',
        'mtiponegocio' => 'venta', 'mtipoinmueble' => ['nombre' => 'Oficina'], 'mciudad' => ['nombre' => 'Cartagena de Indias'],
        'mvalorventa' => 450000000, 'marea' => 46, 'midinmueble' => '123-M456', 'mnombrecomunbarrio' => 'Bocagrande',
        'mbarrio' => 'BOCA GRANDE', 'data' => ['mvaloradministracion' => '250000'],
        'geopoints' => [['address' => 'No es la dirección del inmueble', 'latitude' => 10]], 'localizacion' => ['lat' => 10, 'lon' => -75]];
    $html = static function (array $items, int $total = 1) use ($url): string {
        $payload = '6:' . json_encode(['props' => ['initialResults' => ['results' => $items, 'totalHits' => $total]]], JSON_THROW_ON_ERROR) . "\n";
        return '<link rel="canonical" href="' . $url . '"><script>self.__next_f.push(' . json_encode([1, $payload], JSON_THROW_ON_ERROR) . ')</script>';
    };
    $parser = new App\Services\MetrocuadradoResultsParser();
    $result = $parser->parse($html([$item, $item]), $url);
    expect(count($result['results']) === 1, 'Metrocuadrado deduplica enlaces del resumen');
    $row = $result['results'][0]['row'];
    expect($row['price_amount'] === '450000000' && $row['area_m2'] === '46' && $row['source_name'] === 'Metrocuadrado', 'Metrocuadrado extrae precio y área publicados');
    expect($row['ph_regime'] === 'por_verificar' && !isset($row['latitude']) && !isset($row['address_hint']), 'Metrocuadrado no infiere PH ni usa puntos de interés como dirección');
    $bad = array_replace($item, ['link' => 'https://evil.test/inmueble/1']);
    $otherCity = array_replace($item, ['mciudad' => ['nombre' => 'Bogotá']]);
    expect($parser->parse($html([$bad, $otherCity]), $url)['results'] === [], 'Metrocuadrado omite enlaces externos y otra ciudad');
    expect(str_contains($parser->parse($html([$item], 80), $url)['notice'], 'hasta 50'), 'Metrocuadrado informa límite sin prometer todos los resultados');
    try { $parser->parse($html([$item]), $url . 'otra/'); expect(false, 'canonical distinto rechazado'); }
    catch (RuntimeException) { expect(true, 'Metrocuadrado rechaza barrio no confirmado'); }
    try { $parser->parse('<link rel="canonical" href="' . $url . '">', $url); expect(false, 'formato desconocido rechazado'); }
    catch (RuntimeException) { expect(true, 'Metrocuadrado distingue formato ilegible de listado vacío'); }
    expect($parser->parse($html([], 0), $url)['results'] === [], 'Metrocuadrado acepta barrio sin avisos');
    try { (new App\Services\MetrocuadradoReader())->fetch('https://127.0.0.1/private'); expect(false, 'destino externo rechazado'); }
    catch (InvalidArgumentException) { expect(true, 'lector Metrocuadrado limita destino antes de conectar'); }
})();
