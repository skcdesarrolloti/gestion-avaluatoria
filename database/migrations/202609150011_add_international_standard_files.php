<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('valuation_international_standards', 'source_filename', "VARCHAR(220) NOT NULL DEFAULT ''");
    $schema->addColumn('valuation_international_standards', 'storage_filename', "VARCHAR(220) NOT NULL DEFAULT ''");
    $schema->addColumn('valuation_international_standards', 'file_size_bytes', 'BIGINT UNSIGNED NULL');
    $schema->addColumn('valuation_international_standards', 'imported_at', 'DATETIME NULL');
};
