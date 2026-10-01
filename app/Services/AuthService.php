<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\Env;
use App\Models\FuncionarioRepository;

final class AuthService
{
    public function __construct(private FuncionarioRepository $users, private ?\Closure $analysts = null) {}

    public function attempt(string $login, string $password): bool
    {
        $row = $this->users->byLogin($login);
        if (!$row && $this->analysts && !$this->users->hasLogin($login)) return $this->attemptAnalyst($login, $password);
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
        if (!empty($_SESSION['user']['analyst_id'])) return $this->currentAnalyst();
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
    private function attemptAnalyst(string $login, string $password): bool {
        $row=($this->analysts)()->byLogin($login);
        if (!$row || !$row['active'] || !password_verify($password,$row['password_hash']) || !$this->allowed($this->users->byId((int)$row['owner_id']))) return false;
        session_regenerate_id(true); $_SESSION['csrf']=bin2hex(random_bytes(32));
        $_SESSION['user']=$this->analystIdentity($row); $_SESSION['last_activity']=time(); return true;
    }
    private function currentAnalyst(): ?array {
        $session=$_SESSION['user']; $row=$this->analysts ? ($this->analysts)()->find($session['analyst_id']) : null;
        if (!$row || !$row['active'] || (int)$row['auth_version'] !== (int)($session['auth_version']??0)
            || time()-(int)($_SESSION['last_activity']??0)>max(60,(int)Env::get('SESSION_IDLE_SECONDS','28800'))
            || !$this->allowed($this->users->byId((int)$row['owner_id']))) { unset($_SESSION['user']); return null; }
        $_SESSION['last_activity']=time(); return $_SESSION['user']=$this->analystIdentity($row);
    }
    private function analystIdentity(array $row): array {
        return ['id'=>(int)$row['owner_id'], 'employee_id'=>'', 'name'=>$row['full_name'], 'role'=>'analista',
            'analyst_id'=>$row['id'], 'responsible_appraiser_id'=>$row['appraiser_id'],
            'must_change'=>(bool)$row['must_change'], 'auth_version'=>(int)$row['auth_version']];
    }
}
