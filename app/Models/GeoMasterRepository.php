<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\HttpException;
use PDO;

final class GeoMasterRepository
{
    public function __construct(private PDO $db) {}

    public function departments(): array
    {
        return $this->db->query("SELECT * FROM master_departments
            ORDER BY CASE WHEN active = 'Si' THEN 0 ELSE 1 END, name")->fetchAll();
    }

    public function cities(): array
    {
        return $this->db->query("SELECT c.*, d.name AS department_name FROM master_cities c
            JOIN master_departments d ON d.id = c.department_id
            ORDER BY d.name, CASE WHEN c.active = 'Si' THEN 0 ELSE 1 END, c.name")->fetchAll();
    }

    public function neighborhoods(): array
    {
        return $this->db->query("SELECT n.*, c.name AS city_name, d.name AS department_name,
                l.name AS locality_name
            FROM master_neighborhoods n
            JOIN master_cities c ON c.id = n.city_id
            JOIN master_departments d ON d.id = c.department_id
            LEFT JOIN master_localities l ON l.id = n.locality_id
            ORDER BY d.name, c.name, CASE WHEN n.active = 'Si' THEN 0 ELSE 1 END, n.name")->fetchAll();
    }

    public function localities(): array
    {
        return $this->db->query("SELECT l.*, c.name AS city_name, d.name AS department_name
            FROM master_localities l
            JOIN master_cities c ON c.id = l.city_id
            JOIN master_departments d ON d.id = c.department_id
            ORDER BY d.name, c.name, CASE WHEN l.active = 'Si' THEN 0 ELSE 1 END, l.name")->fetchAll();
    }

    public function createDepartment(array $data): void
    {
        $this->insert('master_departments', ['id', 'code', 'name', 'active', 'created_at', 'updated_at'], [
            bin2hex(random_bytes(16)), $data['code'], $data['name'], $data['active'],
        ]);
    }

    public function createCity(array $data): void
    {
        $this->assertDepartment((string) $data['department_id']);
        $this->insert('master_cities',
            ['id', 'department_id', 'code', 'name', 'active', 'created_at', 'updated_at'],
            [bin2hex(random_bytes(16)), $data['department_id'], $data['code'], $data['name'], $data['active']]);
    }

    public function createNeighborhood(array $data): void
    {
        $this->assertCity((string) $data['city_id']);
        $localityId = $this->localityId((string) $data['city_id'], (string) ($data['locality_name'] ?? ''));
        $this->insert('master_neighborhoods',
            ['id', 'city_id', 'locality_id', 'name', 'commune_ucg', 'zone_sector',
                'active', 'notes', 'created_at', 'updated_at'],
            [bin2hex(random_bytes(16)), $data['city_id'], $localityId,
                $data['name'], $data['commune_ucg'], $data['zone_sector'], $data['active'], $data['notes']]);
    }

    public function createLocality(array $data): void
    {
        $this->assertCity((string) $data['city_id']);
        $this->insert('master_localities',
            ['id', 'city_id', 'name', 'active', 'notes', 'created_at', 'updated_at'],
            [bin2hex(random_bytes(16)), $data['city_id'], $data['name'], $data['active'], $data['notes']]);
    }

    public function assertDepartment(string $id): void
    {
        $query = $this->db->prepare('SELECT COUNT(*) FROM master_departments WHERE id = ?');
        $query->execute([$id]);
        if ((int) $query->fetchColumn() !== 1) throw new HttpException(422, 'Selecciona un departamento válido.');
    }

    public function assertCity(string $id): void
    {
        $query = $this->db->prepare('SELECT COUNT(*) FROM master_cities WHERE id = ?');
        $query->execute([$id]);
        if ((int) $query->fetchColumn() !== 1) throw new HttpException(422, 'Selecciona una ciudad válida.');
    }

    private function assertLocalityForCity(string $id, string $cityId): void
    {
        $query = $this->db->prepare('SELECT COUNT(*) FROM master_localities WHERE id = ? AND city_id = ?');
        $query->execute([$id, $cityId]);
        if ((int) $query->fetchColumn() !== 1) throw new HttpException(422, 'Selecciona una localidad válida para la ciudad.');
    }

    private function localityId(string $cityId, string $name): ?string
    {
        $name = trim($name);
        if ($name === '') return null;
        $query = $this->db->prepare('SELECT id FROM master_localities WHERE city_id = ? AND name = ?');
        $query->execute([$cityId, $name]);
        $id = $query->fetchColumn();
        if (is_string($id) && $id !== '') return $id;
        $id = bin2hex(random_bytes(16));
        $this->insert('master_localities',
            ['id', 'city_id', 'name', 'active', 'notes', 'created_at', 'updated_at'],
            [$id, $cityId, $name, 'Si', 'Creada desde barrio/microsector.']);
        return $id;
    }

    private function insert(string $table, array $columns, array $values): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $marks = implode(', ', array_fill(0, count($columns), '?'));
        $query = $this->db->prepare('INSERT INTO ' . $table . ' (' . implode(', ', $columns) . ') VALUES (' . $marks . ')');
        $query->execute([...$values, $now, $now]);
    }
}
