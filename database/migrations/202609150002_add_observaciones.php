<?php
declare(strict_types=1);
use App\Database\Schema;

// Example of an automatic, retry-safe column addition. Never edit after applying.
return static function (Schema $schema): void {
    $schema->addColumn('appraisals', 'observaciones', 'TEXT NULL');
};
