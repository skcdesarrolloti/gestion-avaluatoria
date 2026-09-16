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

    public function create(array $data): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $query = $this->db->prepare('INSERT INTO valuation_appraisers
            (id, code, full_name, email, phone, raa_number, raa_categories, active, notes,
            raa_expires_at, raa_source_filename, raa_storage_filename, raa_file_size_bytes,
            raa_uploaded_at, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $query->execute([$data['id'], $data['code'], $data['full_name'],
            $data['email'], $data['phone'], $data['raa_number'], $data['raa_categories'],
            $data['active'], $data['notes'], $data['raa_expires_at'], $data['raa_source_filename'],
            $data['raa_storage_filename'], $data['raa_file_size_bytes'], $now, $now, $now]);
    }

    public function find(string $id): array
    {
        $query = $this->db->prepare('SELECT * FROM valuation_appraisers WHERE id = ?');
        $query->execute([$id]);
        $row = $query->fetch();
        if (!$row) throw new HttpException(404, 'No se encontró el perito.');
        return $row;
    }

    public static function raaPath(string $filename): string
    {
        return AppraiserRaaStorage::path($filename);
    }
}
