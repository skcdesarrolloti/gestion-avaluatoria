<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->db->exec("CREATE TABLE IF NOT EXISTS appraisal_comparables (
        id CHAR(32) PRIMARY KEY,
        appraisal_id CHAR(32) NOT NULL,
        owner_id BIGINT UNSIGNED NOT NULL,
        sample_index TINYINT UNSIGNED NOT NULL,
        active VARCHAR(20) NOT NULL DEFAULT 'si',
        status VARCHAR(40) NOT NULL DEFAULT 'por_verificar',
        source_type VARCHAR(40) NOT NULL DEFAULT '',
        source_name VARCHAR(140) NOT NULL DEFAULT '',
        source_url VARCHAR(700) NOT NULL DEFAULT '',
        query_used VARCHAR(500) NOT NULL DEFAULT '',
        operation VARCHAR(40) NOT NULL DEFAULT '',
        property_type VARCHAR(80) NOT NULL DEFAULT '',
        neighborhood VARCHAR(140) NOT NULL DEFAULT '',
        address_hint VARCHAR(180) NOT NULL DEFAULT '',
        project_name VARCHAR(180) NOT NULL DEFAULT '',
        price_amount DECIMAL(16,2) NULL,
        price_unit VARCHAR(40) NOT NULL DEFAULT '',
        area_m2 DECIMAL(12,2) NULL,
        admin_fee DECIMAL(14,2) NULL,
        vat_applies VARCHAR(20) NOT NULL DEFAULT '',
        bedrooms SMALLINT UNSIGNED NULL,
        bathrooms SMALLINT UNSIGNED NULL,
        parking_spaces SMALLINT UNSIGNED NULL,
        floor_level VARCHAR(40) NOT NULL DEFAULT '',
        contact_name VARCHAR(120) NOT NULL DEFAULT '',
        contact_phone VARCHAR(80) NOT NULL DEFAULT '',
        listing_code VARCHAR(120) NOT NULL DEFAULT '',
        listing_date DATE NULL,
        consulted_at DATE NULL,
        comparability_notes TEXT NULL,
        rejection_reason TEXT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        UNIQUE KEY uq_appraisal_sample (appraisal_id, owner_id, sample_index),
        INDEX idx_appraisal_comparables_owner (owner_id, appraisal_id),
        INDEX idx_appraisal_comparables_source (source_name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
};
