<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('appraisal_photos', 'unit_id', 'CHAR(32) NULL AFTER owner_id');
    foreach ([
        'lot_shape' => "VARCHAR(80) NOT NULL DEFAULT '' AFTER surface_notes",
        'topography' => "VARCHAR(80) NOT NULL DEFAULT '' AFTER lot_shape",
        'boundaries' => 'TEXT NULL AFTER topography',
        'enclosure' => "VARCHAR(80) NOT NULL DEFAULT '' AFTER boundaries",
        'equivalent_depth_m' => 'DECIMAL(12,2) NULL AFTER enclosure',
        'front_depth_ratio' => 'DECIMAL(12,2) NULL AFTER equivalent_depth_m',
        'dynamic_surface_notes' => 'TEXT NULL AFTER front_depth_ratio',
        'surface_report_text' => 'TEXT NULL AFTER dynamic_surface_notes',
    ] as $column => $definition) {
        $schema->addColumn('appraisal_units', $column, $definition);
    }
};
