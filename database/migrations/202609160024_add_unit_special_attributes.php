<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('appraisal_units', 'special_attributes_json',
        'TEXT NULL AFTER construction_report_text');
    $schema->addColumn('appraisal_units', 'special_attributes_report_text',
        'TEXT NULL AFTER special_attributes_json');
};
