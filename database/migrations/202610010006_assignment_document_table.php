<?php
declare(strict_types=1);
use App\Database\Schema;
return static function (Schema $schema): void {
    $schema->addColumn('appraisals', 'source_document_details', 'TEXT NULL');
};
