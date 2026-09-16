<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    foreach ([
        'boundary_source' => "VARCHAR(220) NOT NULL DEFAULT '' AFTER boundaries",
        'boundary_front' => 'TEXT NULL AFTER boundary_source',
        'boundary_right' => 'TEXT NULL AFTER boundary_front',
        'boundary_left' => 'TEXT NULL AFTER boundary_right',
        'boundary_back' => 'TEXT NULL AFTER boundary_left',
        'boundary_zenith' => 'TEXT NULL AFTER boundary_back',
        'boundary_nadir' => 'TEXT NULL AFTER boundary_zenith',
        'area_manual_m2' => 'DECIMAL(12,2) NULL AFTER boundary_nadir',
        'area_midas_m2' => 'DECIMAL(12,2) NULL AFTER area_manual_m2',
        'area_tax_m2' => 'DECIMAL(12,2) NULL AFTER area_midas_m2',
        'area_deed_m2' => 'DECIMAL(12,2) NULL AFTER area_tax_m2',
        'area_certificate_m2' => 'DECIMAL(12,2) NULL AFTER area_deed_m2',
        'area_other_m2' => 'DECIMAL(12,2) NULL AFTER area_certificate_m2',
        'area_adopted_m2' => 'DECIMAL(12,2) NULL AFTER area_other_m2',
        'area_adopted_source' => "VARCHAR(40) NOT NULL DEFAULT '' AFTER area_adopted_m2",
        'dynamic_normative_compatibility' => "VARCHAR(60) NOT NULL DEFAULT '' AFTER dynamic_surface_notes",
        'dynamic_environment_conditions' => "VARCHAR(60) NOT NULL DEFAULT '' AFTER dynamic_normative_compatibility",
        'dynamic_service_quality' => "VARCHAR(60) NOT NULL DEFAULT '' AFTER dynamic_environment_conditions",
        'dynamic_service_availability' => "VARCHAR(60) NOT NULL DEFAULT '' AFTER dynamic_service_quality",
        'dynamic_road_condition' => "VARCHAR(60) NOT NULL DEFAULT '' AFTER dynamic_service_availability",
        'dynamic_urban_development' => "VARCHAR(60) NOT NULL DEFAULT '' AFTER dynamic_road_condition",
        'dynamic_affectations' => "VARCHAR(60) NOT NULL DEFAULT '' AFTER dynamic_urban_development",
        'dynamic_restrictions' => "VARCHAR(60) NOT NULL DEFAULT '' AFTER dynamic_affectations",
    ] as $column => $definition) {
        $schema->addColumn('appraisal_units', $column, $definition);
    }
};
