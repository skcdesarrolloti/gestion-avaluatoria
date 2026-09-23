<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('appraisals', 'assignment_description', 'TEXT NULL AFTER report_date');
};
