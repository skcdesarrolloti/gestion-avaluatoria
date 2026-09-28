<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->db->exec("CREATE TABLE IF NOT EXISTS midas_documents (
        id CHAR(32) PRIMARY KEY,
        slug VARCHAR(120) NOT NULL UNIQUE,
        layer_group VARCHAR(120) NOT NULL,
        document_code VARCHAR(120) NOT NULL,
        title VARCHAR(240) NOT NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'vigente',
        practical_use VARCHAR(700) NOT NULL DEFAULT '',
        applies_to VARCHAR(240) NOT NULL DEFAULT '',
        source_filename VARCHAR(240) NOT NULL,
        storage_filename VARCHAR(180) NOT NULL,
        mime_type VARCHAR(80) NOT NULL,
        file_size_bytes INT UNSIGNED NULL,
        file_blob LONGBLOB NULL,
        created_by VARCHAR(120) NOT NULL DEFAULT '',
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        UNIQUE KEY uq_midas_docs_code (document_code),
        UNIQUE KEY uq_midas_docs_file (source_filename),
        INDEX idx_midas_docs_group (layer_group, status),
        INDEX idx_midas_docs_title (title)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
};
