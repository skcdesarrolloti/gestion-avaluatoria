<?php
declare(strict_types=1);
use App\Database\Schema;
use App\Core\Database;

return static function (Schema $schema): void {
    $schema->addColumn('appraisals', 'expediente_number', "VARCHAR(14) NULL AFTER id");
    $query = $schema->db->prepare('SELECT COUNT(*) FROM information_schema.statistics
        WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?');
    $query->execute(['appraisals', 'uq_appraisals_expediente_number']);
    if ((int) $query->fetchColumn() === 0) {
        $schema->db->exec('ALTER TABLE ' . Database::identifier('appraisals')
            . ' ADD UNIQUE KEY uq_appraisals_expediente_number (expediente_number)');
    }
};
