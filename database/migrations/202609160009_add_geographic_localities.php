<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->db->exec("CREATE TABLE IF NOT EXISTS master_localities (
        id CHAR(32) PRIMARY KEY,
        city_id CHAR(32) NOT NULL,
        name VARCHAR(160) NOT NULL,
        active ENUM('Si','No') NOT NULL DEFAULT 'Si',
        notes VARCHAR(240) NOT NULL DEFAULT '',
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        UNIQUE KEY uq_master_localities_city_name (city_id, name),
        INDEX idx_master_localities_city_active (city_id, active, name),
        CONSTRAINT fk_master_localities_city FOREIGN KEY (city_id)
            REFERENCES master_cities (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $schema->addColumn('master_neighborhoods', 'locality_id', 'CHAR(32) NULL AFTER city_id');
};
