<?php
declare(strict_types=1);
use App\Database\Schema;
return static function (Schema $schema): void {
    $schema->db->exec("CREATE TABLE IF NOT EXISTS judicial_expert_profiles (
        appraiser_id CHAR(32) NOT NULL, owner_id BIGINT UNSIGNED NOT NULL,
        payload MEDIUMTEXT NOT NULL, version INT UNSIGNED NOT NULL DEFAULT 1,
        updated_at DATETIME NOT NULL, PRIMARY KEY (appraiser_id, owner_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $schema->db->exec("CREATE TABLE IF NOT EXISTS appraisal_judicial_records (
        appraisal_id CHAR(32) NOT NULL, owner_id BIGINT UNSIGNED NOT NULL,
        appraiser_id CHAR(32) NOT NULL, payload MEDIUMTEXT NOT NULL,
        version INT UNSIGNED NOT NULL DEFAULT 1, presented_on DATE NULL,
        snapshot MEDIUMTEXT NULL, updated_at DATETIME NOT NULL,
        PRIMARY KEY (appraisal_id, owner_id),
        INDEX idx_judicial_expert_history (owner_id, appraiser_id, presented_on)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
};
