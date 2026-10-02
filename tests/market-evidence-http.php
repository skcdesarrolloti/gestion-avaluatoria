<?php
declare(strict_types=1);
$marketHttpPath = $path . '/bien-sujeto/unidades/' . $unitId . '/mercado';
$marketHttpData = ['identity_scope'=>'propia','legal_nature'=>'privada','registry'=>'060-990',
    'legal_source'=>'Certificado de prueba pág. 1','observed_use'=>'lote','approved_use'=>'lote','use_source'=>'Norma de prueba',
    'included_components'=>'Solo terreno de prueba','scope_source'=>'Encargo'];
$marketHttpPost = ['_token'=>$token, 'version'=>0, 'market_evidence'=>$marketHttpData];
expect(request($marketHttpPath, http_build_query(array_replace($marketHttpPost, ['_token'=>'bad'])), $jsonHeaders)['status'] === 419,
    'HTTP soporte de Mercado exige CSRF');
$marketHttpSave = request($marketHttpPath, http_build_query($marketHttpPost), $jsonHeaders);
expect($marketHttpSave['status'] === 200 && json_decode($marketHttpSave['body'], true)['version'] === 1,
    'HTTP autoguardado soporte devuelve versión confirmada');
expect(request($marketHttpPath, http_build_query($marketHttpPost), $jsonHeaders)['status'] === 409,
    'HTTP versión antigua no sobrescribe soporte');
expect(request($path . '/bien-sujeto/unidades/' . str_repeat('f', 32) . '/mercado', http_build_query($marketHttpPost), $jsonHeaders)['status'] === 404,
    'HTTP rechaza soporte de unidad inexistente');
$marketHtml = request($flowPath . '?stage=1&component=' . $unitId . '&academy=unidad');
expect($marketHtml['status'] === 200 && str_contains($marketHtml['body'], '060-990')
    && str_contains($marketHtml['body'], 'Actualizar verificación') && str_contains($marketHtml['body'], 'check_component=' . $unitId),
    'HTTP capítulo 8 consulta soporte persistido y conserva retorno de la unidad');
expect(str_contains(strtolower($marketHtml['headers']), 'cache-control: no-store'), 'HTTP actualización no reutiliza datos cacheados');
