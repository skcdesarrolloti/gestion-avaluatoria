<?php
declare(strict_types=1);
use App\Database\Schema;
return static function (Schema $schema): void {
    $schema->db->exec("CREATE TABLE IF NOT EXISTS analyst_accounts (
        id CHAR(32) PRIMARY KEY, owner_id BIGINT UNSIGNED NOT NULL,
        appraiser_id CHAR(32) NOT NULL, full_name VARCHAR(160) NOT NULL,
        username VARCHAR(80) NOT NULL, password_hash VARCHAR(255) NOT NULL,
        active TINYINT NOT NULL DEFAULT 1, must_change TINYINT NOT NULL DEFAULT 1,
        auth_version INT UNSIGNED NOT NULL DEFAULT 1, created_at DATETIME NOT NULL,
        UNIQUE KEY uq_analyst_username (username), INDEX idx_analyst_owner (owner_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $schema->addColumn('appraisals', 'analyst_account_id', 'CHAR(32) NULL');
};
