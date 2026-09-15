<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Database;
use App\Core\Env;
use PDO;

final class FuncionarioRepository
{
    private string $table;
    private string $userColumn;
    private string $passwordColumn;

    public function __construct(private PDO $db)
    {
        $this->table = Database::identifier(Env::get('AUTH_TABLE', 'wp_jet_cct_funcionarios'));
        $this->userColumn = Database::identifier(Env::get('AUTH_USER_COLUMN', 'user_others_apss'));
        $this->passwordColumn = Database::identifier(Env::get('AUTH_PASSWORD_COLUMN', 'pass_others_apss'));
    }

    public function byLogin(string $login): ?array
    {
        $query = $this->db->prepare("SELECT _ID, id_empleado, nombre, rol, activo,
            {$this->passwordColumn} AS password FROM {$this->table} WHERE {$this->userColumn} = ? LIMIT 2");
        $query->execute([$login]);
        $rows = $query->fetchAll();
        return count($rows) === 1 ? $rows[0] : null;
    }

    public function byId(int $id): ?array
    {
        $query = $this->db->prepare("SELECT _ID, id_empleado, nombre, rol, activo FROM {$this->table} WHERE _ID = ?");
        $query->execute([$id]);
        return $query->fetch() ?: null;
    }

    public function checkSchema(): void
    {
        $this->db->query("SELECT _ID, id_empleado, nombre, rol, activo,
            {$this->userColumn}, {$this->passwordColumn} FROM {$this->table} WHERE 1 = 0");
    }
}
