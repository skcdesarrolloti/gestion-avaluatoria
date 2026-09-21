<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('appraisal_ph_profiles', 'version', 'INT UNSIGNED NOT NULL DEFAULT 0');
};
