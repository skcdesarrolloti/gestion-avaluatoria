<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->db->exec("CREATE TABLE IF NOT EXISTS appraisal_report_note_sections (
        id CHAR(32) PRIMARY KEY,
        appraisal_id CHAR(32) NOT NULL,
        owner_id BIGINT UNSIGNED NOT NULL,
        chapter_code VARCHAR(12) NOT NULL,
        section_code VARCHAR(20) NOT NULL,
        label VARCHAR(180) NOT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        UNIQUE KEY uq_report_note_section (appraisal_id, owner_id, chapter_code, section_code),
        INDEX idx_report_note_sections_appraisal (appraisal_id, owner_id, chapter_code, section_code)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
};
