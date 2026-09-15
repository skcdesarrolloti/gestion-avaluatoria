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
        (new RateLimiter(BASE_PATH . '/storage/rate-limits'))->consume($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        $login = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $valid = is_string($login) && is_string($password) && strlen($login) <= 190
            && strlen($password) <= 1024 && $login !== '' && $password !== '';
        if ($valid && $this->auth->attempt(trim($login), $password)) {
            Http::redirect('');
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
}
