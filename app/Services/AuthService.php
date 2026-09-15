<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\Env;
use App\Models\FuncionarioRepository;

final class AuthService
{
    public function __construct(private FuncionarioRepository $users) {}

    public function attempt(string $login, string $password): bool
    {
        $row = $this->users->byLogin($login);
        $stored = (string) ($row['password'] ?? '');
        $hashed = (password_get_info($stored)['algoName'] ?? 'unknown') !== 'unknown';
        $valid = $hashed ? password_verify($password, $stored)
            : (Env::bool('AUTH_ALLOW_LEGACY_PASSWORDS') && $stored !== '' && hash_equals($stored, $password));
        if (!$valid || !$this->allowed($row)) {
            return false;
        }
        session_regenerate_id(true);
        $_SESSION['user'] = $this->identity($row);
        $_SESSION['last_activity'] = time();
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
        return true;
    }

    public function current(): ?array
    {
        $id = (int) ($_SESSION['user']['id'] ?? 0);
        $timeout = max(60, (int) Env::get('SESSION_IDLE_SECONDS', '28800'));
        if (!$id || time() - (int) ($_SESSION['last_activity'] ?? 0) > $timeout) {
            unset($_SESSION['user']);
            return null;
        }
        // Re-check active status and role even for existing sessions.
        $row = $this->users->byId($id);
        if (!$this->allowed($row)) {
            unset($_SESSION['user']);
            return null;
        }
        $_SESSION['last_activity'] = time();
        return $_SESSION['user'] = $this->identity($row);
    }

    private function allowed(?array $row): bool
    {
        $roles = array_filter(array_map('trim', explode(',', Env::get('AUTH_ALLOWED_ROLES'))));
        return $row && (int) $row['_ID'] > 0 && strtolower(trim((string) $row['activo'])) === 'si'
            && (!$roles || in_array((string) $row['rol'], $roles, true));
    }

    private function identity(array $row): array
    {
        return ['id' => (int) $row['_ID'], 'employee_id' => (string) $row['id_empleado'],
            'name' => (string) $row['nombre'], 'role' => (string) $row['rol']];
    }
}
