<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    foreach ([
        'predial_base_value' => "VARCHAR(80) NOT NULL DEFAULT '' AFTER stratum",
        'predial_destination_code' => "VARCHAR(20) NOT NULL DEFAULT '' AFTER predial_base_value",
        'predial_destination_description' => "VARCHAR(160) NOT NULL DEFAULT '' AFTER predial_destination_code",
        'predial_rate_per_mille' => "VARCHAR(40) NOT NULL DEFAULT '' AFTER predial_destination_description",
        'predial_bill_source' => "VARCHAR(220) NOT NULL DEFAULT '' AFTER predial_rate_per_mille",
    ] as $column => $definition) {
        $schema->addColumn('appraisal_subjects', $column, $definition);
    }
};
