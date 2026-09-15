<?php
declare(strict_types=1);
namespace App\Core;

final class Session
{
    public static function start(): void
    {
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        $directory = BASE_PATH . '/storage/sessions';
        if (!is_dir($directory) && !mkdir($directory, 0700, true) && !is_dir($directory)) {
            throw new \RuntimeException('No se pudo preparar el almacenamiento de sesiones.');
        }
        session_save_path($directory);
        ini_set('session.gc_maxlifetime', (string) max(60, (int) Env::get('SESSION_IDLE_SECONDS', '28800')));
        session_name('gestion_avaluatoria');
        $path = Http::basePath() . '/';
        session_set_cookie_params([
            'httponly' => true, 'samesite' => 'Lax',
            'secure' => Env::bool('SESSION_SECURE', true),
            'path' => $path === '//' ? '/' : $path,
        ]);
        session_start();
        $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
    }

    public static function csrf(): void
    {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST['_token'] ?? '';
        if (!is_string($token) || !hash_equals($_SESSION['csrf'], $token)) {
            throw new HttpException(419, 'La sesión de seguridad venció. Recarga la página.');
        }
    }

    public static function refreshCsrf(): void
    {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    public static function logout(): void
    {
        $_SESSION = [];
        $params = session_get_cookie_params();
        unset($params['lifetime']);
        setcookie(session_name(), '', $params + ['expires' => time() - 3600]);
        session_destroy();
    }
}
