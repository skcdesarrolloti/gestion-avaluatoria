<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->db->exec("CREATE TABLE IF NOT EXISTS appraisal_legal_profiles (
        appraisal_id CHAR(32) PRIMARY KEY,
        owner_id BIGINT UNSIGNED NOT NULL,
        source_certificate_id CHAR(32) NULL,
        status VARCHAR(80) NOT NULL DEFAULT 'Pendiente de revisión',
        data_json LONGTEXT NULL,
        annotations_json LONGTEXT NULL,
        alerts_json LONGTEXT NULL,
        extracted_text LONGTEXT NULL,
        updated_at DATETIME NOT NULL,
        INDEX idx_legal_profile_owner (owner_id, updated_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $schema->db->exec("CREATE TABLE IF NOT EXISTS appraisal_legal_certificates (
        id CHAR(32) PRIMARY KEY,
        appraisal_id CHAR(32) NOT NULL,
        owner_id BIGINT UNSIGNED NOT NULL,
        source_filename VARCHAR(220) NOT NULL,
        storage_filename VARCHAR(220) NOT NULL,
        mime_type VARCHAR(120) NOT NULL DEFAULT '',
        file_size_bytes BIGINT UNSIGNED NOT NULL DEFAULT 0,
        extracted_chars INT UNSIGNED NOT NULL DEFAULT 0,
        analysis_status VARCHAR(80) NOT NULL DEFAULT 'pendiente',
        analysis_message TEXT NULL,
        file_blob LONGBLOB NULL,
        created_at DATETIME NOT NULL,
        INDEX idx_legal_cert_appraisal (appraisal_id, owner_id, created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
};
