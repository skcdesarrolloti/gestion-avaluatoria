<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('master_neighborhoods', 'commune_ucg', "VARCHAR(80) NOT NULL DEFAULT '' AFTER name");
    $schema->addColumn('master_neighborhoods', 'zone_sector', "VARCHAR(120) NOT NULL DEFAULT '' AFTER commune_ucg");
};
