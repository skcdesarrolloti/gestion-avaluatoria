<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('appraisals', 'requester_identification', 'VARCHAR(80) NOT NULL DEFAULT \'\' AFTER requester_name');
    $schema->addColumn('appraisals', 'source_documents', 'TEXT NULL AFTER assignment_report_text');
};
