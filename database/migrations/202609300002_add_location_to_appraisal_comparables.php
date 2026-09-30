<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('appraisal_comparables', 'latitude', 'DECIMAL(10,7) NULL');
    $schema->addColumn('appraisal_comparables', 'longitude', 'DECIMAL(10,7) NULL');
    $schema->addColumn('appraisal_comparables', 'location_precision', "VARCHAR(40) NOT NULL DEFAULT ''");
    $schema->addColumn('appraisal_comparables', 'map_notes', "VARCHAR(300) NOT NULL DEFAULT ''");
};
