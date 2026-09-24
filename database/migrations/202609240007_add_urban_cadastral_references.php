<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('appraisal_urban_norm_profiles', 'cadastral_reference_short', "VARCHAR(80) NOT NULL DEFAULT '' AFTER cadastral_reference");
    $schema->addColumn('appraisal_urban_norm_profiles', 'cadastral_reference_long', "VARCHAR(120) NOT NULL DEFAULT '' AFTER cadastral_reference_short");
};
