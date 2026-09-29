<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    foreach ([
        'stratum' => "VARCHAR(20) NOT NULL DEFAULT ''",
        'age_years' => 'SMALLINT UNSIGNED NULL',
        'building_condition' => "VARCHAR(80) NOT NULL DEFAULT ''",
        'conservation_state' => "VARCHAR(80) NOT NULL DEFAULT ''",
        'view_quality' => "VARCHAR(80) NOT NULL DEFAULT ''",
        'finish_quality' => "VARCHAR(80) NOT NULL DEFAULT ''",
        'elevator' => "VARCHAR(20) NOT NULL DEFAULT ''",
        'amenities' => "VARCHAR(240) NOT NULL DEFAULT ''",
        'security_features' => "VARCHAR(180) NOT NULL DEFAULT ''",
        'power_plant' => "VARCHAR(80) NOT NULL DEFAULT ''",
        'parking_relation' => "VARCHAR(120) NOT NULL DEFAULT ''",
        'balcony_terrace' => "VARCHAR(120) NOT NULL DEFAULT ''",
        'noise_humidity_sun' => "VARCHAR(180) NOT NULL DEFAULT ''",
        'legal_relation_notes' => "VARCHAR(300) NOT NULL DEFAULT ''",
    ] as $column => $definition) {
        $schema->addColumn('appraisal_comparables', $column, $definition);
    }
};
