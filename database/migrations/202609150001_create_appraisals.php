<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->db->exec("CREATE TABLE IF NOT EXISTS appraisals (
        id CHAR(32) PRIMARY KEY,
        owner_id BIGINT UNSIGNED NOT NULL COMMENT '_ID del funcionario; no es id_empleado',
        titulo VARCHAR(160) NOT NULL DEFAULT '',
        tipo VARCHAR(30) NOT NULL DEFAULT '',
        direccion VARCHAR(220) NOT NULL DEFAULT '',
        municipio VARCHAR(120) NOT NULL DEFAULT '',
        version INT UNSIGNED NOT NULL DEFAULT 1,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        INDEX idx_appraisals_owner_updated (owner_id, updated_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
};
