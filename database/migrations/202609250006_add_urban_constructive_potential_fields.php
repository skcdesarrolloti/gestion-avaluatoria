<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('appraisal_urban_norm_profiles', 'actual_built_area_m2', 'DECIMAL(12,2) NULL AFTER norm_other_potential_text');
    $schema->addColumn('appraisal_urban_norm_profiles', 'normative_max_built_area_m2', 'DECIMAL(12,2) NULL AFTER actual_built_area_m2');
    $schema->addColumn('appraisal_urban_norm_profiles', 'buildable_difference_m2', 'DECIMAL(12,2) NULL AFTER normative_max_built_area_m2');
    $schema->addColumn('appraisal_urban_norm_profiles', 'constructive_potential_notes', 'TEXT NULL AFTER buildable_difference_m2');
};
