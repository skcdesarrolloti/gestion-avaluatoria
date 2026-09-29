<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->db->exec("CREATE TABLE IF NOT EXISTS appraisal_narrative_chapters (
        appraisal_id CHAR(32) NOT NULL,
        owner_id BIGINT UNSIGNED NOT NULL,
        chapter_code VARCHAR(10) NOT NULL,
        data_json MEDIUMTEXT NULL,
        updated_at DATETIME NOT NULL,
        PRIMARY KEY (appraisal_id, owner_id, chapter_code),
        INDEX idx_appraisal_narrative_owner (owner_id, chapter_code, updated_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
};
