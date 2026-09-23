<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->db->exec("CREATE TABLE IF NOT EXISTS appraisal_report_notes (
        id CHAR(32) PRIMARY KEY,
        appraisal_id CHAR(32) NOT NULL,
        owner_id BIGINT UNSIGNED NOT NULL,
        chapter_code VARCHAR(12) NOT NULL,
        section_code VARCHAR(20) NOT NULL,
        title VARCHAR(180) NOT NULL DEFAULT '',
        body TEXT NOT NULL,
        source_note VARCHAR(600) NOT NULL DEFAULT '',
        sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
        include_in_report TINYINT(1) NOT NULL DEFAULT 1,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        INDEX idx_report_notes_appraisal (appraisal_id, owner_id, chapter_code, section_code, sort_order),
        INDEX idx_report_notes_owner (owner_id, updated_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
};
