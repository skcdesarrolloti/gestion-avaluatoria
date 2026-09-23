<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('valuation_appraisers', 'identification_number', 'VARCHAR(40) NULL');
    $schema->addColumn('valuation_appraisers', 'raa_issued_at', 'DATE NULL');
    $schema->addColumn('valuation_appraisers', 'raa_pin', "VARCHAR(40) NOT NULL DEFAULT ''");

    $query = $schema->db->prepare('SELECT COUNT(*) FROM information_schema.statistics
        WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?');
    $query->execute(['valuation_appraisers', 'uq_valuation_appraisers_identification']);
    if ((int) $query->fetchColumn() === 0) {
        $schema->db->exec('ALTER TABLE valuation_appraisers
            ADD UNIQUE KEY uq_valuation_appraisers_identification (identification_number)');
    }
};

