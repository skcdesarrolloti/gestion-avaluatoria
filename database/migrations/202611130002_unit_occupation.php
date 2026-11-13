<?php
declare(strict_types=1);
use App\Database\Schema;
return static function (Schema $schema): void {
    $schema->addColumn('appraisal_units','occupation_details','TEXT NULL');
    $schema->addColumn('appraisal_units','occupation_version','INT UNSIGNED NOT NULL DEFAULT 0');
};
