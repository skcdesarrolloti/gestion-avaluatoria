<?php
declare(strict_types=1);
namespace App\Core;

final class Http
{
    public static function json(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        exit;
    }

    public static function wantsJson(): bool
    {
        return str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
    }

    public static function input(): array
    {
        if (!str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json')) {
            throw new HttpException(415, 'Se requiere JSON.');
        }
        $body = file_get_contents('php://input', false, null, 0, 16385);
        if (strlen($body) > 16384) {
            throw new HttpException(413, 'El formulario supera el tamaño permitido.');
        }
        try {
            $data = json_decode($body, true, 16, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new HttpException(400, 'El formulario tiene un formato inválido.');
        }
        if (!is_array($data) || array_is_list($data)) {
            throw new HttpException(400, 'Se esperaba un objeto.');
        }
        return $data;
    }

    public static function basePath(): string
    {
        $configured = rtrim(Env::get('APP_BASE_PATH'), '/');
        if ($configured !== '') {
            return '/' . trim($configured, '/');
        }
        $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        if (str_ends_with($script, '/public/index.php')) {
            return substr($script, 0, -strlen('/index.php'));
        }
        return '';
    }

    public static function redirect(string $route): never
    {
        header('Location: ' . url($route), true, 303);
        exit;
    }
}
