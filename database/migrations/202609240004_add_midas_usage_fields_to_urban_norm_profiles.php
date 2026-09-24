<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('appraisal_urban_norm_profiles', 'cadastral_reference', "VARCHAR(80) NOT NULL DEFAULT '' AFTER owner_id");
    $schema->addColumn('appraisal_urban_norm_profiles', 'midas_query_option', "VARCHAR(80) NOT NULL DEFAULT 'Uso del suelo' AFTER midas_consulted");
    $schema->addColumn('appraisal_urban_norm_profiles', 'midas_usage_result', "TEXT NULL AFTER midas_layers");
    $schema->addColumn('appraisal_urban_norm_profiles', 'midas_activity', "VARCHAR(180) NOT NULL DEFAULT '' AFTER midas_usage_result");
    $schema->addColumn('appraisal_urban_norm_profiles', 'midas_support_reference', "VARCHAR(220) NOT NULL DEFAULT '' AFTER midas_activity");
    $schema->addColumn('appraisal_urban_norm_profiles', 'official_concept_scope', "TEXT NULL AFTER planning_concept_date");
    $schema->addColumn('appraisal_urban_norm_profiles', 'urban_norms_applied', "TEXT NULL AFTER applicable_activity");
    $schema->addColumn('appraisal_urban_norm_profiles', 'heritage_context', "TEXT NULL AFTER urban_norms_applied");
    $schema->addColumn('appraisal_urban_norm_profiles', 'environmental_context', "TEXT NULL AFTER heritage_context");
    $schema->addColumn('appraisal_urban_norm_profiles', 'risk_context', "TEXT NULL AFTER environmental_context");
    $schema->addColumn('appraisal_urban_norm_profiles', 'source_limitations', "TEXT NULL AFTER analyst_notes");
};
