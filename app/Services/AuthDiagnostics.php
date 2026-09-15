<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\Database;
use App\Core\Env;
use App\Models\FuncionarioRepository;

final class AuthDiagnostics
{
    public function run(): array
    {
        $checks = [
            $this->storage('storage', BASE_PATH . '/storage'),
            $this->storage('storage/sessions', BASE_PATH . '/storage/sessions'),
            $this->storage('storage/rate-limits', BASE_PATH . '/storage/rate-limits'),
            ...$this->requiredEnv(),
        ];
        try {
            $repository = new FuncionarioRepository(Database::connection('auth'));
            $repository->checkSchema();
            $checks[] = $this->ok('auth_schema', 'Conexión y columnas de funcionarios verificadas.');
        } catch (\Throwable $error) {
            $checks[] = $this->fail('auth_schema', $this->message($error));
        }
        return $checks;
    }

    private function requiredEnv(): array
    {
        $keys = ['AUTH_DB_HOST', 'AUTH_DB_DATABASE', 'AUTH_DB_USERNAME', 'AUTH_DB_PASSWORD',
            'AUTH_TABLE', 'AUTH_USER_COLUMN', 'AUTH_PASSWORD_COLUMN'];
        return array_map(fn (string $key): array => Env::get($key) === ''
            ? $this->fail($key, "$key no está configurada.")
            : $this->ok($key, "$key está configurada."), $keys);
    }

    private function storage(string $name, string $path): array
    {
        return is_dir($path) && is_writable($path)
            ? $this->ok($name, "$name existe y permite escritura.")
            : $this->fail($name, "$name no existe o no permite escritura.");
    }

    private function message(\Throwable $error): string
    {
        $text = $error->getMessage();
        if ($error instanceof \PDOException) {
            if (str_contains($text, '42S02') || str_contains($text, 'Base table or view not found')) {
                return 'No se encontró la tabla definida en AUTH_TABLE.';
            }
            if (str_contains($text, '42S22') || str_contains($text, 'Unknown column')) {
                return 'Faltan columnas: revisa AUTH_USER_COLUMN y AUTH_PASSWORD_COLUMN.';
            }
            if (str_contains($text, '1045')) {
                return 'Usuario o contraseña de AUTH_DB_* rechazados por MySQL.';
            }
            if (str_contains($text, '1049')) {
                return 'La base AUTH_DB_DATABASE no existe o no es accesible.';
            }
            if (str_contains($text, '2002')) {
                return 'No se pudo alcanzar AUTH_DB_HOST/AUTH_DB_PORT.';
            }
            return 'Falló la conexión o consulta de funcionarios.';
        }
        return $text;
    }

    private function ok(string $key, string $message): array
    {
        return ['key' => $key, 'ok' => true, 'message' => $message];
    }

    private function fail(string $key, string $message): array
    {
        return ['key' => $key, 'ok' => false, 'message' => $message];
    }
}
