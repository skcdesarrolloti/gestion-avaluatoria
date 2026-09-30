<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('appraisal_comparables', 'analysis_factor', "VARCHAR(100) NOT NULL DEFAULT ''");
};
