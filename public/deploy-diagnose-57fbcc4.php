<?php
declare(strict_types=1);

$token = (string) ($_GET['token'] ?? '');
if (!hash_equals('9f520b5c35e44c7e8669df71cbb7b83525714b5077294b0f8dce6793f9f17fd1', $token)) {
    http_response_code(404);
    exit;
}

$view = dirname(__DIR__) . '/app/Views/appraisals/legal-characteristics.php';
$source = is_file($view) ? (string) file_get_contents($view) : '';

header('Content-Type: text/plain; charset=utf-8');
echo 'dir=' . __DIR__ . PHP_EOL;
echo 'view=' . $view . PHP_EOL;
echo 'view_exists=' . (is_file($view) ? 'yes' : 'no') . PHP_EOL;
echo 'sha1=' . ($source !== '' ? sha1($source) : '') . PHP_EOL;
echo 'has_new_partials=' . (str_contains($source, 'legal-annotation-table.php') ? 'yes' : 'no') . PHP_EOL;
echo 'has_old_table=' . (str_contains($source, 'Anotaciones clasificadas') ? 'yes' : 'no') . PHP_EOL;
