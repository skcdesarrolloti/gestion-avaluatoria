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

    public function searchByRegistry(string $term, int $owner, string $excludeId = '', int $limit = 8): array
    {
        $needle = '%' . trim($term) . '%';
        if ($needle === '%%') return [];
        $query = $this->db->prepare('SELECT a.id, a.titulo, a.direccion, a.municipio, a.client_name,
                a.property_owner_name, a.updated_at, s.subject_title, s.address, s.city_name,
                s.neighborhood_name, s.property_registry, s.cadastral_reference, s.registry_office
            FROM appraisal_subjects s JOIN appraisals a ON a.id = s.appraisal_id AND a.owner_id = s.owner_id
            WHERE s.owner_id = ? AND s.appraisal_id <> ?
                AND (s.property_registry LIKE ? OR s.cadastral_reference LIKE ?)
            ORDER BY a.updated_at DESC, a.id DESC LIMIT ' . max(1, min(12, $limit)));
        $query->execute([$owner, $excludeId, $needle, $needle]);
        return $query->fetchAll();
    }

    public function applyMidasPredio(string $appraisalId, int $owner, array $predio): void
    {
        if ($predio === []) return;
        $current = $this->find($appraisalId, $owner);
        $data = ['address_midas' => $predio['address'] ?? '', 'midas_updated_on' => $this->date((string) ($predio['updated_on'] ?? '')), 'midas_predio_raw' => $predio['_raw'] ?? ''];
        $sourceMap = ['midas_national_cadastral_reference' => 'national_cadastral_reference',
            'midas_property_registry' => 'property_registry', 'midas_address' => 'address', 'midas_cadastral_reference' => 'cadastral_reference',
            'midas_territory' => 'territory', 'midas_locality' => 'locality', 'midas_commune_ucg' => 'commune_ucg', 'midas_land_use' => 'land_use',
            'midas_urban_treatment' => 'urban_treatment', 'midas_risk' => 'risk', 'midas_land_classification' => 'land_classification', 'midas_dane_block_code' => 'dane_block_code', 'midas_dane_block_side' => 'dane_block_side',
            'midas_block_number' => 'block_number', 'midas_property_number' => 'property_number', 'midas_stratum' => 'stratum', 'midas_stratum_record' => 'stratum_record', 'midas_stratum_atypical' => 'stratum_atypical',
            'midas_stratum_observation' => 'stratum_observation', 'midas_building_name' => 'building_name', 'midas_land_area_m2' => 'land_area_m2', 'midas_built_area_m2' => 'built_area_m2'];
        foreach ($sourceMap as $target => $source) $data[$target] = $predio[$source] ?? '';
        $fillable = ['neighborhood_name' => $predio['territory'] ?? '', 'locality_name' => $predio['locality'] ?? '',
            'commune_ucg' => $predio['commune_ucg'] ?? '', 'zone_sector' => $predio['land_use'] ?? '',
            'property_registry' => $predio['property_registry'] ?? '', 'cadastral_reference' => $predio['cadastral_reference'] ?? '', 'stratum' => $this->stratum((string) ($predio['stratum'] ?? '')),
            'subject_reference_date' => $this->date((string) ($predio['updated_on'] ?? ''))];
        foreach ($fillable as $key => $value) if (($current[$key] ?? '') === '') $data[$key] = $value;
        foreach ($data as $key => $value) if ($value === '' || $value === null) unset($data[$key]);
        if ($data === []) return;
        if (!$this->exists($appraisalId, $owner)) {
            $this->insert($appraisalId, $owner, array_replace(AppraisalSubjectCatalog::defaults(), $data), gmdate('Y-m-d H:i:s'));
            return;
        }
        $set = implode(', ', array_map(static fn (string $key): string => $key . ' = ?', array_keys($data)));
        $query = $this->db->prepare('UPDATE appraisal_subjects SET ' . $set
            . ', updated_at = ? WHERE appraisal_id = ? AND owner_id = ?');
        $query->execute([...array_values($data), gmdate('Y-m-d H:i:s'), $appraisalId, $owner]);
    }

    private function stratum(string $value): string { $digits = preg_replace('/\D+/', '', $value) ?? ''; return in_array($digits, ['1','2','3','4','5','6'], true) ? $digits : ''; }
    private function date(string $value): ?string { return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value : null; }

    public function searchByNeighborhood(string $neighborhoodId, int $owner, string $excludeId = '', int $limit = 8): array
    {
        if (trim($neighborhoodId) === '') return [];
        $query = $this->db->prepare('SELECT a.id, a.titulo, a.direccion, a.municipio, a.client_name,
                a.property_owner_name, a.updated_at, s.subject_title, s.address, s.city_name,
                s.neighborhood_name, s.property_registry, s.cadastral_reference, s.registry_office
            FROM appraisal_subjects s JOIN appraisals a ON a.id = s.appraisal_id AND a.owner_id = s.owner_id
            WHERE s.owner_id = ? AND s.appraisal_id <> ? AND s.neighborhood_id = ?
            ORDER BY a.updated_at DESC, a.id DESC LIMIT ' . max(1, min(12, $limit)));
        $query->execute([$owner, $excludeId, $neighborhoodId]);
        return $query->fetchAll();
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
            'registry_office' => 120, 'predial_base_value' => 80, 'predial_destination_code' => 20,
            'predial_destination_description' => 160, 'predial_rate_per_mille' => 40,
            'predial_bill_source' => 220, 'restrictions' => 220, 'legal_urban_affectations' => 220,
            'midas_national_cadastral_reference' => 120, 'midas_property_registry' => 80,
            'midas_address' => 220, 'midas_cadastral_reference' => 120, 'midas_territory' => 160,
            'midas_locality' => 160, 'midas_commune_ucg' => 80, 'midas_land_use' => 120,
            'midas_urban_treatment' => 160, 'midas_risk' => 220, 'midas_land_classification' => 120, 'midas_dane_block_code' => 80, 'midas_dane_block_side' => 40,
            'midas_block_number' => 80, 'midas_property_number' => 80, 'midas_stratum' => 20, 'midas_stratum_record' => 160,
            'midas_stratum_atypical' => 80, 'midas_stratum_observation' => 220,
            'midas_building_name' => 180, 'midas_land_area_m2' => 40, 'midas_built_area_m2' => 40,
            'midas_predio_raw' => 70000,
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
        $date = trim((string) ($input['midas_updated_on'] ?? ''));
        $data['midas_updated_on'] = preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) ? $date : null;
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
