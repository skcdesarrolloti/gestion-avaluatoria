<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    foreach ([
        'urban_license' => "VARCHAR(220) NOT NULL DEFAULT '' AFTER urban_treatment",
        'permitted_use' => 'TEXT NULL AFTER urban_license',
        'legal_urban_affectations' => 'TEXT NULL AFTER restrictions',
    ] as $column => $definition) {
        $schema->addColumn('appraisal_urban_norm_profiles', $column, $definition);
    }
};
