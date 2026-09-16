<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('appraisal_photos', 'display_name', "VARCHAR(190) NOT NULL DEFAULT '' AFTER caption");
};
