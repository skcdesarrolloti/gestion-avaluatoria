<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    foreach ([
        'construction_type' => "VARCHAR(80) NOT NULL DEFAULT '' AFTER surface_report_text",
        'construction_measure_unit' => "VARCHAR(20) NOT NULL DEFAULT 'm2' AFTER construction_type",
        'construction_quantity' => 'DECIMAL(12,2) NULL AFTER construction_measure_unit',
        'construction_floors' => 'SMALLINT UNSIGNED NULL AFTER construction_quantity',
        'construction_basements' => 'SMALLINT UNSIGNED NULL AFTER construction_floors',
        'built_area_manual_m2' => 'DECIMAL(12,2) NULL AFTER construction_basements',
        'built_area_midas_m2' => 'DECIMAL(12,2) NULL AFTER built_area_manual_m2',
        'built_area_tax_m2' => 'DECIMAL(12,2) NULL AFTER built_area_midas_m2',
        'built_area_deed_m2' => 'DECIMAL(12,2) NULL AFTER built_area_tax_m2',
        'built_area_certificate_m2' => 'DECIMAL(12,2) NULL AFTER built_area_deed_m2',
        'built_area_other_m2' => 'DECIMAL(12,2) NULL AFTER built_area_certificate_m2',
        'built_area_adopted_m2' => 'DECIMAL(12,2) NULL AFTER built_area_other_m2',
        'built_area_adopted_source' => "VARCHAR(40) NOT NULL DEFAULT '' AFTER built_area_adopted_m2",
        'construction_year' => 'SMALLINT UNSIGNED NULL AFTER built_area_adopted_source',
        'construction_age_years' => 'SMALLINT UNSIGNED NULL AFTER construction_year',
        'construction_state' => "VARCHAR(60) NOT NULL DEFAULT '' AFTER construction_age_years",
        'construction_progress_percent' => 'DECIMAL(5,2) NULL AFTER construction_state',
        'construction_integrity_percent' => 'DECIMAL(5,2) NULL AFTER construction_progress_percent',
        'construction_conservation_json' => 'TEXT NULL AFTER construction_integrity_percent',
        'construction_general_aspects' => 'TEXT NULL AFTER construction_conservation_json',
        'construction_services_json' => 'TEXT NULL AFTER construction_general_aspects',
        'construction_specifics_json' => 'TEXT NULL AFTER construction_services_json',
        'construction_report_text' => 'TEXT NULL AFTER construction_specifics_json',
    ] as $column => $definition) {
        $schema->addColumn('appraisal_units', $column, $definition);
    }
};
