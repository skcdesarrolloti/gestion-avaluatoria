<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    foreach ([
        'subject_title' => "VARCHAR(160) NOT NULL DEFAULT '' AFTER zone_sector",
        'address_certificate' => "VARCHAR(220) NOT NULL DEFAULT '' AFTER address",
        'address_midas' => "VARCHAR(220) NOT NULL DEFAULT '' AFTER address_certificate",
        'address_tax' => "VARCHAR(220) NOT NULL DEFAULT '' AFTER address_midas",
        'address_deed' => "VARCHAR(220) NOT NULL DEFAULT '' AFTER address_tax",
        'address_other' => "VARCHAR(220) NOT NULL DEFAULT '' AFTER address_deed",
        'adopted_source' => "VARCHAR(40) NOT NULL DEFAULT '' AFTER address_other",
        'adopted_address' => "VARCHAR(220) NOT NULL DEFAULT '' AFTER adopted_source",
    ] as $column => $definition) {
        $schema->addColumn('appraisal_subjects', $column, $definition);
    }
};
