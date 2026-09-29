<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('appraisal_units', 'valuation_treatment',
        "VARCHAR(40) NOT NULL DEFAULT '' AFTER construction_type");
};
