<?php
// Authenticated local fixture session created by tests/http.php.
$base = $path . '/bien-sujeto/ph';
$fields = ['_token'=>$token, 'version'=>'0', 'ph'=>['ph_name'=>'Copropiedad manual']];
$jsonHeaders = ['Accept: application/json'];
$response = request($base . '/autoguardar', http_build_query($fields), $jsonHeaders);
expect($response['status'] === 200 && json_decode($response['body'], true)['version'] === 1, 'PH autoguardado HTTP con versión');
expect(request($base . '/autoguardar', http_build_query($fields), $jsonHeaders)['status'] === 409, 'PH HTTP conserva formulario ante conflicto');
expect(request($base . '/autoguardar', 'version=1', $jsonHeaders)['status'] === 419, 'PH HTTP exige CSRF');
$pdf = tempnam(sys_get_temp_dir(), 'ph_pdf_');
$reading = tempnam(sys_get_temp_dir(), 'ph_json_');
file_put_contents($pdf, "%PDF-1.4\n% Synthetic upload fixture");
$contents = [['index'=>0, 'name'=>'fixture.pdf', 'size'=>filesize($pdf), 'total'=>2, 'pages'=>[
    ['page'=>1, 'text'=>'Reglamento de propiedad horizontal. EDIFICIO PRUEBA HTTP.', 'confidence'=>95],
    ['page'=>2, 'text'=>'La piscina, las escaleras y los ascensores son bienes comunes.', 'confidence'=>95],
]]];
file_put_contents($reading, json_encode($contents));
try {
    $response = request($base . '/soportes', ['_token'=>$token, 'version'=>'1',
        'ph_document[0]'=>new CURLFile($pdf, 'application/pdf', 'fixture.pdf'),
        'ph_client_text'=>new CURLFile($reading, 'application/json', 'lectura.json')]);
    expect($response['status'] === 303, 'PH HTTP recibe original y lectura completa por separado');
    $html = request($path . '/bien-sujeto')['body'];
    // Enable PH in the appraisal before looking at the section.
    $data['version'] = 2; $data['regimen_ph'] = 'si';
    request($path . '/borrador', json_encode($data), $headers);
    $html = request($path . '/bien-sujeto')['body'];
    expect(str_contains($html, 'Copropiedad manual') && str_contains($html, 'p. 2'), 'PH HTTP prellena sin borrar criterio manual');
    preg_match('#href="([^"]+/ph/soportes/[a-f0-9]{32}/texto)"#', $html, $link);
    $readback = request(html_entity_decode($link[1] ?? '/missing'));
    expect($readback['status'] === 200 && str_contains($readback['body'], '[Página 2]'), 'PH HTTP recupera texto completo persistido');
} finally { unlink($pdf); unlink($reading); }
