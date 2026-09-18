<?php
declare(strict_types=1);
use App\Database\Schema;
use App\Models\IfrsStandardRepository;
use App\Models\InternationalStandardRepository;
use App\Models\ValuationStandardRepository;

return static function (Schema $schema): void {
    $schema->addColumn('valuation_standards', 'pdf_blob', 'LONGBLOB NULL');
    $schema->addColumn('valuation_international_standards', 'pdf_blob', 'LONGBLOB NULL');
    $schema->addColumn('valuation_ifrs_standards', 'pdf_blob', 'LONGBLOB NULL');

    $sets = [
        ['valuation_standards', ValuationStandardRepository::class],
        ['valuation_international_standards', InternationalStandardRepository::class],
        ['valuation_ifrs_standards', IfrsStandardRepository::class],
    ];
    foreach ($sets as [$table, $repository]) {
        $rows = $schema->db->query("SELECT slug, storage_filename FROM $table
            WHERE pdf_blob IS NULL AND storage_filename <> ''")->fetchAll();
        $update = $schema->db->prepare("UPDATE $table SET pdf_blob = ? WHERE slug = ?");
        foreach ($rows as $row) {
            $path = $repository::storagePath((string) $row['storage_filename']);
            if (is_file($path)) {
                $blob = file_get_contents($path);
                if (is_string($blob)) $update->execute([$blob, $row['slug']]);
            }
        }
    }
};
