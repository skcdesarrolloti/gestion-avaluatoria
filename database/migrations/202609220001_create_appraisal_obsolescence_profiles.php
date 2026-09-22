<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->db->exec("CREATE TABLE IF NOT EXISTS appraisal_obsolescence_profiles (
        appraisal_id CHAR(32) NOT NULL,
        owner_id BIGINT UNSIGNED NOT NULL,
        summary_text TEXT NULL,
        diagnosis_text TEXT NULL,
        quantification_text TEXT NULL,
        normative_text TEXT NULL,
        factors_json MEDIUMTEXT NULL,
        updated_at DATETIME NOT NULL,
        PRIMARY KEY (appraisal_id, owner_id),
        INDEX idx_appraisal_obsolescence_owner (owner_id, updated_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
};
