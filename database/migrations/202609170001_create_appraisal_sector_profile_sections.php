<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->db->exec("CREATE TABLE IF NOT EXISTS appraisal_sector_profile_sections (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        appraisal_id CHAR(32) NOT NULL,
        owner_id BIGINT UNSIGNED NOT NULL,
        neighborhood_id CHAR(32) NULL,
        section_code VARCHAR(10) NOT NULL,
        section_title VARCHAR(220) NOT NULL,
        status VARCHAR(60) NOT NULL DEFAULT 'En construcción',
        version INT UNSIGNED NOT NULL DEFAULT 1,
        data_json LONGTEXT NULL,
        content_text TEXT NULL,
        updated_at DATETIME NOT NULL,
        UNIQUE KEY uq_appraisal_sector_section (appraisal_id, section_code),
        INDEX idx_appraisal_sector_section_owner (owner_id, updated_at),
        INDEX idx_appraisal_sector_section_neighborhood (neighborhood_id, section_code)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
};
