<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->db->exec("CREATE TABLE IF NOT EXISTS master_sector_sources (
        source_key VARCHAR(80) PRIMARY KEY,
        group_name VARCHAR(120) NOT NULL,
        source_name VARCHAR(180) NOT NULL,
        responsible_entity VARCHAR(180) NOT NULL DEFAULT '',
        access_url TEXT NULL,
        automatable VARCHAR(20) NOT NULL DEFAULT 'NO',
        latest_revision DATE NULL,
        status VARCHAR(40) NOT NULL DEFAULT 'Pendiente',
        updated_at DATETIME NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $schema->db->exec("CREATE TABLE IF NOT EXISTS master_sector_profile_sections (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        neighborhood_id CHAR(32) NOT NULL,
        section_code VARCHAR(10) NOT NULL,
        section_title VARCHAR(220) NOT NULL,
        status VARCHAR(60) NOT NULL DEFAULT 'Pendiente',
        version INT UNSIGNED NOT NULL DEFAULT 1,
        source_name VARCHAR(220) NOT NULL DEFAULT '',
        source_updated_at DATETIME NULL,
        requires_field_validation VARCHAR(20) NOT NULL DEFAULT 'SI',
        requires_photo_support VARCHAR(20) NOT NULL DEFAULT 'NO',
        content_text TEXT NULL,
        data_json LONGTEXT NULL,
        updated_at DATETIME NOT NULL,
        UNIQUE KEY uq_sector_section (neighborhood_id, section_code),
        INDEX idx_sector_section_status (neighborhood_id, status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $schema->db->exec("CREATE TABLE IF NOT EXISTS master_sector_section_sources (
        neighborhood_id CHAR(32) NOT NULL,
        section_code VARCHAR(10) NOT NULL,
        source_key VARCHAR(80) NOT NULL,
        relation_status VARCHAR(40) NOT NULL DEFAULT 'Pendiente',
        source_data_at DATETIME NULL,
        updated_at DATETIME NOT NULL,
        PRIMARY KEY (neighborhood_id, section_code, source_key),
        INDEX idx_sector_source_key (source_key)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $schema->db->exec("CREATE TABLE IF NOT EXISTS appraisal_sector_snapshots (
        appraisal_id CHAR(32) PRIMARY KEY,
        owner_id BIGINT UNSIGNED NOT NULL,
        neighborhood_id CHAR(32) NULL,
        profile_version INT UNSIGNED NOT NULL DEFAULT 1,
        snapshot_json LONGTEXT NOT NULL,
        copied_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        INDEX idx_sector_snapshot_neighborhood (neighborhood_id, copied_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
};
