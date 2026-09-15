<?php
declare(strict_types=1);
require dirname(__DIR__) . '/bootstrap.php';
require __DIR__ . '/support.php';

if (getenv('GA_TEST_HTTP') !== 'true') {
    throw new RuntimeException('Ejecutar solo con GA_TEST_HTTP=true y servidor de fixtures en 127.0.0.1:8088.');
}
$cookie = tempnam(sys_get_temp_dir(), 'ga_http_');
function request(string $path, ?string $body = null, array $headers = []): array
{
    $curl = curl_init('http://127.0.0.1:8088' . $path);
    curl_setopt_array($curl, [CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => true,
        CURLOPT_COOKIEJAR => $GLOBALS['cookie'], CURLOPT_COOKIEFILE => $GLOBALS['cookie'],
        CURLOPT_TIMEOUT => 10, CURLOPT_HTTPHEADER => $headers]);
    if ($body !== null) {
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
    }
    $result = curl_exec($curl);
    if ($result === false) {
        throw new RuntimeException(curl_error($curl));
    }
    $response = ['status' => curl_getinfo($curl, CURLINFO_HTTP_CODE),
        'headers' => substr($result, 0, curl_getinfo($curl, CURLINFO_HEADER_SIZE)),
        'body' => substr($result, curl_getinfo($curl, CURLINFO_HEADER_SIZE))];
    curl_close($curl);
    return $response;
}
function csrf(string $html): string
{
    preg_match('/name="csrf-token" content="([a-f0-9]+)"/', $html, $matches);
    return $matches[1] ?? throw new RuntimeException('No se encontro CSRF.');
}

try {
    expect(request('/')['status'] === 303, 'redireccion anonima a login');
    expect(request('/.env')['status'] === 404, 'secretos fuera de raiz publica');
    expect(request('/avaluos', '')['status'] === 419, 'POST sin CSRF rechazado');
    $login = request('/login');
    expect($login['status'] === 200, 'login disponible');
    $post = http_build_query(['_token' => csrf($login['body']), 'username' => 'ga_test', 'password' => 'Only-test-2026!']);
    expect(request('/login', $post)['status'] === 303, 'login HTTP correcto');
    $home = request('/');
    expect($home['status'] === 200 && str_contains($home['body'], 'Mis avalúos'), 'panel autenticado');
    $token = csrf($home['body']);
    $new = request('/avaluos', http_build_query(['_token' => $token]));
    preg_match('#Location: (/avaluos/[a-f0-9]{32})#', $new['headers'], $matches);
    $path = $matches[1] ?? throw new RuntimeException('No se creo ficha.');
    expect($new['status'] === 303, 'creacion HTTP');
    $headers = ['Accept: application/json', 'Content-Type: application/json', 'X-CSRF-Token: ' . $token];
    $data = ['titulo' => '<script>alert(1)</script>', 'tipo' => '', 'direccion' => '', 'municipio' => 'Bogotá', 'observaciones' => '', 'version' => 1];
    $save = request($path . '/borrador', json_encode($data), $headers);
    expect($save['status'] === 200 && json_decode($save['body'], true)['version'] === 2, 'guardado HTTP confirmado');
    expect(request($path . '/borrador', json_encode($data), $headers)['status'] === 409, 'conflicto HTTP');
    $page = request($path);
    expect($page['status'] === 200 && !str_contains($page['body'], '<script>alert(1)</script>'), 'XSS escapado al recuperar');
    expect(request('/logout')['status'] === 405, 'logout GET rechazado');
    expect(request('/logout', http_build_query(['_token' => $token]))['status'] === 303, 'logout POST');
    // Get a fresh anonymous token, then verify expired auth returns JSON, not a login page.
    $fresh = request('/login');
    $headers[2] = 'X-CSRF-Token: ' . csrf($fresh['body']);
    expect(request($path . '/borrador', json_encode($data), $headers)['status'] === 401, 'API sin sesion devuelve 401');
    report();
} finally {
    unlink($cookie);
}
