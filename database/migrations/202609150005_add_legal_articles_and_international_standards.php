<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->db->exec("CREATE TABLE IF NOT EXISTS valuation_legal_articles (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        document_slug VARCHAR(100) NOT NULL,
        category_code VARCHAR(4) NOT NULL,
        article_label VARCHAR(80) NOT NULL,
        title VARCHAR(220) NOT NULL DEFAULT '',
        excerpt TEXT NOT NULL,
        applicability VARCHAR(320) NOT NULL DEFAULT '',
        status VARCHAR(20) NOT NULL DEFAULT 'vigente',
        sort_order SMALLINT UNSIGNED NOT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        INDEX idx_vlega_category_sort (category_code, sort_order),
        INDEX idx_vlega_document (document_slug),
        CONSTRAINT fk_vlega_category FOREIGN KEY (category_code)
            REFERENCES valuation_legal_categories (code),
        CONSTRAINT fk_vlega_document FOREIGN KEY (document_slug)
            REFERENCES valuation_legal_documents (slug)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $schema->db->exec("CREATE TABLE IF NOT EXISTS valuation_international_groups (
        code VARCHAR(8) PRIMARY KEY,
        name VARCHAR(180) NOT NULL,
        sort_order SMALLINT UNSIGNED NOT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $schema->db->exec("CREATE TABLE IF NOT EXISTS valuation_international_standards (
        slug VARCHAR(100) PRIMARY KEY,
        group_code VARCHAR(8) NOT NULL,
        standard_code VARCHAR(40) NOT NULL,
        title VARCHAR(220) NOT NULL,
        applicable_categories VARCHAR(80) NOT NULL DEFAULT '',
        summary VARCHAR(320) NOT NULL DEFAULT '',
        effective_from DATE NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'vigente',
        source_reference VARCHAR(240) NOT NULL DEFAULT '',
        sort_order SMALLINT UNSIGNED NOT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        INDEX idx_vints_group_sort (group_code, sort_order),
        INDEX idx_vints_code (standard_code),
        CONSTRAINT fk_vints_group FOREIGN KEY (group_code)
            REFERENCES valuation_international_groups (code)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $now = gmdate('Y-m-d H:i:s');
    $groups = [['G', 'Normas generales IVS', 0], ['200', 'Empresas, pasivos e inventarios', 1],
        ['210', 'Intangibles', 2], ['300', 'Planta, equipos e infraestructura', 3],
        ['400', 'Inmuebles', 4], ['500', 'Instrumentos financieros', 5]];
    $groupQuery = $schema->db->prepare('INSERT INTO valuation_international_groups
        (code, name, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE name = VALUES(name), sort_order = VALUES(sort_order),
        updated_at = VALUES(updated_at)');
    foreach ($groups as $group) $groupQuery->execute([...$group, $now, $now]);
    $standards = [
        ['ivs-100-framework', 'G', 'IVS 100', 'Valuation Framework', 'A,1,2,3,4,5,6,7,8,9,10,11,12,13', 'Marco general para trabajos de valuación.', 1],
        ['ivs-101-scope', 'G', 'IVS 101', 'Scope of Work', 'A,1,2,3,4,5,6,7,8,9,10,11,12,13', 'Alcance, términos y condiciones del encargo.', 2],
        ['ivs-102-bases', 'G', 'IVS 102', 'Bases of Value', 'A,1,2,3,4,5,6,7,8,9,10,11,12,13', 'Bases de valor usadas en el análisis.', 3],
        ['ivs-103-approaches', 'G', 'IVS 103', 'Valuation Approaches', 'A,1,2,3,4,5,6,7,8,9,10,11,12,13', 'Enfoques y métodos de valuación.', 4],
        ['ivs-104-data-inputs', 'G', 'IVS 104', 'Data and Inputs', 'A,1,2,3,4,5,6,7,8,9,10,11,12,13', 'Calidad y selección de datos e insumos.', 5],
        ['ivs-105-models', 'G', 'IVS 105', 'Valuation Models', 'A,1,2,3,4,5,6,7,8,9,10,11,12,13', 'Modelos de valuación y juicio profesional.', 6],
        ['ivs-106-reporting', 'G', 'IVS 106', 'Documentation and Reporting', 'A,1,2,3,4,5,6,7,8,9,10,11,12,13', 'Documentación y reporte del trabajo.', 7],
        ['ivs-200-businesses', '200', 'IVS 200', 'Businesses and Business Interests', '11', 'Empresas, participaciones y negocios.', 1],
        ['ivs-220-liabilities', '200', 'IVS 220', 'Non-Financial Liabilities', 'B,11,13', 'Pasivos no financieros cuando apliquen.', 2],
        ['ivs-230-inventory', '200', 'IVS 230', 'Inventory', '11', 'Inventarios vinculados al negocio.', 3],
        ['ivs-210-intangibles', '210', 'IVS 210', 'Intangible Assets', '12,13', 'Activos intangibles y derechos especiales.', 1],
        ['ivs-300-infrastructure', '300', 'IVS 300', 'Plant, Equipment and Infrastructure', '4,7,8,11', 'Planta, equipos, maquinaria e infraestructura.', 1],
        ['ivs-400-real-property', '400', 'IVS 400', 'Real Property Interests', '1,2,3,4,5,6', 'Derechos sobre bienes inmuebles.', 1],
        ['ivs-410-development', '400', 'IVS 410', 'Development Property', '1,6', 'Inmuebles en desarrollo.', 2],
        ['ivs-500-financial', '500', 'IVS 500', 'Financial Instruments', 'B,11,13', 'Instrumentos financieros cuando apliquen.', 1],
    ];
    $standardQuery = $schema->db->prepare("INSERT INTO valuation_international_standards
        (slug, group_code, standard_code, title, applicable_categories, summary, effective_from,
        status, source_reference, sort_order, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, '2025-01-31', 'vigente', 'IVSC IVS effective 31 January 2025', ?, ?, ?)
        ON DUPLICATE KEY UPDATE group_code = VALUES(group_code), title = VALUES(title),
        applicable_categories = VALUES(applicable_categories), summary = VALUES(summary),
        sort_order = VALUES(sort_order), updated_at = VALUES(updated_at)");
    foreach ($standards as $standard) $standardQuery->execute([...$standard, $now, $now]);
};
