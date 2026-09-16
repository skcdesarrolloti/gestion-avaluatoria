<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    foreach ([
        'appraiser_id' => 'CHAR(32) NULL',
        'igac_category' => "VARCHAR(40) NOT NULL DEFAULT ''",
        'igac_typology_hint' => "VARCHAR(190) NOT NULL DEFAULT ''",
        'inspection_notes' => 'TEXT NULL',
        'configuration_status' => "VARCHAR(30) NOT NULL DEFAULT 'borrador'",
    ] as $column => $definition) {
        $schema->addColumn('appraisals', $column, $definition);
    }
    $schema->db->exec("CREATE TABLE IF NOT EXISTS appraisal_photos (
        id CHAR(32) PRIMARY KEY,
        appraisal_id CHAR(32) NOT NULL,
        owner_id BIGINT UNSIGNED NOT NULL,
        source_filename VARCHAR(190) NOT NULL,
        storage_filename VARCHAR(190) NOT NULL,
        mime_type VARCHAR(80) NOT NULL,
        file_size_bytes BIGINT UNSIGNED NOT NULL,
        caption VARCHAR(190) NOT NULL DEFAULT '',
        created_at DATETIME NOT NULL,
        INDEX idx_appraisal_photos_appraisal (appraisal_id, created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
};
