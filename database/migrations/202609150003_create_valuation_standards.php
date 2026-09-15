<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->db->exec("CREATE TABLE IF NOT EXISTS valuation_standard_categories (
        code VARCHAR(4) PRIMARY KEY,
        name VARCHAR(180) NOT NULL,
        group_type VARCHAR(20) NOT NULL,
        sort_order SMALLINT UNSIGNED NOT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $schema->db->exec("CREATE TABLE IF NOT EXISTS valuation_standards (
        slug VARCHAR(80) PRIMARY KEY,
        category_code VARCHAR(4) NOT NULL,
        standard_code VARCHAR(40) NOT NULL,
        title VARCHAR(220) NOT NULL,
        kind VARCHAR(20) NOT NULL,
        sector_code VARCHAR(20) NOT NULL,
        source_filename VARCHAR(220) NOT NULL,
        storage_filename VARCHAR(220) NOT NULL,
        summary VARCHAR(280) NOT NULL DEFAULT '',
        file_size_bytes BIGINT UNSIGNED NULL,
        imported_at DATETIME NULL,
        sort_order SMALLINT UNSIGNED NOT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        INDEX idx_vstd_category_sort (category_code, sort_order),
        INDEX idx_vstd_code (standard_code),
        CONSTRAINT fk_vstd_category FOREIGN KEY (category_code)
            REFERENCES valuation_standard_categories (code)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $now = gmdate('Y-m-d H:i:s');
    $categories = [
        ['A', 'Normas Técnicas Generales', 'general', 0],
        ['B', 'Normas Técnicas Específicas', 'specific', 1],
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
    $categoryQuery = $schema->db->prepare('INSERT INTO valuation_standard_categories
        (code, name, group_type, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE name = VALUES(name), group_type = VALUES(group_type),
        sort_order = VALUES(sort_order), updated_at = VALUES(updated_at)');
    foreach ($categories as $category) {
        $categoryQuery->execute([...$category, $now, $now]);
    }
    $standards = [
        ['nts-s04-codigo-conducta', 'A', 'NTS S04', 'Código de conducta del avaluador', 'NTS', 'S04', '01 NTS S04 Codigo conducta.pdf', 'nts-s04-codigo-conducta.pdf', 1],
        ['nts-s03-contenido-informe-valuacion', 'A', 'NTS S03', 'Contenido de informes de valuación', 'NTS', 'S03', '02 NTS S03 Contenido Inf Valuacion.pdf', 'nts-s03-contenido-informe-valuacion.pdf', 2],
        ['gts-g02-conceptos-principios-valuacion', 'A', 'GTS G02', 'Conceptos y principios generales de valuación', 'GTS', 'G02', '03 GTS G02 Conceptos  Principios Valuacion.pdf', 'gts-g02-conceptos-principios-valuacion.pdf', 3],
        ['nts-g03-tipos-bienes', 'A', 'NTS G03', 'Tipos de bienes', 'NTS', 'G03', '04 NTS G03 Tipos Bienes.pdf', 'nts-g03-tipos-bienes.pdf', 4],
        ['nts-s01-bases-valor-mercado', 'A', 'NTS S01', 'Bases para la determinación del valor de mercado', 'NTS', 'S01', '05 NTS S01 Bases deter Vr Mercado.pdf', 'nts-s01-bases-valor-mercado.pdf', 5],
        ['nts-s02-bases-valor-distinto-mercado', 'A', 'NTS S02', 'Bases para determinar valores distintos al valor de mercado', 'NTS', 'S02', '06 NTS S02 Bases deter Vr Distinto Mercado.pdf', 'nts-s02-bases-valor-distinto-mercado.pdf', 6],
        ['nts-a02-garantia-creditos', 'A', 'NTS A02', 'Valuación para garantía de créditos', 'NTS', 'A02', '22NTS A02 Val GarantiaCreditos.pdf', 'nts-a02-garantia-creditos.pdf', 7],
        ['gts-e02-valuacion-arrendamiento', 'B', 'GTS E02', 'Valuación de arrendamiento', 'GTS', 'E02', '20 GTS E02 Valuac Arrendamiento.pdf', 'gts-e02-valuacion-arrendamiento.pdf', 1],
        ['gts-e01-valuacion-inmuebles', '1', 'GTS E01', 'Valuación de inmuebles', 'GTS', 'E01', '07 GTS E01 Valuac Inmuebles.pdf', 'gts-e01-valuacion-inmuebles.pdf', 1],
        ['nts-i01-contenido-informe-urbanos', '1', 'NTS I01', 'Contenido de informes de avalúos urbanos', 'NTS', 'I01', '08 NTS I01Contenido Informe Urbanos.pdf', 'nts-i01-contenido-informe-urbanos.pdf', 2],
        ['nts-m01-metodologias-valuacion', '1', 'NTS M01', 'Metodologías de valuación', 'NTS', 'M01', '09 NTS M01 Metodologias Val.pdf', 'nts-m01-metodologias-valuacion.pdf', 3],
        ['gts-e04-valuacion-rurales', '2', 'GTS E04', 'Valuación de inmuebles rurales', 'GTS', 'E04', '10 GTS E04 Valuac Rurales.pdf', 'gts-e04-valuacion-rurales.pdf', 1],
        ['nts-i02-contenido-informes-rurales', '2', 'NTS I02', 'Contenido de informes rurales', 'NTS', 'I02', '11 NTS I02 Contenido Informes Rurales.pdf', 'nts-i02-contenido-informes-rurales.pdf', 2],
        ['nts-m02-metodo-rural', '2', 'NTS M02', 'Método rural', 'NTS', 'M02', '12 NTS M 02 Metodo Rural.pdf', 'nts-m02-metodo-rural.pdf', 3],
        ['gts-e03-valuacion-maquinaria-equipo', '7', 'GTS E03', 'Valuación de maquinaria y equipo', 'GTS', 'E03', '13 GTS E03 Val Maq Equipo.pdf', 'gts-e03-valuacion-maquinaria-equipo.pdf', 1],
        ['nts-i04-contenido-maquinaria', '7', 'NTS I04', 'Contenido de informes de maquinaria', 'NTS', 'I04', '14 NTS I04 Contenido Maquinaria.pdf', 'nts-i04-contenido-maquinaria.pdf', 2],
        ['nts-m04-metodologia-maquinaria-equipo', '7', 'NTS M04', 'Metodología de maquinaria y equipo', 'NTS', 'M04', '15 NTS M04 Metodolog Maq Equipo.pdf', 'nts-m04-metodologia-maquinaria-equipo.pdf', 3],
        ['nts-m06-maquinaria-equipos-especiales', '8', 'NTS M06', 'Metodología para maquinaria y equipos especiales', 'NTS', 'M06', '16 NTS M06 Metodologia para la valuacion de maquinaria y equipos especiales.pdf', 'nts-m06-maquinaria-equipos-especiales.pdf', 1],
        ['nts-m07-empresas-unidad-negocio', '11', 'NTS M07', 'Valuación de empresas y unidades de negocio', 'NTS', 'M07', '17 NTS M07 Val Empresas Und Negocio.pdf', 'nts-m07-empresas-unidad-negocio.pdf', 1],
        ['gts-e07-metodo-empresas', '11', 'GTS E07', 'Método de empresas', 'GTS', 'E07', '18 GTS E07 Metodo Empresas.pdf', 'gts-e07-metodo-empresas.pdf', 2],
        ['gts-e05-valuacion-intangibles', '12', 'GTS E05', 'Valuación de intangibles', 'GTS', 'E05', '19 GTS E05 Val Intangibles.pdf', 'gts-e05-valuacion-intangibles.pdf', 1],
        ['nts-m05-generales-intangibles', '12', 'NTS M05', 'Generalidades de intangibles', 'NTS', 'M05', '21 NTS M05 Generales Intangibles.pdf', 'nts-m05-generales-intangibles.pdf', 2],
    ];
    $standardQuery = $schema->db->prepare('INSERT INTO valuation_standards
        (slug, category_code, standard_code, title, kind, sector_code, source_filename, storage_filename, sort_order,
        created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE category_code = VALUES(category_code), standard_code = VALUES(standard_code),
        title = VALUES(title), kind = VALUES(kind), sector_code = VALUES(sector_code),
        source_filename = VALUES(source_filename), storage_filename = VALUES(storage_filename),
        sort_order = VALUES(sort_order), updated_at = VALUES(updated_at)');
    foreach ($standards as $standard) {
        $standardQuery->execute([...$standard, $now, $now]);
    }
};
