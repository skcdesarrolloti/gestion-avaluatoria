<?php
declare(strict_types=1);
return static function (\App\Database\Schema $schema): void {
    $schema->addColumn('appraisal_units','subject_factors_json','MEDIUMTEXT NULL');
    $schema->addColumn('appraisal_units','subject_factors_version','INT UNSIGNED NOT NULL DEFAULT 0');
};
