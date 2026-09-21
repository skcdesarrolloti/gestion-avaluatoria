<?php
declare(strict_types=1);
// Development server: php -S 127.0.0.1:8088 -t public public/router.php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
if (preg_match('#^/assets/(?:[a-zA-Z0-9_-]+/)*[a-zA-Z0-9_.-]+\.(css|js|mjs|wasm|gz)$#D', $path, $asset)
    && is_file(__DIR__ . $path)) {
    header('Content-Type: ' . match ($asset[1]) {
        'css'=>'text/css', 'js', 'mjs'=>'text/javascript', 'wasm'=>'application/wasm', 'gz'=>'application/gzip',
    });
    header('X-Content-Type-Options: nosniff');
    readfile(__DIR__ . $path);
    return true;
}
require __DIR__ . '/index.php';
