<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->db->exec("CREATE TABLE IF NOT EXISTS appraisal_urban_norm_profiles (
        appraisal_id CHAR(32) PRIMARY KEY,
        owner_id BIGINT UNSIGNED NOT NULL,
        document_slug VARCHAR(100) NULL,
        table_slug VARCHAR(120) NULL,
        category_slug VARCHAR(140) NULL,
        source_status VARCHAR(60) NOT NULL DEFAULT 'pendiente',
        pot_state VARCHAR(80) NOT NULL DEFAULT '',
        midas_consulted TINYINT(1) NOT NULL DEFAULT 0,
        midas_consulted_on DATE NULL,
        midas_layers VARCHAR(500) NOT NULL DEFAULT '',
        midas_result TEXT NULL,
        planning_concept_number VARCHAR(120) NOT NULL DEFAULT '',
        planning_concept_date DATE NULL,
        land_classification VARCHAR(120) NOT NULL DEFAULT '',
        activity_area VARCHAR(160) NOT NULL DEFAULT '',
        normative_zone VARCHAR(160) NOT NULL DEFAULT '',
        urban_treatment VARCHAR(160) NOT NULL DEFAULT '',
        current_use VARCHAR(160) NOT NULL DEFAULT '',
        intended_use VARCHAR(220) NOT NULL DEFAULT '',
        applicable_activity VARCHAR(160) NOT NULL DEFAULT '',
        use_cross_result VARCHAR(40) NOT NULL DEFAULT '',
        restrictions TEXT NULL,
        conclusion TEXT NULL,
        support_summary TEXT NULL,
        analyst_notes TEXT NULL,
        version INT UNSIGNED NOT NULL DEFAULT 0,
        updated_at DATETIME NOT NULL,
        INDEX idx_appraisal_urban_norm_owner (owner_id, updated_at),
        INDEX idx_appraisal_urban_norm_category (category_slug),
        CONSTRAINT fk_appraisal_urban_norm_document FOREIGN KEY (document_slug)
            REFERENCES urban_norm_documents (slug),
        CONSTRAINT fk_appraisal_urban_norm_table FOREIGN KEY (table_slug)
            REFERENCES urban_norm_tables (slug),
        CONSTRAINT fk_appraisal_urban_norm_category FOREIGN KEY (category_slug)
            REFERENCES urban_norm_use_categories (slug)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $schema->db->exec("CREATE TABLE IF NOT EXISTS appraisal_urban_norm_references (
        id CHAR(32) PRIMARY KEY,
        appraisal_id CHAR(32) NOT NULL,
        owner_id BIGINT UNSIGNED NOT NULL,
        document_slug VARCHAR(100) NULL,
        table_slug VARCHAR(120) NULL,
        category_slug VARCHAR(140) NULL,
        reference_type VARCHAR(40) NOT NULL DEFAULT '',
        source_label VARCHAR(220) NOT NULL DEFAULT '',
        source_date DATE NULL,
        extracted_text MEDIUMTEXT NULL,
        support_filename VARCHAR(220) NOT NULL DEFAULT '',
        storage_filename VARCHAR(220) NOT NULL DEFAULT '',
        file_size_bytes BIGINT UNSIGNED NULL,
        pdf_blob LONGBLOB NULL,
        created_at DATETIME NOT NULL,
        INDEX idx_appraisal_urban_refs_appraisal (appraisal_id, owner_id, created_at),
        CONSTRAINT fk_appraisal_urban_refs_document FOREIGN KEY (document_slug)
            REFERENCES urban_norm_documents (slug),
        CONSTRAINT fk_appraisal_urban_refs_table FOREIGN KEY (table_slug)
            REFERENCES urban_norm_tables (slug),
        CONSTRAINT fk_appraisal_urban_refs_category FOREIGN KEY (category_slug)
            REFERENCES urban_norm_use_categories (slug)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
};
