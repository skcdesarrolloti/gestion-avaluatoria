<?php
declare(strict_types=1);

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $route = ''): string
{
    return App\Core\Http::basePath() . '/' . ltrim($route, '/');
}

function view(string $template, array $data = []): void
{
    extract($data, EXTR_SKIP);
    ob_start();
    require BASE_PATH . '/app/Views/' . $template . '.php';
    $content = ob_get_clean();
    require BASE_PATH . '/app/Views/layout.php';
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e($_SESSION['csrf']) . '">';
}
