<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('appraisal_urban_norm_profiles', 'lot_depth_normative_m', 'DECIMAL(10,2) NULL AFTER lot_front_normative_m');
    $schema->addColumn('appraisal_urban_norm_profiles', 'setback_front_m', 'DECIMAL(10,2) NULL AFTER setback_area_percent');
    $schema->addColumn('appraisal_urban_norm_profiles', 'setback_rear_m', 'DECIMAL(10,2) NULL AFTER setback_front_m');
    $schema->addColumn('appraisal_urban_norm_profiles', 'setback_left_m', 'DECIMAL(10,2) NULL AFTER setback_rear_m');
    $schema->addColumn('appraisal_urban_norm_profiles', 'setback_right_m', 'DECIMAL(10,2) NULL AFTER setback_left_m');
};
