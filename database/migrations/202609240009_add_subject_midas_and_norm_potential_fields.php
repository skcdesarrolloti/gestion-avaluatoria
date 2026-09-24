<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    foreach ([
        'midas_national_cadastral_reference' => "VARCHAR(120) NOT NULL DEFAULT '' AFTER cadastral_reference",
        'midas_property_registry' => "VARCHAR(80) NOT NULL DEFAULT '' AFTER midas_national_cadastral_reference",
        'midas_address' => "VARCHAR(220) NOT NULL DEFAULT '' AFTER midas_property_registry",
        'midas_cadastral_reference' => "VARCHAR(120) NOT NULL DEFAULT '' AFTER midas_address",
        'midas_territory' => "VARCHAR(160) NOT NULL DEFAULT '' AFTER midas_cadastral_reference",
        'midas_locality' => "VARCHAR(160) NOT NULL DEFAULT '' AFTER midas_territory",
        'midas_commune_ucg' => "VARCHAR(80) NOT NULL DEFAULT '' AFTER midas_locality",
        'midas_land_use' => "VARCHAR(120) NOT NULL DEFAULT '' AFTER midas_territory",
        'midas_urban_treatment' => "VARCHAR(160) NOT NULL DEFAULT '' AFTER midas_land_use",
        'midas_risk' => "VARCHAR(220) NOT NULL DEFAULT '' AFTER midas_urban_treatment",
        'midas_land_classification' => "VARCHAR(120) NOT NULL DEFAULT '' AFTER midas_risk",
        'midas_dane_block_code' => "VARCHAR(80) NOT NULL DEFAULT '' AFTER midas_land_classification",
        'midas_dane_block_side' => "VARCHAR(40) NOT NULL DEFAULT '' AFTER midas_dane_block_code",
        'midas_block_number' => "VARCHAR(80) NOT NULL DEFAULT '' AFTER midas_dane_block_side",
        'midas_property_number' => "VARCHAR(80) NOT NULL DEFAULT '' AFTER midas_block_number",
        'midas_stratum' => "VARCHAR(20) NOT NULL DEFAULT '' AFTER midas_property_number",
        'midas_stratum_record' => "VARCHAR(160) NOT NULL DEFAULT '' AFTER midas_stratum",
        'midas_stratum_atypical' => "VARCHAR(80) NOT NULL DEFAULT '' AFTER midas_stratum_record",
        'midas_stratum_observation' => "VARCHAR(220) NOT NULL DEFAULT '' AFTER midas_stratum_atypical",
        'midas_building_name' => "VARCHAR(180) NOT NULL DEFAULT '' AFTER midas_stratum_observation",
        'midas_land_area_m2' => "VARCHAR(40) NOT NULL DEFAULT '' AFTER midas_building_name",
        'midas_built_area_m2' => "VARCHAR(40) NOT NULL DEFAULT '' AFTER midas_land_area_m2",
        'midas_updated_on' => 'DATE NULL AFTER midas_building_name',
        'midas_predio_raw' => 'TEXT NULL AFTER midas_updated_on',
    ] as $column => $definition) $schema->addColumn('appraisal_subjects', $column, $definition);
    foreach ([
        'norm_unit_basic_text' => 'TEXT NULL AFTER use_prohibited_text',
        'norm_free_area_text' => 'TEXT NULL AFTER norm_unit_basic_text',
        'norm_min_lot_front_text' => 'TEXT NULL AFTER norm_free_area_text',
        'norm_max_height_text' => 'TEXT NULL AFTER norm_min_lot_front_text',
        'norm_construction_index_text' => 'TEXT NULL AFTER norm_max_height_text',
        'norm_isolation_text' => 'TEXT NULL AFTER norm_construction_index_text',
        'norm_other_potential_text' => 'TEXT NULL AFTER norm_isolation_text',
    ] as $column => $definition) $schema->addColumn('appraisal_urban_norm_profiles', $column, $definition);
};
