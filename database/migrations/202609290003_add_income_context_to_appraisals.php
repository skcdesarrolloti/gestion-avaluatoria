<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    foreach ([
        'income_producing' => "VARCHAR(20) NOT NULL DEFAULT ''",
        'rent_amount' => 'DECIMAL(14,2) NULL',
        'rent_period' => "VARCHAR(40) NOT NULL DEFAULT ''",
        'ph_admin_fee_amount' => 'DECIMAL(14,2) NULL',
        'rent_charges_vat' => "VARCHAR(20) NOT NULL DEFAULT ''",
        'income_notes' => 'TEXT NULL',
    ] as $column => $definition) {
        $schema->addColumn('appraisals', $column, $definition);
    }
};
