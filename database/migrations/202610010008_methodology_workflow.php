<?php
declare(strict_types=1);
use App\Database\Schema;
return static function (Schema $schema): void {
    $schema->addColumn('appraisals', 'methodology_workflow', 'LONGTEXT NULL');
    $schema->addColumn('appraisals', 'methodology_version', 'INT UNSIGNED NOT NULL DEFAULT 0');
};
