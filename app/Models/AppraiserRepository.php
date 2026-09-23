<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\HttpException;
use App\Services\AppraiserRaaStorage;
use PDO;

final class AppraiserRepository
{
    public function __construct(private PDO $db) {}

    public function all(): array
    {
        return $this->db->query("SELECT * FROM valuation_appraisers
            ORDER BY CASE WHEN active = 'Si' THEN 0 ELSE 1 END ASC, code ASC, full_name ASC")->fetchAll();
    }

    public function eligibleForAssignment(): array
    {
        $query = $this->db->prepare("SELECT * FROM valuation_appraisers
            WHERE active = 'Si' AND (raa_expires_at IS NOT NULL AND raa_expires_at >= ?)
            ORDER BY code ASC, full_name ASC");
        $query->execute([$this->today()]);
        return $query->fetchAll();
    }

    public function findByRaaIdentity(string $identification, string $raaNumber): ?array
    {
        $query = $this->db->prepare('SELECT * FROM valuation_appraisers
            WHERE (identification_number IS NOT NULL AND identification_number = ?) OR raa_number = ? LIMIT 1');
        $query->execute([$identification, $raaNumber]);
        return $query->fetch() ?: null;
    }

    public function nextCode(): string
    {
        $max = 0;
        foreach ($this->db->query('SELECT code FROM valuation_appraisers')->fetchAll() as $row) {
            if (preg_match('/^\d+$/', (string) $row['code'])) $max = max($max, (int) $row['code']);
        }
        return str_pad((string) ($max + 1), 2, '0', STR_PAD_LEFT);
    }

    public function create(array $data): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $query = $this->db->prepare('INSERT INTO valuation_appraisers
            (id, code, full_name, identification_number, email, phone, raa_number, raa_categories, raa_contact_city, raa_contact_department, raa_contact_address,
            active, notes, raa_issued_at, raa_expires_at, raa_pin, raa_source_filename,
            raa_storage_filename, raa_file_size_bytes, raa_uploaded_at, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $query->execute([$data['id'], $data['code'], $data['full_name'], $this->nullable($data['identification_number'] ?? ''),
            $data['email'], $data['phone'], $data['raa_number'], $data['raa_categories'], $data['raa_contact_city'],
            $data['raa_contact_department'], $data['raa_contact_address'], $data['active'], $data['notes'], $data['raa_issued_at'], $data['raa_expires_at'], $data['raa_pin'],
            $data['raa_source_filename'], $data['raa_storage_filename'], $data['raa_file_size_bytes'],
            $now, $now, $now]);
    }

    public function updateRaa(string $id, array $data): void
    {
        $query = $this->db->prepare('UPDATE valuation_appraisers SET identification_number = ?, email = ?, phone = ?, raa_number = ?,
            raa_categories = ?, raa_contact_city = ?, raa_contact_department = ?, raa_contact_address = ?,
            active = ?, notes = ?, raa_issued_at = ?, raa_expires_at = ?, raa_pin = ?,
            raa_source_filename = ?, raa_storage_filename = ?, raa_file_size_bytes = ?, raa_uploaded_at = ?,
            updated_at = ? WHERE id = ?');
        $now = gmdate('Y-m-d H:i:s');
        $query->execute([$this->nullable($data['identification_number'] ?? ''), $data['email'], $data['phone'], $data['raa_number'], $data['raa_categories'], $data['raa_contact_city'],
            $data['raa_contact_department'], $data['raa_contact_address'], $data['active'], $data['notes'], $data['raa_issued_at'], $data['raa_expires_at'], $data['raa_pin'],
            $data['raa_source_filename'], $data['raa_storage_filename'], $data['raa_file_size_bytes'], $now, $now, $id]);
    }

    public function find(string $id): array
    {
        $query = $this->db->prepare('SELECT * FROM valuation_appraisers WHERE id = ?');
        $query->execute([$id]);
        $row = $query->fetch();
        if (!$row) throw new HttpException(404, 'No se encontró el perito.');
        return $row;
    }

    public static function raaPath(string $filename): string { return AppraiserRaaStorage::path($filename); }
    private function nullable(string $value): ?string { $value = trim($value); return $value === '' ? null : $value; }
    private function today(): string { return (new \DateTimeImmutable('today', new \DateTimeZone('America/Bogota')))->format('Y-m-d'); }
}
