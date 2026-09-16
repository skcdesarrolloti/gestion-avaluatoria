<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->db->exec("CREATE TABLE IF NOT EXISTS valuation_appraisers (
        id CHAR(32) PRIMARY KEY,
        code VARCHAR(10) NOT NULL,
        full_name VARCHAR(160) NOT NULL,
        email VARCHAR(190) NOT NULL DEFAULT '',
        phone VARCHAR(50) NOT NULL DEFAULT '',
        raa_number VARCHAR(80) NOT NULL DEFAULT '',
        raa_categories TEXT NULL,
        active ENUM('Si','No') NOT NULL DEFAULT 'Si',
        notes TEXT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        UNIQUE KEY uq_valuation_appraisers_code (code),
        INDEX idx_valuation_appraisers_active_name (active, full_name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
};
