<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->db->exec("CREATE TABLE IF NOT EXISTS master_documents (
        id CHAR(32) PRIMARY KEY,
        slug VARCHAR(120) NOT NULL UNIQUE,
        destination VARCHAR(40) NOT NULL,
        document_code VARCHAR(120) NOT NULL,
        title VARCHAR(240) NOT NULL,
        document_type VARCHAR(40) NOT NULL,
        version VARCHAR(80) NOT NULL DEFAULT '',
        effective_at DATE NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'vigente',
        source_url VARCHAR(500) NOT NULL DEFAULT '',
        summary VARCHAR(600) NOT NULL DEFAULT '',
        topics_json JSON NULL,
        modules_json JSON NULL,
        source_filename VARCHAR(240) NOT NULL,
        storage_filename VARCHAR(180) NOT NULL,
        file_size_bytes INT UNSIGNED NULL,
        pdf_blob LONGBLOB NULL,
        created_by VARCHAR(120) NOT NULL DEFAULT '',
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        INDEX idx_master_docs_destination (destination, status),
        INDEX idx_master_docs_code (document_code),
        INDEX idx_master_docs_title (title)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
};
