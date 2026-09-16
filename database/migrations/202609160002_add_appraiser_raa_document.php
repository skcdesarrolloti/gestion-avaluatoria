<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('valuation_appraisers', 'raa_expires_at', 'DATE NULL');
    $schema->addColumn('valuation_appraisers', 'raa_source_filename', "VARCHAR(190) NOT NULL DEFAULT ''");
    $schema->addColumn('valuation_appraisers', 'raa_storage_filename', "VARCHAR(190) NOT NULL DEFAULT ''");
    $schema->addColumn('valuation_appraisers', 'raa_file_size_bytes', 'BIGINT UNSIGNED NULL');
    $schema->addColumn('valuation_appraisers', 'raa_uploaded_at', 'DATETIME NULL');
};
