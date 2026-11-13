<?php
declare(strict_types=1);
use App\Database\Schema;
return static function (Schema $schema): void {
    $schema->addColumn('appraisals','lease_register','LONGTEXT NULL');
    $schema->addColumn('appraisals','lease_version','INT UNSIGNED NOT NULL DEFAULT 0');
};
