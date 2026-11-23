<?php
declare(strict_types=1);
// Included only by tests/http.php against its disposable local fixture server.
$photoFile = tempnam(sys_get_temp_dir(), 'ga-photo-http-');
$photoBytes = hex2bin('89504e470d0a1a0a0000000d4948445200000001000000010804000000b51c0c020000000b4944415478da6364f80f00010501012718e3660000000049454e44ae426082');
file_put_contents($photoFile, $photoBytes);
try {
    $photoFields = ['_token' => $token, 'photo_caption' => 'location:chapter-one',
        'photo_name' => 'Mapa de prueba HTTP', 'photos[0]' => new CURLFile($photoFile, 'image/png', 'mapa.png')];
    $photoHeaders = ['Accept: application/json'];
    $rejected = request($path . '/bien-sujeto/fotos', array_replace($photoFields, ['_token' => 'invalid']), $photoHeaders);
    expect($rejected['status'] === 419, 'imagen sin CSRF no se guarda');
    $uploaded = request($path . '/bien-sujeto/fotos', $photoFields, $photoHeaders);
    $gallery = json_decode($uploaded['body'], true);
    expect($uploaded['status'] === 200 && $gallery['ok'] === true && count($gallery['photos']) === 1,
        'POST multipart confirma imagen del apartado 1.4');
    $locationPhoto = $gallery['photos'][0];
    expect($locationPhoto['name'] === 'Mapa de prueba HTTP', 'imagen conserva nombre y sección');
    $image = request($locationPhoto['url']);
    expect($image['status'] === 200 && $image['body'] === $photoBytes, 'imagen guardada se puede abrir sin alteración');
    $chapter = request($path . '/expediente');
    expect(str_contains($chapter['body'], $locationPhoto['id']) && str_contains($chapter['body'], 'Mapa de prueba HTTP'),
        'recarga del expediente recupera galería desde base de datos');
    $other = request('/avaluos', http_build_query(['_token' => $token]));
    preg_match('#Location: (/avaluos/[a-f0-9]{32})#', $other['headers'], $newPhotoRecord);
    expect(request($newPhotoRecord[1] . '/fotos/' . $locationPhoto['id'])['status'] === 404,
        'foto no puede consultarse desde otro expediente');
} finally {
    unlink($photoFile);
}
