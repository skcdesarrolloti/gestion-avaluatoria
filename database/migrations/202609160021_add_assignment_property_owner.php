<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('appraisals', 'property_owner_name',
        "VARCHAR(160) NOT NULL DEFAULT '' AFTER requester_name");
};
