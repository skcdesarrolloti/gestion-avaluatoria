<?php
declare(strict_types=1);
use App\Database\Schema;
use App\Models\LegalDocumentRepository;

return static function (Schema $schema): void {
    $schema->addColumn('valuation_legal_documents', 'pdf_blob', 'LONGBLOB NULL');
    $rows = $schema->db->query("SELECT slug, storage_filename FROM valuation_legal_documents
        WHERE pdf_blob IS NULL AND storage_filename <> ''")->fetchAll();
    $update = $schema->db->prepare('UPDATE valuation_legal_documents SET pdf_blob = ? WHERE slug = ?');
    foreach ($rows as $row) {
        $path = LegalDocumentRepository::storagePath((string) $row['storage_filename']);
        if (is_file($path)) {
            $blob = file_get_contents($path);
            if (is_string($blob)) $update->execute([$blob, $row['slug']]);
        }
    }
};
