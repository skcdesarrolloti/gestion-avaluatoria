<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->db->exec("CREATE TABLE IF NOT EXISTS appraisal_sector_midas_files (
        id CHAR(32) PRIMARY KEY,
        appraisal_id CHAR(32) NOT NULL,
        owner_id BIGINT UNSIGNED NOT NULL,
        neighborhood_id VARCHAR(80) NOT NULL DEFAULT '',
        layer_group VARCHAR(120) NOT NULL DEFAULT '',
        source_filename VARCHAR(220) NOT NULL,
        storage_filename VARCHAR(220) NOT NULL,
        mime_type VARCHAR(120) NOT NULL DEFAULT '',
        file_size_bytes BIGINT UNSIGNED NOT NULL DEFAULT 0,
        notes TEXT NULL,
        file_blob LONGBLOB NULL,
        created_at DATETIME NOT NULL,
        INDEX idx_sector_midas_appraisal (appraisal_id, owner_id, created_at),
        INDEX idx_sector_midas_neighborhood (neighborhood_id, created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
};
