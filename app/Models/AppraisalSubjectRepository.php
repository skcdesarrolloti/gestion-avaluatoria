<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\HttpException;
use App\Support\AppraisalSubjectCatalog;
use PDO;

final class AppraisalSubjectRepository
{
    public function __construct(private PDO $db) {}

    public function find(string $appraisalId, int $owner): array
    {
        $query = $this->db->prepare('SELECT * FROM appraisal_subjects WHERE appraisal_id = ? AND owner_id = ?');
        $query->execute([$appraisalId, $owner]);
        $row = $query->fetch();
        return $row ? array_replace(AppraisalSubjectCatalog::defaults(), $row) : AppraisalSubjectCatalog::defaults();
    }

    public function save(string $appraisalId, int $owner, array $input): void
    {
        $data = $this->normalized($input);
        $this->applyLocation($data);
        $now = gmdate('Y-m-d H:i:s');
        if ($this->exists($appraisalId, $owner)) {
            $this->update($appraisalId, $owner, $data, $now);
            return;
        }
        $this->insert($appraisalId, $owner, $data, $now);
    }

    private function exists(string $appraisalId, int $owner): bool
    {
        $query = $this->db->prepare('SELECT COUNT(*) FROM appraisal_subjects WHERE appraisal_id = ? AND owner_id = ?');
        $query->execute([$appraisalId, $owner]);
        return (int) $query->fetchColumn() === 1;
    }

    private function insert(string $appraisalId, int $owner, array $data, string $now): void
    {
        $columns = array_keys($data);
        $marks = implode(', ', array_fill(0, count($columns) + 3, '?'));
        $sql = 'INSERT INTO appraisal_subjects (appraisal_id, owner_id, ' . implode(', ', $columns) . ', updated_at)
            VALUES (' . $marks . ')';
        $query = $this->db->prepare($sql);
        $query->execute([$appraisalId, $owner, ...array_values($data), $now]);
    }

    private function update(string $appraisalId, int $owner, array $data, string $now): void
    {
        $assignments = implode(', ', array_map(static fn (string $key): string => $key . ' = ?', array_keys($data)));
        $query = $this->db->prepare('UPDATE appraisal_subjects SET ' . $assignments . ',
            updated_at = ? WHERE appraisal_id = ? AND owner_id = ?');
        $query->execute([...array_values($data), $now, $appraisalId, $owner]);
    }

    private function normalized(array $input): array
    {
        $limits = ['notes' => 2000, 'subject_title' => 160, 'point_reference' => 220, 'address' => 220,
            'address_certificate' => 220, 'address_midas' => 220, 'address_tax' => 220,
            'address_deed' => 220, 'address_other' => 220, 'adopted_address' => 220,
            'alternate_nomenclature' => 160, 'property_registry' => 80, 'cadastral_reference' => 120,
            'registry_office' => 120, 'restrictions' => 220, 'legal_urban_affectations' => 220,
            'complementary_potential_uses' => 160, 'secondary_complementary_activities' => 160,
            'latitude' => 40, 'longitude' => 40];
        $data = [];
        foreach (AppraisalSubjectCatalog::textKeys() as $key) {
            $data[$key] = mb_substr(trim((string) ($input[$key] ?? '')), 0, $limits[$key] ?? 120);
        }
        foreach (AppraisalSubjectCatalog::selects() as $key => [, $options]) {
            $value = (string) ($input[$key] ?? '');
            $data[$key] = array_key_exists($value, $options) ? $value : '';
        }
        foreach (['department_id', 'city_id', 'neighborhood_id'] as $key) {
            $data[$key] = mb_substr(trim((string) ($input[$key] ?? '')), 0, 80);
        }
        $date = trim((string) ($input['subject_reference_date'] ?? ''));
        $data['subject_reference_date'] = preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) ? $date : null;
        $data['notes'] = mb_substr(trim((string) ($input['notes'] ?? '')), 0, 2000);
        return $data;
    }

    private function applyLocation(array &$data): void
    {
        foreach (['department_name', 'city_name', 'neighborhood_name', 'locality_name',
            'commune_ucg', 'zone_sector'] as $key) $data[$key] = '';
        if ($data['neighborhood_id'] !== '') {
            $row = $this->locationFromNeighborhood($data['neighborhood_id']);
            if (!$row) throw new HttpException(422, 'Selecciona un barrio o microsector válido.');
            $data['department_id'] = (string) $row['department_id'];
            $data['city_id'] = (string) $row['city_id'];
            $data['department_name'] = (string) $row['department_name'];
            $data['city_name'] = (string) $row['city_name'];
            $data['neighborhood_name'] = (string) $row['name'];
            $data['locality_name'] = (string) ($row['locality_name'] ?? '');
            $data['commune_ucg'] = (string) ($row['commune_ucg'] ?? '');
            $data['zone_sector'] = (string) ($row['zone_sector'] ?? '');
            return;
        }
        if ($data['city_id'] !== '') $this->applyCity($data);
        elseif ($data['department_id'] !== '') $this->applyDepartment($data);
    }

    private function applyCity(array &$data): void
    {
        $query = $this->db->prepare('SELECT c.id, c.name, d.id AS department_id, d.name AS department_name
            FROM master_cities c JOIN master_departments d ON d.id = c.department_id WHERE c.id = ?');
        $query->execute([$data['city_id']]);
        $row = $query->fetch();
        if (!$row) throw new HttpException(422, 'Selecciona una ciudad válida.');
        $data['department_id'] = (string) $row['department_id'];
        $data['department_name'] = (string) $row['department_name'];
        $data['city_name'] = (string) $row['name'];
    }

    private function applyDepartment(array &$data): void
    {
        $query = $this->db->prepare('SELECT name FROM master_departments WHERE id = ?');
        $query->execute([$data['department_id']]);
        $name = $query->fetchColumn();
        if (!is_string($name)) throw new HttpException(422, 'Selecciona un departamento válido.');
        $data['department_name'] = $name;
    }

    private function locationFromNeighborhood(string $id): ?array
    {
        $query = $this->db->prepare('SELECT n.*, c.name AS city_name, c.department_id,
                d.name AS department_name, l.name AS locality_name
            FROM master_neighborhoods n
            JOIN master_cities c ON c.id = n.city_id
            JOIN master_departments d ON d.id = c.department_id
            LEFT JOIN master_localities l ON l.id = n.locality_id
            WHERE n.id = ?');
        $query->execute([$id]);
        $row = $query->fetch();
        return $row ?: null;
    }
}
