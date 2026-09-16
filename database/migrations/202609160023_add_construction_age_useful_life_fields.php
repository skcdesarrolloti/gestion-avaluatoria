<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    foreach ([
        'construction_apparent_age_years' => 'SMALLINT UNSIGNED NULL AFTER construction_age_years',
        'construction_useful_life_years' => 'SMALLINT UNSIGNED NULL AFTER construction_apparent_age_years',
        'construction_remaining_life_years' => 'SMALLINT UNSIGNED NULL AFTER construction_useful_life_years',
        'construction_rentable_units' => 'SMALLINT UNSIGNED NULL AFTER construction_remaining_life_years',
    ] as $column => $definition) {
        $schema->addColumn('appraisal_units', $column, $definition);
    }
};
