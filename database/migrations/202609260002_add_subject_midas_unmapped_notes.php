<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('appraisal_subjects', 'midas_unmapped_notes',
        'TEXT NULL AFTER midas_predio_raw');
};
