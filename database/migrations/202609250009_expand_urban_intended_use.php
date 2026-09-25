<?php
declare(strict_types=1);
use App\Core\Database;
use App\Database\Schema;

return static function (Schema $schema): void {
    $query = $schema->db->prepare('SELECT DATA_TYPE FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?');
    $query->execute(['appraisal_urban_norm_profiles', 'intended_use']);
    $type = strtolower((string) $query->fetchColumn());
    if ($type !== '' && !in_array($type, ['text', 'mediumtext', 'longtext'], true)) {
        $schema->db->exec('ALTER TABLE ' . Database::identifier('appraisal_urban_norm_profiles') . ' MODIFY COLUMN ' . Database::identifier('intended_use') . ' TEXT NULL');
    }
};
