<?php
declare(strict_types=1);
use App\Database\Schema;
return static function (Schema $schema): void {
    $schema->addColumn('appraisal_units', 'market_evidence_json', 'LONGTEXT NULL');
    $schema->addColumn('appraisal_units', 'market_evidence_version', 'INT UNSIGNED NOT NULL DEFAULT 0');
};
