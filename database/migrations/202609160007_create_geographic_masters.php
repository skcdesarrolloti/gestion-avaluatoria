<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->db->exec("CREATE TABLE IF NOT EXISTS master_departments (
        id CHAR(32) PRIMARY KEY,
        code VARCHAR(20) NOT NULL DEFAULT '',
        name VARCHAR(120) NOT NULL,
        active ENUM('Si','No') NOT NULL DEFAULT 'Si',
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        UNIQUE KEY uq_master_departments_name (name),
        INDEX idx_master_departments_active_name (active, name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $schema->db->exec("CREATE TABLE IF NOT EXISTS master_cities (
        id CHAR(32) PRIMARY KEY,
        department_id CHAR(32) NOT NULL,
        code VARCHAR(20) NOT NULL DEFAULT '',
        name VARCHAR(140) NOT NULL,
        active ENUM('Si','No') NOT NULL DEFAULT 'Si',
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        UNIQUE KEY uq_master_cities_department_name (department_id, name),
        INDEX idx_master_cities_department_active (department_id, active, name),
        CONSTRAINT fk_master_cities_department FOREIGN KEY (department_id)
            REFERENCES master_departments (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $schema->db->exec("CREATE TABLE IF NOT EXISTS master_neighborhoods (
        id CHAR(32) PRIMARY KEY,
        city_id CHAR(32) NOT NULL,
        name VARCHAR(160) NOT NULL,
        active ENUM('Si','No') NOT NULL DEFAULT 'Si',
        notes VARCHAR(240) NOT NULL DEFAULT '',
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        UNIQUE KEY uq_master_neighborhoods_city_name (city_id, name),
        INDEX idx_master_neighborhoods_city_active (city_id, active, name),
        CONSTRAINT fk_master_neighborhoods_city FOREIGN KEY (city_id)
            REFERENCES master_cities (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
};
