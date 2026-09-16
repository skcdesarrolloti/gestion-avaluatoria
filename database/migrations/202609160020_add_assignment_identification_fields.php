<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    foreach ([
        'client_name' => "VARCHAR(160) NOT NULL DEFAULT '' AFTER municipio",
        'requester_name' => "VARCHAR(160) NOT NULL DEFAULT '' AFTER client_name",
        'report_recipient' => "VARCHAR(160) NOT NULL DEFAULT '' AFTER requester_name",
        'intended_use' => "VARCHAR(220) NOT NULL DEFAULT '' AFTER finalidad",
        'visit_date' => 'DATE NULL AFTER intended_use',
        'value_date' => 'DATE NULL AFTER visit_date',
        'report_date' => 'DATE NULL AFTER value_date',
        'assignment_scope' => 'TEXT NULL AFTER report_date',
        'assignment_limitations' => 'TEXT NULL AFTER assignment_scope',
        'assignment_hypotheses' => 'TEXT NULL AFTER assignment_limitations',
    ] as $column => $definition) {
        $schema->addColumn('appraisals', $column, $definition);
    }
};
