<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('appraisal_urban_norm_profiles', 'land_area_normative_m2', 'DECIMAL(12,2) NULL AFTER norm_other_potential_text');
    $schema->addColumn('appraisal_urban_norm_profiles', 'setback_area_percent', 'DECIMAL(8,4) NULL AFTER land_area_normative_m2');
    $schema->addColumn('appraisal_urban_norm_profiles', 'net_land_area_m2', 'DECIMAL(12,2) NULL AFTER setback_area_percent');
    $schema->addColumn('appraisal_urban_norm_profiles', 'occupancy_index', 'DECIMAL(8,4) NULL AFTER net_land_area_m2');
    $schema->addColumn('appraisal_urban_norm_profiles', 'max_floors', 'DECIMAL(8,2) NULL AFTER occupancy_index');
    $schema->addColumn('appraisal_urban_norm_profiles', 'construction_index', 'DECIMAL(8,4) NULL AFTER max_floors');
    $schema->addColumn('appraisal_urban_norm_profiles', 'sellable_area_factor', 'DECIMAL(8,4) NULL AFTER buildable_difference_m2');
    $schema->addColumn('appraisal_urban_norm_profiles', 'sellable_area_m2', 'DECIMAL(12,2) NULL AFTER sellable_area_factor');
};
