<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $columns = [
        'sector_country' => "VARCHAR(80) NOT NULL DEFAULT ''",
        'sector_department' => "VARCHAR(120) NOT NULL DEFAULT ''",
        'sector_city' => "VARCHAR(120) NOT NULL DEFAULT ''",
        'sector_neighborhood' => "VARCHAR(160) NOT NULL DEFAULT ''",
        'sector_locality' => "VARCHAR(160) NOT NULL DEFAULT ''",
        'sector_commune' => "VARCHAR(80) NOT NULL DEFAULT ''",
        'sector_microsector' => "VARCHAR(160) NOT NULL DEFAULT ''",
        'sector_map_url' => 'TEXT NULL',
        'sector_latitude' => "VARCHAR(40) NOT NULL DEFAULT ''",
        'sector_longitude' => "VARCHAR(40) NOT NULL DEFAULT ''",
        'sector_area_ha' => "VARCHAR(40) NOT NULL DEFAULT ''",
        'sector_perimeter_m' => "VARCHAR(40) NOT NULL DEFAULT ''",
        'sector_north_boundary' => "VARCHAR(220) NOT NULL DEFAULT ''",
        'sector_east_boundary' => "VARCHAR(220) NOT NULL DEFAULT ''",
        'sector_south_boundary' => "VARCHAR(220) NOT NULL DEFAULT ''",
        'sector_west_boundary' => "VARCHAR(220) NOT NULL DEFAULT ''",
    ];
    foreach (['appraisal_sector_profiles', 'master_sector_profiles'] as $table) {
        foreach ($columns as $column => $definition) {
            $schema->addColumn($table, $column, $definition);
        }
    }
};
