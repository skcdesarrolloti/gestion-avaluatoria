<?php
declare(strict_types=1);
$flowPath = $path . '/metodologia-valuatoria';
$flowPost = ['_token' => $token, 'version' => 0, 'component' => 'principal', 'method' => 'mercado', 'reason' => 'Prueba HTTP', 'coverage' => 'Solo componente principal', 'treatment' => 'separado'];
$jsonHeaders = ['Accept: application/json'];
expect(request($flowPath . '/flujo', http_build_query($flowPost), $jsonHeaders)['status'] === 200, 'HTTP guarda método por componente');
expect(request($flowPath . '/flujo', http_build_query($flowPost), $jsonHeaders)['status'] === 409, 'HTTP detecta conflicto metodológico');
expect(request($flowPath . '/flujo', http_build_query(array_replace($flowPost, ['_token' => 'bad'])), $jsonHeaders)['status'] === 419, 'HTTP exige CSRF para selección');
expect(request($flowPath . '/flujo', http_build_query(array_replace($flowPost, ['version' => 1, 'component' => 'ajeno'])), $jsonHeaders)['status'] === 422, 'HTTP rechaza componente no perteneciente al expediente');
foreach (['1', '2', '3', '4', '5', 'components', 'integration', 'decision', 'report'] as $stage) {
    $response = request($flowPath . '?component=principal&stage=' . $stage);
    expect($response['status'] === 200 && !str_contains($response['body'], 'Warning:') && !str_contains($response['body'], 'Fatal error:'), 'HTTP renderiza etapa ' . $stage . ' sin errores');
}
$sampleId = bin2hex(random_bytes(16));
$matrix = ['_token' => $token, 'version' => 0, 'matrix_complete' => '1', 'component_scope' => '', 'comparable_rows_json' => json_encode([['id' => $sampleId, 'source_name' => 'Muestra HTTP conservada', 'price_amount' => 1000000]])];
expect(request($flowPath . '/comparables/autoguardar', http_build_query($matrix), $jsonHeaders)['status'] === 200, 'HTTP guarda banco previo');
$assign = ['_token' => $token, 'version' => 1, 'component' => 'principal', 'samples' => [$sampleId]];
expect(request($flowPath . '/asignar-muestras', http_build_query($assign))['status'] === 303, 'HTTP asigna muestra sin duplicar');
$response = request($flowPath . '?component=principal&stage=3');
expect(str_contains($response['body'], $sampleId) && str_contains($response['body'], 'Muestra HTTP conservada'), 'HTTP recupera ID y datos en componente destino');
$assign = array_replace($assign, ['version' => 2, 'source_scope' => 'principal', 'component' => '']);
expect(request($flowPath . '/asignar-muestras', http_build_query($assign))['status'] === 303, 'HTTP permite corregir asignación devolviendo muestra al banco');
