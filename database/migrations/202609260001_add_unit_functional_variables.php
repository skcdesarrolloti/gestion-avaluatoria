<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    foreach ([
        'functional_bedrooms_count' => 'SMALLINT UNSIGNED NULL AFTER construction_rentable_units',
        'functional_bathrooms_count' => 'DECIMAL(4,1) NULL AFTER functional_bedrooms_count',
        'functional_service_room_bathroom' => "VARCHAR(40) NOT NULL DEFAULT '' AFTER functional_bathrooms_count",
        'functional_parking_spaces_count' => 'SMALLINT UNSIGNED NULL AFTER functional_service_room_bathroom',
        'functional_loading_bays_count' => 'SMALLINT UNSIGNED NULL AFTER functional_parking_spaces_count',
        'functional_clear_height_m' => 'DECIMAL(8,2) NULL AFTER functional_loading_bays_count',
        'functional_office_area_m2' => 'DECIMAL(12,2) NULL AFTER functional_clear_height_m',
        'functional_access_type' => "VARCHAR(80) NOT NULL DEFAULT '' AFTER functional_office_area_m2",
        'functional_view' => "VARCHAR(80) NOT NULL DEFAULT '' AFTER functional_access_type",
        'functional_finish_quality' => "VARCHAR(80) NOT NULL DEFAULT '' AFTER functional_view",
        'functional_notes' => 'TEXT NULL AFTER functional_finish_quality',
    ] as $column => $definition) {
        $schema->addColumn('appraisal_units', $column, $definition);
    }
};
