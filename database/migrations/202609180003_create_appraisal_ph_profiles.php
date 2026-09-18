<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->db->exec("CREATE TABLE IF NOT EXISTS appraisal_ph_profiles (
        appraisal_id CHAR(32) PRIMARY KEY,
        owner_id BIGINT UNSIGNED NOT NULL,
        ph_key VARCHAR(190) NOT NULL DEFAULT '',
        ph_name VARCHAR(190) NOT NULL DEFAULT '',
        administration_name VARCHAR(190) NOT NULL DEFAULT '',
        administration_contact VARCHAR(190) NOT NULL DEFAULT '',
        administration_phone VARCHAR(80) NOT NULL DEFAULT '',
        administration_email VARCHAR(190) NOT NULL DEFAULT '',
        matrix_registration VARCHAR(120) NOT NULL DEFAULT '',
        private_unit VARCHAR(190) NOT NULL DEFAULT '',
        coefficient VARCHAR(80) NOT NULL DEFAULT '',
        regulation_document TEXT NULL,
        reform_documents TEXT NULL,
        monthly_fee VARCHAR(80) NOT NULL DEFAULT '',
        fee_status VARCHAR(60) NOT NULL DEFAULT '',
        reserve_fund VARCHAR(120) NOT NULL DEFAULT '',
        insurance_status VARCHAR(120) NOT NULL DEFAULT '',
        restrictions_text TEXT NULL,
        common_areas_json TEXT NULL,
        documents_json TEXT NULL,
        risks_json TEXT NULL,
        photos_json TEXT NULL,
        diagnosis_text TEXT NULL,
        report_text TEXT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        INDEX idx_appraisal_ph_owner (owner_id, ph_key)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
};
