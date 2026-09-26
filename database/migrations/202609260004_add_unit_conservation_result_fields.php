<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('appraisal_units', 'conservation_result_json',
        'TEXT NULL AFTER construction_conservation_json');
    $schema->addColumn('appraisal_units', 'conservation_generated_text',
        'TEXT NULL AFTER conservation_result_json');
    $schema->addColumn('appraisal_units', 'conservation_approved_text',
        'TEXT NULL AFTER conservation_generated_text');
};
