<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Http;
use App\Core\Session;
use App\Services\AuthService;
use App\Services\RateLimiter;

final class AuthController
{
    public function __construct(private AuthService $auth) {}

    public function login(): void
    {
        view('auth/login', ['title' => 'Iniciar sesión']);
    }

    public function attempt(): void
    {
        try {
            (new RateLimiter(BASE_PATH . '/storage/rate-limits'))->consume($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        } catch (\Throwable $error) {
            $this->infrastructureError($error, 'rate-limit');
        }
        $login = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $valid = is_string($login) && is_string($password) && strlen($login) <= 190
            && strlen($password) <= 1024 && $login !== '' && $password !== '';
        if ($valid) {
            try {
                if ($this->auth->attempt(trim($login), $password)) {
                    Http::redirect('');
                }
            } catch (\Throwable $error) {
                $this->infrastructureError($error, 'auth-attempt');
            }
        }
        Session::flash('login_error', 'Usuario o contraseña incorrectos, o acceso no habilitado.');
        Session::flash('login_username', is_string($login) ? substr($login, 0, 190) : '');
        Http::redirect('login');
    }

    public function logout(): never
    {
        Session::logout();
        Http::redirect('login');
    }

    private function infrastructureError(\Throwable $error, string $scope): never
    {
        error_log('Gestion avaluatoria login ' . $scope . ' ' . get_class($error) . ' code=' . $error->getCode()
            . ' at ' . basename($error->getFile()) . ':' . $error->getLine());
        $login = $_POST['username'] ?? '';
        Session::flash('login_error', 'No se pudo verificar el acceso con funcionarios. Contacta al administrador.');
        Session::flash('login_username', is_string($login) ? substr($login, 0, 190) : '');
        Http::redirect('login');
    }
}
