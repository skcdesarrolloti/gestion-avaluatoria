<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->db->exec("CREATE TABLE IF NOT EXISTS valuation_ifrs_groups (
        code VARCHAR(8) PRIMARY KEY,
        name VARCHAR(180) NOT NULL,
        sort_order SMALLINT UNSIGNED NOT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $schema->db->exec("CREATE TABLE IF NOT EXISTS valuation_ifrs_standards (
        slug VARCHAR(100) PRIMARY KEY,
        group_code VARCHAR(8) NOT NULL,
        standard_code VARCHAR(40) NOT NULL,
        title VARCHAR(220) NOT NULL,
        applicable_categories VARCHAR(80) NOT NULL DEFAULT '',
        measurement_focus VARCHAR(160) NOT NULL DEFAULT '',
        summary VARCHAR(360) NOT NULL DEFAULT '',
        field_relevance VARCHAR(260) NOT NULL DEFAULT '',
        source_reference VARCHAR(240) NOT NULL DEFAULT '',
        status VARCHAR(20) NOT NULL DEFAULT 'vigente',
        source_filename VARCHAR(220) NOT NULL DEFAULT '',
        storage_filename VARCHAR(220) NOT NULL DEFAULT '',
        file_size_bytes BIGINT UNSIGNED NULL,
        imported_at DATETIME NULL,
        sort_order SMALLINT UNSIGNED NOT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        INDEX idx_vifrs_group_sort (group_code, sort_order),
        INDEX idx_vifrs_code (standard_code),
        CONSTRAINT fk_vifrs_group FOREIGN KEY (group_code) REFERENCES valuation_ifrs_groups (code)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $schema->db->exec("CREATE TABLE IF NOT EXISTS valuation_field_considerations (
        field_key VARCHAR(80) PRIMARY KEY,
        field_label VARCHAR(160) NOT NULL,
        classification VARCHAR(40) NOT NULL,
        normative_basis VARCHAR(240) NOT NULL DEFAULT '',
        operational_use VARCHAR(360) NOT NULL DEFAULT '',
        ifrs_relation VARCHAR(220) NOT NULL DEFAULT '',
        sort_order SMALLINT UNSIGNED NOT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $now = gmdate('Y-m-d H:i:s');
    $groups = [['G', 'Medición y valor razonable', 0], ['A', 'Activos tangibles e inmuebles', 1],
        ['D', 'Deterioro y recuperabilidad', 2], ['I', 'Intangibles y negocios', 3],
        ['P', 'Inventarios, agro y mantenidos para venta', 4], ['F', 'Financieros y revelaciones', 5]];
    $groupSql = "INSERT INTO valuation_ifrs_groups (code, name, sort_order, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name),
        sort_order = VALUES(sort_order), updated_at = VALUES(updated_at)";
    $groupQuery = $schema->db->prepare($groupSql);
    foreach ($groups as $group) $groupQuery->execute([...$group, $now, $now]);
    $standards = [
        ['ifrs-13-fair-value-measurement', 'G', 'NIIF 13 / IFRS 13', 'Medición del valor razonable', 'A,1,2,3,4,5,6,7,8,9,10,11,12,13', 'Valor razonable', 'Define el marco NIIF para mediciones de valor razonable y revelaciones relacionadas.', 'Respalda el campo base/tipo de valor cuando el encargo sea contable o financiero.', 'IFRS Foundation - IFRS 13 Fair Value Measurement', 1],
        ['ias-16-property-plant-equipment', 'A', 'NIC 16 / IAS 16', 'Propiedades, planta y equipo', '1,2,4,6,7,8,11', 'Costo, depreciación y revaluación', 'Activos tangibles mantenidos para uso, alquiler operativo o administración.', 'Relaciona tipo de inmueble, estructura del método y medición posterior si aplica NIIF.', 'IFRS Foundation - IAS 16 Property, Plant and Equipment', 1],
        ['ias-40-investment-property', 'A', 'NIC 40 / IAS 40', 'Propiedades de inversión', '1,2,6,11', 'Modelo de valor razonable o costo', 'Inmuebles mantenidos para rentas, valorización o ambos.', 'Distingue negocio, destinación y base de valor en activos inmobiliarios de inversión.', 'IFRS Foundation - IAS 40 Investment Property', 2],
        ['ifrs-16-leases', 'A', 'NIIF 16 / IFRS 16', 'Arrendamientos', '1,2,6,11,13', 'Derechos de uso y pasivos por arrendamiento', 'Tratamiento contable de contratos de arrendamiento y activos por derecho de uso.', 'Apoya tipo de derecho y tipo de negocio cuando el encargo sea arriendo o derecho de uso.', 'IFRS Foundation - IFRS 16 Leases', 3],
        ['ias-36-impairment-assets', 'D', 'NIC 36 / IAS 36', 'Deterioro del valor de los activos', '1,2,4,6,7,8,11,12,13', 'Importe recuperable', 'Evalúa deterioro mediante importe recuperable, valor en uso y valor razonable menos costos.', 'Se conecta con finalidad interna, patrimonial o financiera y con valor recuperable.', 'IFRS Foundation - IAS 36 Impairment of Assets', 1],
        ['ias-38-intangible-assets', 'I', 'NIC 38 / IAS 38', 'Activos intangibles', '12,13', 'Reconocimiento y medición de intangibles', 'Criterios de reconocimiento, medición y revelación de activos sin sustancia física.', 'Sustenta tipo de derecho, categoría intangible y base de valor bajo enfoque contable.', 'IFRS Foundation - IAS 38 Intangible Assets', 1],
        ['ifrs-3-business-combinations', 'I', 'NIIF 3 / IFRS 3', 'Combinaciones de negocios', '11,12,13', 'Asignación de precio de compra', 'Identificación y medición de activos adquiridos y pasivos asumidos en una combinación.', 'Aplica a valoración de empresas, unidades de negocio e intangibles identificables.', 'IFRS Foundation - IFRS 3 Business Combinations', 2],
        ['ias-2-inventories', 'P', 'NIC 2 / IAS 2', 'Inventarios', '7,8,10,11', 'Costo y valor neto realizable', 'Guía medición de inventarios al menor entre costo y valor neto realizable.', 'Relaciona activos operacionales, inventarios, maquinaria para venta y semovientes como inventario.', 'IFRS Foundation - IAS 2 Inventories', 1],
        ['ias-41-agriculture', 'P', 'NIC 41 / IAS 41', 'Agricultura', '2,10,11', 'Activos biológicos', 'Tratamiento contable de activos biológicos y productos agrícolas.', 'Útil para semovientes, animales, cultivos y explotación rural bajo medición contable.', 'IFRS Foundation - IAS 41 Agriculture', 2],
        ['ifrs-5-held-for-sale', 'P', 'NIIF 5 / IFRS 5', 'Activos no corrientes mantenidos para la venta', '1,2,4,6,7,8,11,12', 'Valor razonable menos costos de venta', 'Clasificación y medición de activos disponibles para venta y operaciones discontinuadas.', 'Apoya finalidad de venta, negociación o desinversión bajo estado financiero.', 'IFRS Foundation - IFRS 5 Non-current Assets Held for Sale', 3],
        ['ifrs-9-financial-instruments', 'F', 'NIIF 9 / IFRS 9', 'Instrumentos financieros', '11,13', 'Clasificación, medición y deterioro financiero', 'Reglas de medición para activos y pasivos financieros.', 'Pertinente cuando el avalúo involucre instrumentos financieros o derechos económicos.', 'IFRS Foundation - IFRS 9 Financial Instruments', 1],
        ['ifrs-7-financial-disclosures', 'F', 'NIIF 7 / IFRS 7', 'Instrumentos financieros: información a revelar', '11,13', 'Revelaciones financieras', 'Revelaciones sobre importancia y riesgos de instrumentos financieros.', 'Complementa soporte documental cuando el informe tenga finalidad financiera.', 'IFRS Foundation - IFRS 7 Financial Instruments: Disclosures', 2],
    ];
    $standardSql = "INSERT INTO valuation_ifrs_standards (slug, group_code, standard_code, title,
        applicable_categories, measurement_focus, summary, field_relevance, source_reference,
        status, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'vigente', ?, ?, ?)
        ON DUPLICATE KEY UPDATE group_code = VALUES(group_code), standard_code = VALUES(standard_code),
        title = VALUES(title), applicable_categories = VALUES(applicable_categories),
        measurement_focus = VALUES(measurement_focus), summary = VALUES(summary),
        field_relevance = VALUES(field_relevance), source_reference = VALUES(source_reference),
        sort_order = VALUES(sort_order), updated_at = VALUES(updated_at)";
    $standardQuery = $schema->db->prepare($standardSql);
    foreach ($standards as $standard) $standardQuery->execute([...$standard, $now, $now]);
    $fields = [
        ['tipo_avaluo', 'Tipo de avalúo', 'Derivado metodológico', 'NTS e IVS según encargo', 'Clasifica el enfoque profesional del trabajo: comercial, posesión, mejoras, negociación, remate u otros.', 'Puede activar o no NIIF según finalidad contable.', 1],
        ['tipo_derecho', 'Tipo de derecho', 'Normativo directo', 'Código Civil, Ley 675, IVS 400, NIIF 16', 'Precisa si se valora dominio, posesión, usufructo, arrendamiento u otro derecho.', 'Clave para derechos de uso, arrendamientos e inmuebles.', 2],
        ['tipo_negocio', 'Tipo de negocio', 'Operativo interno', 'Contrato o instrucción del cliente', 'Diferencia venta, arriendo u otro negocio para no mezclar referencias económicas.', 'Conecta con NIC 40, NIIF 16 o NIIF 5 según uso.', 3],
        ['destinacion', 'Destinación', 'Derivado normativo', 'POT, uso económico, NTS e IVS', 'Ubica el uso principal: residencial, comercial, institucional, industrial, dotacional, lote o mixto.', 'Impacta NIC 16, NIC 40 y deterioro.', 4],
        ['tipo_inmueble', 'Tipo de inmueble', 'Operativo interno', 'Categoría valuatoria y ficha técnica', 'Activa formularios, atributos y controles específicos por tipología.', 'Ayuda a seleccionar NIC 16, NIC 40 o NIIF 5.', 5],
        ['base_valor', 'Base/tipo de valor', 'Normativo directo', 'NTS, IVS 102, NIIF 13, NIC 36', 'Define valor de mercado, razonable, recuperable, renta, catastral o residual.', 'Campo central cuando aplica medición NIIF.', 6],
        ['aplica_niif', '¿Aplica NIIF?', 'Operativo de control', 'Finalidad contable o instrucción del encargo', 'Indica si el lenguaje técnico debe conectarse con medición financiera.', 'Dispara consulta de esta biblioteca NIIF.', 7],
        ['finalidad', 'Finalidad', 'Derivado normativo', 'Contrato, proceso judicial, garantía, contabilidad o decisión corporativa', 'Aclara para qué será usado el informe y qué marco debe citarse.', 'Define si NIIF es principal, complementaria o no aplicable.', 8],
        ['regimen_ph', 'Régimen PH', 'Normativo directo', 'Ley 675 de 2001', 'Separa propiedad privada y bienes comunes en inmuebles sometidos a PH.', 'Puede afectar unidad de cuenta en medición financiera.', 9],
        ['estructura_metodo', 'Estructura del método', 'Derivado metodológico', 'NTS, IVS 103 e IVS 105', 'Ordena si se valora terreno, construcción, negocio, derecho o activo compuesto.', 'Ayuda a justificar supuestos contables si aplica NIIF.', 10],
    ];
    $fieldSql = "INSERT INTO valuation_field_considerations (field_key, field_label, classification,
        normative_basis, operational_use, ifrs_relation, sort_order, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE field_label = VALUES(field_label),
        classification = VALUES(classification), normative_basis = VALUES(normative_basis),
        operational_use = VALUES(operational_use), ifrs_relation = VALUES(ifrs_relation),
        sort_order = VALUES(sort_order), updated_at = VALUES(updated_at)";
    $fieldQuery = $schema->db->prepare($fieldSql);
    foreach ($fields as $field) $fieldQuery->execute([...$field, $now, $now]);
};
