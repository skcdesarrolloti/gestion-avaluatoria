<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $columns = "sector_name VARCHAR(160) NOT NULL DEFAULT '',
        influence_area TEXT NULL, sector_boundaries TEXT NULL, sector_source VARCHAR(220) NOT NULL DEFAULT '',
        services_status VARCHAR(40) NOT NULL DEFAULT '', infrastructure_notes TEXT NULL,
        road_hierarchy VARCHAR(60) NOT NULL DEFAULT '', public_space_state VARCHAR(60) NOT NULL DEFAULT '',
        predominant_use VARCHAR(60) NOT NULL DEFAULT '', urban_norm TEXT NULL,
        urban_treatment VARCHAR(100) NOT NULL DEFAULT '', development_level VARCHAR(60) NOT NULL DEFAULT '',
        access_roads TEXT NULL, public_transport VARCHAR(60) NOT NULL DEFAULT '',
        connectivity VARCHAR(60) NOT NULL DEFAULT '', mobility_notes TEXT NULL,
        nearby_facilities TEXT NULL, commercial_activity VARCHAR(60) NOT NULL DEFAULT '',
        activity_anchors TEXT NULL, daily_dynamics TEXT NULL,
        consolidation_level VARCHAR(60) NOT NULL DEFAULT '', socioeconomic_profile VARCHAR(80) NOT NULL DEFAULT '',
        security_perception VARCHAR(60) NOT NULL DEFAULT '', environmental_quality VARCHAR(60) NOT NULL DEFAULT '',
        positive_externalities TEXT NULL, negative_externalities TEXT NULL,
        sector_risks TEXT NULL, mitigation_notes TEXT NULL, field_sources TEXT NULL,
        support_notes TEXT NULL, sector_conclusion TEXT NULL, sector_report_text TEXT NULL";
    $schema->db->exec("CREATE TABLE IF NOT EXISTS master_sector_profiles (
        neighborhood_id CHAR(32) PRIMARY KEY,
        source_appraisal_id CHAR(32) NULL,
        updated_by_owner_id BIGINT UNSIGNED NOT NULL,
        version INT UNSIGNED NOT NULL DEFAULT 1,
        $columns,
        updated_at DATETIME NOT NULL,
        INDEX idx_master_sector_profiles_updated (updated_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
};
