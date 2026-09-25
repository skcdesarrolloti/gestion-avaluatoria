<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('appraisal_urban_norm_profiles', 'normative_scenarios_json', 'MEDIUMTEXT NULL AFTER norm_other_potential_text');
    $schema->addColumn('appraisal_urban_norm_profiles', 'adopted_normative_route', "VARCHAR(80) NOT NULL DEFAULT '' AFTER normative_scenarios_json");
    $schema->addColumn('appraisal_urban_norm_profiles', 'adopted_normative_route_label', "VARCHAR(160) NOT NULL DEFAULT '' AFTER adopted_normative_route");
    $schema->addColumn('appraisal_urban_norm_profiles', 'highest_best_use_reason', 'TEXT NULL AFTER adopted_normative_route_label');
};
