<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('appraisal_units', 'property_type',
        "VARCHAR(40) NOT NULL DEFAULT '' AFTER label");
};
