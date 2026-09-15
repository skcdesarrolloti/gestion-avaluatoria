<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $now = gmdate('Y-m-d H:i:s');
    $documents = [
        ['b1-01-ley-388-1997', '1', 'B1-01', 'Ley 388 de 1997', 'Ley', 'vigente', '1997-07-18', 'Ordenamiento territorial aplicable a inmuebles urbanos.', 101],
        ['b1-02-decreto-1170-2015-capitulo-3', '1', 'B1-02', 'Decreto 1170 de 2015, Capítulo 3', 'Decreto', 'vigente', '2015-05-28', 'Marco sectorial relacionado con avalúos y gestión catastral.', 102],
        ['b1-03-resolucion-igac-941-2026', '1', 'B1-03', 'Resolución IGAC 941 de 2026', 'Resolución', 'vigente', null, 'Referencia IGAC principal indicada para la categoría de inmuebles urbanos.', 103],
        ['b1-04-decreto-1077-2015-sector-vivienda', '1', 'B1-04', 'Decreto 1077 de 2015 - Sector Vivienda, Ciudad y Territorio', 'Decreto', 'vigente', '2015-05-26', 'POT, usos, licenciamiento y desarrollo urbanístico.', 104],
        ['b1-05-ley-675-2001-propiedad-horizontal', '1', 'B1-05', 'Ley 675 de 2001', 'Ley', 'vigente', '2001-08-03', 'Aplica cuando el inmueble esté sometido a propiedad horizontal.', 105],
        ['b1-06-ley-400-1997-sismorresistente', '1', 'B1-06', 'Ley 400 de 1997 y reglamentación sismorresistente', 'Ley', 'vigente', '1997-08-19', 'Aplica cuando las condiciones constructivas sean relevantes.', 106],
        ['b1-07-resolucion-1040-2023-catastro', '1', 'B1-07', 'Resolución 1040 de 2023 y modificaciones', 'Resolución', 'vigente', null, 'Información catastral aplicable al análisis del inmueble urbano.', 107],
        ['b1-08-pot-pbot-eot-normas-urbanisticas', '1', 'B1-08', 'POT/PBOT/EOT municipal y normas urbanísticas específicas', 'Norma local', 'vigente', null, 'Instrumentos territoriales y normas urbanísticas del municipio del predio.', 108],
    ];
    $isSqlite = $schema->db->getAttribute(\PDO::ATTR_DRIVER_NAME) === 'sqlite';
    $sql = $isSqlite ? "INSERT INTO valuation_legal_documents
        (slug, category_code, document_code, title, document_type, status, issued_at, source_reference,
        summary, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON CONFLICT(slug) DO UPDATE SET category_code = excluded.category_code,
        document_code = excluded.document_code, title = excluded.title, document_type = excluded.document_type,
        status = excluded.status, issued_at = excluded.issued_at, source_reference = excluded.source_reference,
        summary = excluded.summary, sort_order = excluded.sort_order, updated_at = excluded.updated_at"
        : "INSERT INTO valuation_legal_documents
        (slug, category_code, document_code, title, document_type, status, issued_at, source_reference,
        summary, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE category_code = VALUES(category_code), document_code = VALUES(document_code),
        title = VALUES(title), document_type = VALUES(document_type), status = VALUES(status),
        issued_at = VALUES(issued_at), source_reference = VALUES(source_reference), summary = VALUES(summary),
        sort_order = VALUES(sort_order), updated_at = VALUES(updated_at)";
    $query = $schema->db->prepare($sql);
    foreach ($documents as $document) {
        $query->execute([...array_slice($document, 0, 7), 'Bibliografía B1 - Inmuebles urbanos', $document[7], $document[8], $now, $now]);
    }
    $standards = [
        ['ivs-400-real-property', '1,2,3,4,5,6', 'Derechos sobre bienes inmuebles. Relacionada con B1-T01 para inmuebles urbanos.', 'IVSC IVS effective 31 January 2025 · B1-T01'],
        ['ivs-410-development', '1,6', 'Inmuebles en desarrollo. Relacionada con B1-T02 cuando el urbano corresponda.', 'IVSC IVS effective 31 January 2025 · B1-T02'],
    ];
    $update = $schema->db->prepare('UPDATE valuation_international_standards
        SET applicable_categories = ?, summary = ?, source_reference = ?, updated_at = ? WHERE slug = ?');
    foreach ($standards as $standard) {
        $update->execute([$standard[1], $standard[2], $standard[3], $now, $standard[0]]);
    }
};
