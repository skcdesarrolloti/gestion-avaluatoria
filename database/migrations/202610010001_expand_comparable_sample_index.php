<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $query = $schema->db->query("SELECT DATA_TYPE FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = 'appraisal_comparables'
        AND column_name = 'sample_index'");
    if (in_array($query->fetchColumn(), ['tinyint', 'smallint', 'mediumint'], true)) {
        $schema->db->exec('ALTER TABLE appraisal_comparables MODIFY sample_index INT UNSIGNED NOT NULL');
    }
};
