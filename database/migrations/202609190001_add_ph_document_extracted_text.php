<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('appraisal_ph_documents', 'extracted_text',
        'MEDIUMTEXT NULL AFTER extracted_chars');
};
