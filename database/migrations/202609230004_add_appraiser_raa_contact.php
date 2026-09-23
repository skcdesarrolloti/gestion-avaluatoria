<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('valuation_appraisers', 'raa_contact_city', "VARCHAR(100) NOT NULL DEFAULT ''");
    $schema->addColumn('valuation_appraisers', 'raa_contact_department', "VARCHAR(100) NOT NULL DEFAULT ''");
    $schema->addColumn('valuation_appraisers', 'raa_contact_address', "VARCHAR(190) NOT NULL DEFAULT ''");
};
