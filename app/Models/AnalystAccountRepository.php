<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\HttpException;
use PDO;
final class AnalystAccountRepository
{
    public function __construct(private PDO $db) {}
    public function byLogin(string $login): ?array {
        $q=$this->db->prepare('SELECT * FROM analyst_accounts WHERE username = ?'); $q->execute([$login]);
        return $q->fetch() ?: null;
    }
    public function find(string $id): ?array {
        $q=$this->db->prepare('SELECT * FROM analyst_accounts WHERE id = ?'); $q->execute([$id]); return $q->fetch() ?: null;
    }
    public function forOwner(int $owner): array {
        $q=$this->db->prepare('SELECT id, username, full_name, appraiser_id, active, must_change FROM analyst_accounts WHERE owner_id = ? ORDER BY created_at, id');
        $q->execute([$owner]); return $q->fetchAll();
    }
    public function create(int $owner, string $expert, string $name, string $username): string {
        if (!preg_match('/^[a-z][a-z0-9._-]{2,79}$/D', $username) || trim($name)==='' || mb_strlen($name)>160)
            throw new HttpException(422, 'Escribe nombre y usuario: de 3 a 80 caracteres, inicial y apellido, sin espacios.');
        $id=bin2hex(random_bytes(16));
        $q=$this->db->prepare('INSERT INTO analyst_accounts (id, owner_id, appraiser_id, full_name, username, password_hash, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $q->execute([$id,$owner,$expert,trim($name),$username,password_hash($username,PASSWORD_DEFAULT),gmdate('Y-m-d H:i:s')]); return $id;
    }
    public function revoke(string $id, int $owner): void {
        $q=$this->db->prepare('UPDATE analyst_accounts SET active = 0, auth_version = auth_version + 1 WHERE id = ? AND owner_id = ?'); $q->execute([$id,$owner]);
        if (!$q->rowCount()) throw new HttpException(404, 'Acceso no encontrado.');
    }
    public function changePassword(string $id, int $version, string $password): void {
        $row=$this->find($id);
        if (!$row || !$row['active']) throw new HttpException(403,'Acceso desactivado.');
        if (strlen($password)<12 || strlen($password)>72 || strcasecmp($password,$row['username'])===0)
            throw new HttpException(422,'Usa de 12 a 72 caracteres y una contraseña distinta del usuario.');
        $q=$this->db->prepare('UPDATE analyst_accounts SET password_hash = ?, must_change = 0, auth_version = auth_version + 1 WHERE id = ? AND active = 1 AND auth_version = ?');
        $q->execute([password_hash($password,PASSWORD_DEFAULT),$id,$version]);
        if (!$q->rowCount()) throw new HttpException(409,'El acceso cambió. Inicia sesión nuevamente.');
    }
    public function requireRecord(string $id, array $user): void {
        $q=$this->db->prepare('SELECT appraiser_id FROM appraisals WHERE id = ? AND owner_id = ? AND analyst_account_id = ?');
        $q->execute([$id,$user['id'],$user['analyst_id']]); $row=$q->fetch();
        if (!$row || $row['appraiser_id'] !== $user['responsible_appraiser_id']) throw new HttpException(404,'No tienes asignado este avalúo.');
    }
}
