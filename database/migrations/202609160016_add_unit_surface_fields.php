<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    foreach ([
        'area_land_m2' => 'DECIMAL(12,2) NULL AFTER notes',
        'area_built_m2' => 'DECIMAL(12,2) NULL AFTER area_land_m2',
        'area_private_m2' => 'DECIMAL(12,2) NULL AFTER area_built_m2',
        'area_common_m2' => 'DECIMAL(12,2) NULL AFTER area_private_m2',
        'front_length_m' => 'DECIMAL(12,2) NULL AFTER area_common_m2',
        'depth_length_m' => 'DECIMAL(12,2) NULL AFTER front_length_m',
        'surface_source' => "VARCHAR(120) NOT NULL DEFAULT '' AFTER depth_length_m",
        'surface_notes' => 'TEXT NULL AFTER surface_source',
    ] as $column => $definition) {
        $schema->addColumn('appraisal_units', $column, $definition);
    }
};
