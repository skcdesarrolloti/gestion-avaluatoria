<?php
declare(strict_types=1);
namespace App\Core;
use App\Controllers\AppraisalController;
use App\Controllers\AuthController;
use App\Controllers\StandardController;
use App\Database\Migrator;
use App\Models\AppraisalRepository;
use App\Models\FuncionarioRepository;
use App\Models\ValuationStandardRepository;
use App\Services\AuthService;

final class Kernel
{
    public function run(): void
    {
        Session::start();
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('Referrer-Policy: same-origin');
        header('Cache-Control: no-store');
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
        $base = Http::basePath();
        if ($base !== '' && $path !== $base && !str_starts_with($path, $base . '/')) {
            throw new HttpException(404, 'Página no encontrada.');
        }
        $path = '/' . trim(substr($path, strlen($base)), '/');
        $method = $_SERVER['REQUEST_METHOD'];
        $routes = require BASE_PATH . '/routes/web.php';
        $matchedPath = false;
        foreach ($routes as [$verb, $pattern, $controller, $action, $protected]) {
            if (!preg_match($pattern, $path, $matches)) {
                continue;
            }
            $matchedPath = true;
            if ($method !== $verb) {
                continue;
            }
            if ($method === 'POST') {
                try {
                    Session::csrf();
                } catch (HttpException $error) {
                    if ($controller === 'auth' && $action === 'attempt') {
                        Session::refreshCsrf();
                        Session::flash('login_error', $error->getMessage());
                        Session::flash('login_username', $this->postedUsername());
                        Http::redirect('login');
                    }
                    throw $error;
                }
            }
            // The login form is accessible before database credentials are configured.
            if ($controller === 'auth' && $action === 'login') {
                view('auth/login', $this->loginData());
                return;
            }
            if ($controller === 'auth' && $action === 'logout') {
                Session::logout();
                Http::redirect('login');
            }
            if ($protected && empty($_SESSION['user'])) {
                if (Http::wantsJson()) {
                    throw new HttpException(401, 'Inicia sesión para continuar.');
                }
                Http::redirect('login');
            }
            try {
                $auth = $this->authService();
            } catch (\Throwable $error) {
                if ($controller === 'auth' && $action === 'attempt') {
                    $this->loginInfrastructureError($error);
                    Http::redirect('login');
                }
                throw $error;
            }
            $user = $protected ? $auth->current() : null;
            if ($protected && !$user) {
                if (Http::wantsJson()) {
                    throw new HttpException(401, 'Tu sesión venció. Conserva tus cambios e inicia sesión de nuevo.');
                }
                Http::redirect('login');
            }
            if ($protected) {
                Database::assertSeparate();
                $db = Database::connection();
                if (Env::bool('AUTO_MIGRATE', true)) {
                    (new Migrator($db, BASE_PATH . '/database/migrations'))->run();
                }
            }
            $instance = match ($controller) {
                'auth' => new AuthController($auth),
                'standards' => new StandardController(new ValuationStandardRepository($db)),
                default => new AppraisalController(new AppraisalRepository($db), $user),
            };
            $instance->$action(...array_slice($matches, 1));
            return;
        }
        throw new HttpException($matchedPath ? 405 : 404, $matchedPath ? 'Método no permitido.' : 'Página no encontrada.');
    }

    private function postedUsername(): string
    {
        $username = $_POST['username'] ?? '';
        return is_string($username) ? substr($username, 0, 190) : '';
    }

    private function authService(): AuthService
    {
        return new AuthService(new FuncionarioRepository(Database::connection('auth')));
    }

    private function loginInfrastructureError(\Throwable $error): void
    {
        error_log('Gestion avaluatoria login auth ' . get_class($error) . ' code=' . $error->getCode()
            . ' at ' . basename($error->getFile()) . ':' . $error->getLine());
        Session::flash('login_error', 'No se pudo verificar el acceso con funcionarios. Contacta al administrador.');
        Session::flash('login_username', $this->postedUsername());
    }

    private function loginData(): array
    {
        return [
            'title' => 'Iniciar sesión',
            'error' => Session::pullFlash('login_error'),
            'username' => Session::pullFlash('login_username'),
        ];
    }
}
