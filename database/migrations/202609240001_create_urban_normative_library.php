<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->db->exec("CREATE TABLE IF NOT EXISTS urban_norm_documents (
        slug VARCHAR(100) PRIMARY KEY,
        title VARCHAR(220) NOT NULL,
        document_type VARCHAR(40) NOT NULL,
        issuer VARCHAR(160) NOT NULL DEFAULT '',
        jurisdiction VARCHAR(120) NOT NULL DEFAULT '',
        normative_reference VARCHAR(120) NOT NULL DEFAULT '',
        issued_on DATE NULL,
        status VARCHAR(40) NOT NULL DEFAULT 'vigente',
        version_label VARCHAR(80) NOT NULL DEFAULT '',
        source_url VARCHAR(500) NOT NULL DEFAULT '',
        source_filename VARCHAR(220) NOT NULL DEFAULT '',
        storage_filename VARCHAR(220) NOT NULL DEFAULT '',
        file_size_bytes BIGINT UNSIGNED NULL,
        pdf_blob LONGBLOB NULL,
        imported_at DATETIME NULL,
        summary TEXT NULL,
        sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        INDEX idx_urban_norm_documents_status (status, sort_order)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $schema->db->exec("CREATE TABLE IF NOT EXISTS urban_norm_tables (
        slug VARCHAR(120) PRIMARY KEY,
        document_slug VARCHAR(100) NOT NULL,
        table_code VARCHAR(40) NOT NULL,
        title VARCHAR(220) NOT NULL,
        scope VARCHAR(160) NOT NULL DEFAULT '',
        page_start SMALLINT UNSIGNED NULL,
        page_end SMALLINT UNSIGNED NULL,
        sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        INDEX idx_urban_norm_tables_doc (document_slug, sort_order),
        CONSTRAINT fk_urban_norm_tables_doc FOREIGN KEY (document_slug)
            REFERENCES urban_norm_documents (slug)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $schema->db->exec("CREATE TABLE IF NOT EXISTS urban_norm_use_categories (
        slug VARCHAR(140) PRIMARY KEY,
        table_slug VARCHAR(120) NOT NULL,
        code VARCHAR(60) NOT NULL,
        name VARCHAR(180) NOT NULL,
        activity_group VARCHAR(80) NOT NULL DEFAULT '',
        description TEXT NULL,
        sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        INDEX idx_urban_norm_categories_table (table_slug, sort_order),
        INDEX idx_urban_norm_categories_code (code),
        CONSTRAINT fk_urban_norm_categories_table FOREIGN KEY (table_slug)
            REFERENCES urban_norm_tables (slug)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $schema->db->exec("CREATE TABLE IF NOT EXISTS urban_norm_use_rules (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        category_slug VARCHAR(140) NOT NULL,
        rule_type VARCHAR(30) NOT NULL,
        content TEXT NOT NULL,
        sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        INDEX idx_urban_norm_rules_category (category_slug, sort_order),
        CONSTRAINT fk_urban_norm_rules_category FOREIGN KEY (category_slug)
            REFERENCES urban_norm_use_categories (slug)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $schema->db->exec("CREATE TABLE IF NOT EXISTS urban_norm_parameters (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        category_slug VARCHAR(140) NOT NULL,
        parameter_key VARCHAR(60) NOT NULL,
        label VARCHAR(140) NOT NULL,
        value_text TEXT NOT NULL,
        sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        INDEX idx_urban_norm_parameters_category (category_slug, sort_order),
        CONSTRAINT fk_urban_norm_parameters_category FOREIGN KEY (category_slug)
            REFERENCES urban_norm_use_categories (slug)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
};
