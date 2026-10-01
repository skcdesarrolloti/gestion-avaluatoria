<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    foreach (['requester_email' => 254, 'requester_phone' => 40, 'requester_municipality' => 120] as $field => $length) {
        $schema->addColumn('appraisals', $field, "VARCHAR($length) NOT NULL DEFAULT ''");
    }
    $schema->addColumn('appraisals', 'value_date_notes', 'TEXT NULL');
};
