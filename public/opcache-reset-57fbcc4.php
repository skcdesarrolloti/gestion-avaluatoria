<?php
declare(strict_types=1);

$token = (string) ($_GET['token'] ?? '');
if (!hash_equals('9f520b5c35e44c7e8669df71cbb7b83525714b5077294b0f8dce6793f9f17fd1', $token)) {
    http_response_code(404);
    exit;
}

header('Content-Type: text/plain; charset=utf-8');
if (function_exists('opcache_reset')) {
    echo opcache_reset() ? 'opcache reset ok' : 'opcache reset failed';
    exit;
}

echo 'opcache not available';
