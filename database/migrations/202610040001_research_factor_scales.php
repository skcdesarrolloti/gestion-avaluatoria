<?php
declare(strict_types=1);
return static function (\App\Database\Schema $schema): void {
    $schema->db->exec("CREATE TABLE IF NOT EXISTS research_factor_scales (
        owner_id BIGINT UNSIGNED NOT NULL,
        factor_key VARCHAR(30) NOT NULL,
        definition_json TEXT NOT NULL,
        version INT UNSIGNED NOT NULL DEFAULT 1,
        updated_at DATETIME NOT NULL,
        PRIMARY KEY(owner_id, factor_key)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
};
