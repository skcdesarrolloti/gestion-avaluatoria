<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->db->exec("CREATE TABLE IF NOT EXISTS appraisal_units (
        id CHAR(32) PRIMARY KEY,
        appraisal_id CHAR(32) NOT NULL,
        owner_id BIGINT UNSIGNED NOT NULL,
        unit_kind VARCHAR(20) NOT NULL,
        unit_index SMALLINT UNSIGNED NOT NULL,
        label VARCHAR(120) NOT NULL DEFAULT '',
        igac_category VARCHAR(40) NOT NULL DEFAULT '',
        igac_typology_hint VARCHAR(190) NOT NULL DEFAULT '',
        notes TEXT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        UNIQUE KEY uq_appraisal_unit (appraisal_id, unit_kind, unit_index),
        INDEX idx_appraisal_units_owner (owner_id, appraisal_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
};
