<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    foreach ([
        'midas_predio_raw' => 'TEXT NULL AFTER midas_result',
        'midas_usage_raw' => 'TEXT NULL AFTER midas_predio_raw',
        'use_regulation_table' => "VARCHAR(120) NOT NULL DEFAULT '' AFTER midas_usage_raw",
        'use_principal_text' => 'TEXT NULL AFTER use_regulation_table',
        'use_compatible_text' => 'TEXT NULL AFTER use_principal_text',
        'use_complementary_text' => 'TEXT NULL AFTER use_compatible_text',
        'use_restricted_text' => 'TEXT NULL AFTER use_complementary_text',
        'use_prohibited_text' => 'TEXT NULL AFTER use_restricted_text',
    ] as $column => $definition) $schema->addColumn('appraisal_urban_norm_profiles', $column, $definition);
};
