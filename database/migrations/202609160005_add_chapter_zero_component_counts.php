<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('appraisals', 'igac_property_units_count', 'SMALLINT UNSIGNED NOT NULL DEFAULT 0');
    $schema->addColumn('appraisals', 'igac_annex_units_count', 'SMALLINT UNSIGNED NOT NULL DEFAULT 0');
};
