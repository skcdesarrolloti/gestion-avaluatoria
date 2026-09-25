<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('appraisal_urban_norm_profiles', 'norm_physical_base_text', 'TEXT NULL AFTER sellable_area_m2');
    $schema->addColumn('appraisal_urban_norm_profiles', 'constructive_potential_status', 'VARCHAR(80) NULL AFTER norm_physical_base_text');
};
