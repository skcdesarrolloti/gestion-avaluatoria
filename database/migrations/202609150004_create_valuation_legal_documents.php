<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->db->exec("CREATE TABLE IF NOT EXISTS valuation_legal_categories (
        code VARCHAR(4) PRIMARY KEY,
        name VARCHAR(180) NOT NULL,
        group_type VARCHAR(20) NOT NULL,
        sort_order SMALLINT UNSIGNED NOT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $schema->db->exec("CREATE TABLE IF NOT EXISTS valuation_legal_documents (
        slug VARCHAR(100) PRIMARY KEY,
        category_code VARCHAR(4) NOT NULL,
        document_code VARCHAR(80) NOT NULL,
        title VARCHAR(240) NOT NULL,
        document_type VARCHAR(30) NOT NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'vigente',
        issued_at DATE NULL,
        repealed_at DATE NULL,
        source_reference VARCHAR(240) NOT NULL DEFAULT '',
        summary VARCHAR(320) NOT NULL DEFAULT '',
        sort_order SMALLINT UNSIGNED NOT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        INDEX idx_vleg_category_sort (category_code, sort_order),
        INDEX idx_vleg_code (document_code),
        INDEX idx_vleg_status (status),
        CONSTRAINT fk_vleg_category FOREIGN KEY (category_code)
            REFERENCES valuation_legal_categories (code)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $now = gmdate('Y-m-d H:i:s');
    $categories = [
        ['A', 'Marco jurídico general', 'general', 0],
        ['B', 'Marco jurídico específico', 'specific', 1],
        ['1', 'Inmuebles urbanos', 'category', 11],
        ['2', 'Inmuebles rurales', 'category', 12],
        ['3', 'Recursos naturales y suelos de protección', 'category', 13],
        ['4', 'Obras de infraestructura', 'category', 14],
        ['5', 'Edificaciones de conservación arqueológica y monumentos históricos', 'category', 15],
        ['6', 'Inmuebles especiales', 'category', 16],
        ['7', 'Maquinaria fija, equipos y maquinaria móvil', 'category', 17],
        ['8', 'Maquinaria y equipos especiales', 'category', 18],
        ['9', 'Obras de arte, orfebrería, patrimoniales y similares', 'category', 19],
        ['10', 'Semovientes y animales', 'category', 20],
        ['11', 'Activos operacionales y establecimientos de comercio', 'category', 21],
        ['12', 'Intangibles', 'category', 22],
        ['13', 'Intangibles especiales', 'category', 23],
    ];
    $query = $schema->db->prepare('INSERT INTO valuation_legal_categories
        (code, name, group_type, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE name = VALUES(name), group_type = VALUES(group_type),
        sort_order = VALUES(sort_order), updated_at = VALUES(updated_at)');
    foreach ($categories as $category) {
        $query->execute([...$category, $now, $now]);
    }
};
