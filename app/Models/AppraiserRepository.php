<?php
declare(strict_types=1);
namespace App\Models;
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
            (id, code, full_name, email, phone, raa_number, raa_categories, active, notes, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $query->execute([bin2hex(random_bytes(16)), $data['code'], $data['full_name'],
            $data['email'], $data['phone'], $data['raa_number'], $data['raa_categories'],
            $data['active'], $data['notes'], $now, $now]);
    }
}
