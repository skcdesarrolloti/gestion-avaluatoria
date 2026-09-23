<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('appraisals', 'requester_capacity', "VARCHAR(220) NOT NULL DEFAULT '' AFTER requester_identification");
    $schema->addColumn('appraisals', 'request_date', 'DATE NULL AFTER intended_use');
};
