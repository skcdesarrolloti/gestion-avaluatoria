<?php
declare(strict_types=1);
use App\Database\Schema;
return static function (Schema $schema): void {
    $schema->addColumn('appraisal_units', 'method_structure', 'VARCHAR(40) NULL');
};
