<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('appraisals', 'comparables_version', 'INT UNSIGNED NOT NULL DEFAULT 0');
    $schema->addColumn('appraisal_comparables', 'ph_regime', "VARCHAR(20) NOT NULL DEFAULT 'por_verificar'");
    // No cascading FK: the existing collection save replaces rows, retaining their IDs.
    $schema->db->exec("CREATE TABLE IF NOT EXISTS appraisal_comparable_photos (
        id CHAR(32) PRIMARY KEY,
        appraisal_id CHAR(32) NOT NULL,
        comparable_id CHAR(32) NOT NULL,
        owner_id BIGINT UNSIGNED NOT NULL,
        source_filename VARCHAR(190) NOT NULL,
        mime_type VARCHAR(40) NOT NULL,
        caption VARCHAR(300) NOT NULL DEFAULT '',
        file_hash CHAR(64) NOT NULL,
        file_blob MEDIUMBLOB NOT NULL,
        created_at DATETIME NOT NULL,
        UNIQUE KEY uq_comparable_photo_hash (appraisal_id, comparable_id, owner_id, file_hash),
        INDEX idx_comparable_photos_owner (owner_id, appraisal_id, comparable_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
};
