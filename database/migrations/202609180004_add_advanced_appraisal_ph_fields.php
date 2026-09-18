<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('appraisal_ph_profiles', 'ph_typology',
        "VARCHAR(60) NOT NULL DEFAULT '' AFTER ph_name");
    $schema->addColumn('appraisal_ph_profiles', 'linkage_json',
        'TEXT NULL AFTER ph_typology');
    $schema->addColumn('appraisal_ph_profiles', 'technical_json',
        'MEDIUMTEXT NULL AFTER photos_json');
    $schema->addColumn('appraisal_ph_profiles', 'source_summary',
        'TEXT NULL AFTER technical_json');
    $schema->addColumn('appraisal_ph_profiles', 'findings_json',
        'TEXT NULL AFTER source_summary');
    $schema->db->exec("CREATE TABLE IF NOT EXISTS appraisal_ph_documents (
        id CHAR(32) PRIMARY KEY,
        appraisal_id CHAR(32) NOT NULL,
        owner_id BIGINT UNSIGNED NOT NULL,
        source_filename VARCHAR(220) NOT NULL,
        storage_filename VARCHAR(260) NOT NULL,
        mime_type VARCHAR(120) NOT NULL,
        file_size_bytes INT UNSIGNED NOT NULL,
        extracted_chars INT UNSIGNED NOT NULL DEFAULT 0,
        analysis_status VARCHAR(80) NOT NULL DEFAULT '',
        analysis_message VARCHAR(255) NOT NULL DEFAULT '',
        file_blob LONGBLOB NULL,
        created_at DATETIME NOT NULL,
        INDEX idx_appraisal_ph_documents_owner (owner_id, appraisal_id, created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
};
