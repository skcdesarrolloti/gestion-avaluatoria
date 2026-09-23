<?php
declare(strict_types=1);
use App\Core\Database;
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('appraisals', 'location_description', 'TEXT NULL AFTER source_documents');
    $schema->addColumn('appraisals', 'location_image_reference', 'TEXT NULL AFTER location_description');
    $schema->addColumn('appraisals', 'source_documents_json', 'TEXT NULL AFTER source_documents');
    $query = $schema->db->prepare('SELECT DATA_TYPE FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?');
    $query->execute(['appraisals', 'intended_use']);
    $type = strtolower((string) $query->fetchColumn());
    if ($type !== '' && !in_array($type, ['text', 'mediumtext', 'longtext'], true)) {
        $schema->db->exec('ALTER TABLE ' . Database::identifier('appraisals') . ' MODIFY COLUMN ' . Database::identifier('intended_use') . ' TEXT NULL');
    }
};