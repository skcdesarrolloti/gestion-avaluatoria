<?php declare(strict_types=1);

return static function (App\Database\Schema $schema): void {
    $now = gmdate('Y-m-d H:i:s');
    $documents = [
        ['b11-01-decreto-410-1971-establecimiento', '11', 'B11-01', 'Código de Comercio - Decreto 410 de 1971', 'Decreto', '1971-03-27', 'Artículos 515 y 516 sobre establecimiento de comercio y sus elementos.', 1101],
        ['b11-02-ley-222-1995-societaria', '11', 'B11-02', 'Ley 222 de 1995', 'Ley', '1995-12-20', 'Aplica cuando corresponda materia societaria.', 1102],
        ['b11-03-ley-1116-2006-insolvencia', '11', 'B11-03', 'Ley 1116 de 2006', 'Ley', '2006-12-27', 'Insolvencia, reorganización o liquidación.', 1103],
        ['b11-04-decreto-2420-2015-niif', '11', 'B11-04', 'Decreto 2420 de 2015', 'Decreto', '2015-12-14', 'NIIF y medición financiera.', 1104],
        ['b11-05-estatuto-tributario-decreto-1625-2016', '11', 'B11-05', 'Estatuto Tributario y Decreto 1625 de 2016', 'Norma tributaria', '2016-10-11', 'Aplica cuando el propósito sea tributario.', 1105],
        ['b12-01-decision-andina-486-2000', '12', 'B12-01', 'Decisión Andina 486 de 2000', 'Decisión Andina', '2000-09-14', 'Propiedad industrial: patentes, modelos, diseños, marcas y nombres comerciales.', 1201],
        ['b12-02-decision-andina-351-1993-intangibles', '12', 'B12-02', 'Decisión Andina 351 de 1993', 'Decisión Andina', '1993-12-17', 'Derechos de autor.', 1202],
        ['b12-03-ley-23-1982-intangibles', '12', 'B12-03', 'Ley 23 de 1982', 'Ley', '1982-01-28', 'Derechos de autor.', 1203],
        ['b12-04-ley-44-1993', '12', 'B12-04', 'Ley 44 de 1993', 'Ley', '1993-02-05', 'Derechos de autor y gestión relacionada.', 1204],
        ['b12-05-codigo-comercio-intangibles', '12', 'B12-05', 'Código de Comercio', 'Decreto', '1971-03-27', 'Nombre comercial, establecimiento y componentes mercantiles.', 1205],
        ['b12-06-ley-1341-2009-tic', '12', 'B12-06', 'Ley 1341 de 2009', 'Ley', '2009-07-30', 'Espectro radioeléctrico y sector TIC, con modificaciones aplicables.', 1206],
        ['b12-07-decreto-1078-2015-tic', '12', 'B12-07', 'Decreto 1078 de 2015', 'Decreto', '2015-05-26', 'Decreto único del sector TIC.', 1207],
        ['b12-08-decreto-2420-2015-intangibles', '12', 'B12-08', 'Decreto 2420 de 2015', 'Decreto', '2015-12-14', 'Intangibles y combinaciones de negocios para fines financieros.', 1208],
        ['b13-01-codigo-civil-derechos', '13', 'B13-01', 'Código Civil', 'Código', null, 'Responsabilidad, indemnización, servidumbres, sucesiones y derechos litigiosos.', 1301],
        ['b13-02-ley-1564-2012-cgp', '13', 'B13-02', 'Ley 1564 de 2012 - Código General del Proceso', 'Ley', '2012-07-12', 'Código General del Proceso.', 1302],
        ['b13-05-ley-1274-2009-servidumbres', '13', 'B13-05', 'Ley 1274 de 2009', 'Ley', '2009-01-05', 'Servidumbres de hidrocarburos y procedimiento de avalúo.', 1305],
        ['b13-06-ley-56-1981-servidumbres', '13', 'B13-06', 'Ley 56 de 1981', 'Ley', '1981-09-01', 'Servidumbres eléctricas, acueductos y determinadas obras públicas.', 1306],
        ['b13-07-ley-142-1994-servidumbres', '13', 'B13-07', 'Ley 142 de 1994', 'Ley', '1994-07-11', 'Servidumbres de servicios públicos.', 1307],
        ['b13-08-leyes-1682-1742-infraestructura', '13', 'B13-08', 'Ley 1682 de 2013 y Ley 1742 de 2014', 'Ley', null, 'Perjuicios derivados de infraestructura de transporte.', 1308],
        ['b13-09-resoluciones-898-1044-infraestructura', '13', 'B13-09', 'Resoluciones IGAC 898 y 1044 de 2014', 'Resolución', null, 'Indemnizaciones asociadas a infraestructura de transporte.', 1309],
        ['b13-10-jurisprudencia-perjuicios', '13', 'B13-10', 'Jurisprudencia según tipo de perjuicio', 'Jurisprudencia', null, 'Corte Suprema, Consejo de Estado y Corte Constitucional, según el derecho valorado.', 1310],
    ];
    $upsert = $schema->db->getAttribute(\PDO::ATTR_DRIVER_NAME) === 'sqlite'
        ? "INSERT INTO valuation_legal_documents (slug, category_code, document_code, title, document_type,
        status, issued_at, source_reference, summary, sort_order, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, 'vigente', ?, 'Bibliografía B11-B13', ?, ?, ?, ?)
        ON CONFLICT(slug) DO UPDATE SET category_code = excluded.category_code,
        document_code = excluded.document_code, title = excluded.title, document_type = excluded.document_type,
        issued_at = excluded.issued_at, source_reference = excluded.source_reference,
        summary = excluded.summary, sort_order = excluded.sort_order, updated_at = excluded.updated_at"
        : "INSERT INTO valuation_legal_documents (slug, category_code, document_code, title, document_type,
        status, issued_at, source_reference, summary, sort_order, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, 'vigente', ?, 'Bibliografía B11-B13', ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE category_code = VALUES(category_code), document_code = VALUES(document_code),
        title = VALUES(title), document_type = VALUES(document_type), issued_at = VALUES(issued_at),
        source_reference = VALUES(source_reference), summary = VALUES(summary), sort_order = VALUES(sort_order),
        updated_at = VALUES(updated_at)";
    $query = $schema->db->prepare($upsert);
    foreach ($documents as $document) $query->execute([...$document, $now, $now]);
    $articles = [
        ['b13-02-ley-1564-2012-cgp', '13', 'B13-03 - Artículo 206', 'Juramento estimatorio', 'Artículo pendiente de transcripción exacta.', 'Citar cuando el avalúo soporte indemnización, compensación, frutos o mejoras.', 1303],
        ['b13-02-ley-1564-2012-cgp', '13', 'B13-04 - Artículo 226', 'Dictamen pericial', 'Artículo pendiente de transcripción exacta.', 'Citar cuando el informe actúe como dictamen pericial.', 1304],
    ];
    $insertArticle = $schema->db->prepare("INSERT INTO valuation_legal_articles (document_slug, category_code,
        article_label, title, excerpt, applicability, status, sort_order, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, 'vigente', ?, ?, ?)");
    $findArticle = $schema->db->prepare('SELECT id FROM valuation_legal_articles WHERE document_slug = ?
        AND category_code = ? AND article_label = ? LIMIT 1');
    $updateArticle = $schema->db->prepare('UPDATE valuation_legal_articles SET title = ?, excerpt = ?,
        applicability = ?, status = ?, sort_order = ?, updated_at = ? WHERE id = ?');
    foreach ($articles as $article) {
        $findArticle->execute([$article[0], $article[1], $article[2]]);
        $id = $findArticle->fetchColumn();
        if ($id) { $updateArticle->execute([$article[3], $article[4], $article[5], 'vigente', $article[6], $now, $id]); continue; }
        $insertArticle->execute([...$article, $now, $now]);
    }
    $international = [
        ['ivs-200-businesses', '11', 'Empresas, participaciones y negocios.', 'IVSC IVS effective 31 January 2025 · B11-T01'],
        ['ivs-210-intangibles', '12,13', 'Activos intangibles y derechos especiales.', 'IVSC IVS effective 31 January 2025 · B12-T01'],
        ['ivs-230-inventory', '11', 'Inventarios vinculados al negocio.', 'IVSC IVS effective 31 January 2025 · B11-T02'],
        ['ivs-300-infrastructure', '4,7,8,11', 'Planta, equipos, maquinaria e infraestructura.', 'IVSC IVS effective 31 January 2025 · B4-T01/B7-T01/B8-T01/B11-T03'],
        ['ivs-400-real-property', '1,2,4,5,6', 'Derechos sobre bienes inmuebles.', 'IVSC IVS effective 31 January 2025 · B1-T01/B2-T01/B4-T02/B5-T01/B6-T01'],
        ['ivs-410-development', '1,6', 'Inmuebles en desarrollo.', 'IVSC IVS effective 31 January 2025 · B1-T02/B6-T02'],
    ];
    $update = $schema->db->prepare('UPDATE valuation_international_standards
        SET applicable_categories = ?, summary = ?, source_reference = ?, updated_at = ? WHERE slug = ?');
    foreach ($international as $row) $update->execute([$row[1], $row[2], $row[3], $now, $row[0]]);
};
