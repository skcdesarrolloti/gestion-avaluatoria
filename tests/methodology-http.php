<?php
declare(strict_types=1);
$flowPath = $path . '/metodologia-valuatoria';
$jsonHeaders = ['Accept: application/json'];
$empty = request($flowPath);
expect(str_contains($empty['body'], 'Todavía no hay unidades ni anexos registrados') && !str_contains($empty['body'], 'Terreno separado (si corresponde)'), 'HTTP sin unidades no crea componentes virtuales');
$setup = ['_token' => $token, 'version' => 3, 'igac_property_units_count' => 1, 'igac_annex_units_count' => 2];
expect(request($path . '/bien-sujeto/preclasificacion/autoguardar', http_build_query($setup), $jsonHeaders)['status'] === 200, 'HTTP registra composición en capítulo 3');
$sourcePage = request($path . '/bien-sujeto');
preg_match_all('/name="units\[([a-f0-9]{32})\]\[default_label\]" value="([^"]+)"/', $sourcePage['body'], $unitMatches, PREG_SET_ORDER);
$unitId = '';
foreach ($unitMatches as $match) if ($match[2] === 'Unidad 1') $unitId = $match[1];
expect($unitId !== '', 'HTTP recupera identidad real de la unidad del capítulo 3');
$names = ['Unidad 1' => 'Terreno industrial', 'Anexo 1' => 'Cerramiento', 'Anexo 2' => 'Base de concreto'];
$definitions = [];
foreach ($unitMatches as $match) $definitions[$match[1]] = ['label' => $names[$match[2]], 'property_type' => 'lote', 'notes' => 'Estudio registrado en capítulo 3'];
expect(request($path . '/bien-sujeto/unidades/autoguardar', http_build_query(['_token' => $token, 'units' => $definitions]), $jsonHeaders)['status'] === 200, 'HTTP guarda nombres y estudio en su capítulo de origen');
$reflected = request($flowPath)['body'];
expect(str_contains($reflected, 'Terreno industrial') && str_contains($reflected, 'Cerramiento') && str_contains($reflected, 'Base de concreto') && str_contains($reflected, 'Estudio registrado en capítulo 3'), 'HTTP capítulo 8 refleja nombres y descripción guardados sin volver a capturarlos');
expect(request($flowPath . '/flujo', http_build_query(['_token' => $token, 'version' => 0, 'component' => 'terreno', 'method' => 'mercado']), $jsonHeaders)['status'] === 422, 'HTTP rechaza terreno sintético aun con unidades reales');
$flowPost = ['_token' => $token, 'version' => 0, 'component' => $unitId, 'method' => 'mercado', 'reason' => 'Prueba HTTP', 'coverage' => 'Solo componente principal', 'treatment' => 'separado'];
expect(request($flowPath . '/flujo', http_build_query($flowPost), $jsonHeaders)['status'] === 200, 'HTTP guarda método por componente');
expect(request($flowPath . '/flujo', http_build_query($flowPost), $jsonHeaders)['status'] === 409, 'HTTP detecta conflicto metodológico');
expect(request($flowPath . '/flujo', http_build_query(array_replace($flowPost, ['_token' => 'bad'])), $jsonHeaders)['status'] === 419, 'HTTP exige CSRF para selección');
expect(request($flowPath . '/flujo', http_build_query(array_replace($flowPost, ['version' => 1, 'component' => 'ajeno'])), $jsonHeaders)['status'] === 422, 'HTTP rechaza componente no perteneciente al expediente');
foreach (['1', '2', '3', '4', '5', 'components', 'integration', 'decision', 'report'] as $stage) {
    $response = request($flowPath . '?component=' . $unitId . '&stage=' . $stage);
    expect($response['status'] === 200 && !str_contains($response['body'], 'Warning:') && !str_contains($response['body'], 'Fatal error:'), 'HTTP renderiza etapa ' . $stage . ' sin errores');
}
$sampleId = bin2hex(random_bytes(16));
$matrix = ['_token' => $token, 'version' => 0, 'matrix_complete' => '1', 'component_scope' => '', 'comparable_rows_json' => json_encode([['id' => $sampleId, 'source_name' => 'Muestra HTTP conservada', 'price_amount' => 1000000]])];
expect(request($flowPath . '/comparables/autoguardar', http_build_query($matrix), $jsonHeaders)['status'] === 200, 'HTTP guarda banco previo');
$assign = ['_token' => $token, 'version' => 1, 'component' => $unitId, 'samples' => [$sampleId]];
expect(request($flowPath . '/asignar-muestras', http_build_query($assign))['status'] === 303, 'HTTP asigna muestra sin duplicar');
$response = request($flowPath . '?component=' . $unitId . '&stage=3');
expect(str_contains($response['body'], $sampleId) && str_contains($response['body'], 'Muestra HTTP conservada'), 'HTTP recupera ID y datos en componente destino');
$annexIds = array_values(array_filter(array_column($unitMatches, 1), static fn ($key) => $key !== $unitId));
foreach (['costo', 'renta'] as $index => $chosen) {
    $saved = request($flowPath . '/flujo', http_build_query(['_token' => $token, 'version' => $index + 1,
        'component' => $annexIds[$index], 'method' => $chosen, 'treatment' => 'separado']), $jsonHeaders);
    expect($saved['status'] === 200, 'HTTP guarda método distinto para anexo ' . $chosen);
    $unitPage = request($flowPath . '?component=' . $annexIds[$index] . '&stage=components&method=mercado')['body'];
    expect(str_contains($unitPage, 'orientación para ' . ucfirst($chosen))
        && !str_contains($unitPage, 'Revisar Terreno industrial'), 'HTTP método guardado prevalece sobre enlace anterior y solo abre su unidad');
    $inputsPage = request($flowPath . '?component=' . $annexIds[$index] . '&stage=3')['body'];
    expect(!str_contains($inputsPage, $sampleId), 'HTTP insumos no mezclan muestras de otro componente');
    expect(str_contains($inputsPage, $chosen === 'renta' ? 'Consulta preparada para esta unidad · Arriendo' : 'Insumos de Costo'),
        'HTTP prepara insumos adecuados a ' . $chosen);
}
require __DIR__ . '/market-evidence-http.php';
$setup = array_replace($setup, ['version' => 4, 'igac_property_units_count' => 0, 'igac_annex_units_count' => 0]);
expect(request($path . '/bien-sujeto/preclasificacion/autoguardar', http_build_query($setup), $jsonHeaders)['status'] === 200, 'HTTP actualiza composición desde origen');
$recovery = request($flowPath . '?stage=3')['body'];
expect(str_contains($recovery, $sampleId) && str_contains($recovery, 'muestras con asignación anterior'), 'HTTP conserva visible muestra de unidad que dejó de estar activa');
$assign = array_replace($assign, ['version' => 2, 'source_scope' => $unitId, 'component' => '']);
expect(request($flowPath . '/asignar-muestras', http_build_query($assign))['status'] === 303, 'HTTP permite corregir asignación devolviendo muestra al banco');
