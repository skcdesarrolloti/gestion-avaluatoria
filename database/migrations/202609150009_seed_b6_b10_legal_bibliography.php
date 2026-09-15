<?php declare(strict_types=1);

return static function (App\Database\Schema $schema): void {
    $now = gmdate('Y-m-d H:i:s');
    $documents = [
        ['b6-01-decreto-1170-2015-especiales', '6', 'B6-01', 'Decreto 1170 de 2015', 'Decreto', '2015-05-28', 'Marco aplicable a inmuebles especiales.', 601],
        ['b6-02-resolucion-igac-941-2026', '6', 'B6-02', 'Resolución IGAC 941 de 2026', 'Resolución', null, 'Referencia IGAC principal indicada para inmuebles especiales.', 602],
        ['b6-03-ley-388-1997-especiales', '6', 'B6-03', 'Ley 388 de 1997', 'Ley', '1997-07-18', 'Ordenamiento territorial aplicable al inmueble especial.', 603],
        ['b6-04-decreto-1077-2015-especiales', '6', 'B6-04', 'Decreto 1077 de 2015', 'Decreto', '2015-05-26', 'POT, usos, licenciamiento y desarrollo urbanístico.', 604],
        ['b6-05-ley-675-2001-especiales-ph', '6', 'B6-05', 'Ley 675 de 2001', 'Ley', '2001-08-03', 'Aplica cuando exista propiedad horizontal.', 605],
        ['b6-06-ley-400-1997-especiales', '6', 'B6-06', 'Ley 400 de 1997 y reglamento sismorresistente', 'Ley', '1997-08-19', 'Aplica cuando las condiciones constructivas sean relevantes.', 606],
        ['b6-07-norma-sectorial-uso', '6', 'B6-07', 'Norma sectorial correspondiente al uso', 'Norma sectorial', null, 'Hotel, clínica, hospital, institución educativa u otro uso específico.', 607],
        ['b7-01-ley-1673-2013', '7', 'B7-01', 'Ley 1673 de 2013', 'Ley', '2013-07-19', 'Marco general del avaluador y la actividad valuatoria.', 701],
        ['b7-02-decreto-1074-2015', '7', 'B7-02', 'Decreto 1074 de 2015', 'Decreto', '2015-05-26', 'Decreto único del sector comercio, industria y turismo.', 702],
        ['b7-03-decreto-2420-2015', '7', 'B7-03', 'Decreto 2420 de 2015', 'Decreto', '2015-12-14', 'Aplica para información financiera, NIIF o medición contable.', 703],
        ['b7-04-ley-769-2002-transito', '7', 'B7-04', 'Ley 769 de 2002', 'Ley', '2002-08-06', 'Código Nacional de Tránsito, para vehículos.', 704],
        ['b7-05-reglamentos-tecnicos-sectoriales', '7', 'B7-05', 'Reglamentos técnicos sectoriales aplicables a cada equipo', 'Reglamento técnico', null, 'Normas técnicas del activo específico.', 705],
        ['b8-01-decreto-410-1971-comercio', '8', 'B8-01', 'Código de Comercio - Decreto 410 de 1971', 'Decreto', '1971-03-27', 'Aplica según la naturaleza comercial del activo especial.', 801],
        ['b8-02-decreto-1079-2015-transporte', '8', 'B8-02', 'Decreto 1079 de 2015 - Sector Transporte', 'Decreto', '2015-05-26', 'Marco sectorial de transporte.', 802],
        ['b8-03-rac-aeronaves', '8', 'B8-03', 'Reglamentos Aeronáuticos de Colombia - RAC', 'Reglamento técnico', null, 'Aplica para aeronaves.', 803],
        ['b8-04-dimar-naves', '8', 'B8-04', 'Normativa y registro de DIMAR', 'Reglamento técnico', null, 'Aplica para naves y artefactos navales.', 804],
        ['b8-05-regulacion-ferroviaria', '8', 'B8-05', 'Regulación ferroviaria vigente', 'Reglamento técnico', null, 'Aplica para trenes, locomotoras y vagones.', 805],
        ['b8-06-decreto-2420-2015-financiero', '8', 'B8-06', 'Decreto 2420 de 2015', 'Decreto', '2015-12-14', 'Aplica para fines financieros o contables.', 806],
        ['b9-01-ley-397-1997-arte', '9', 'B9-01', 'Ley 397 de 1997', 'Ley', '1997-08-07', 'Ley General de Cultura.', 901],
        ['b9-02-ley-1185-2008-arte', '9', 'B9-02', 'Ley 1185 de 2008', 'Ley', '2008-03-12', 'Régimen de patrimonio cultural.', 902],
        ['b9-03-decreto-1080-2015-arte', '9', 'B9-03', 'Decreto 1080 de 2015', 'Decreto', '2015-05-26', 'Decreto único del sector cultura.', 903],
        ['b9-04-ley-23-1982-derechos-autor', '9', 'B9-04', 'Ley 23 de 1982', 'Ley', '1982-01-28', 'Derechos de autor.', 904],
        ['b9-05-decision-andina-351-1993', '9', 'B9-05', 'Decisión Andina 351 de 1993', 'Decisión Andina', '1993-12-17', 'Régimen común de derecho de autor.', 905],
        ['b9-06-ley-1675-2013-patrimonio-sumergido', '9', 'B9-06', 'Ley 1675 de 2013', 'Ley', '2013-07-30', 'Aplica para patrimonio cultural sumergido.', 906],
        ['b9-07-actos-inventarios-patrimoniales', '9', 'B9-07', 'Actos de declaratoria o inclusión en inventarios patrimoniales', 'Acto administrativo', null, 'Documentos específicos de declaratoria o inventario patrimonial.', 907],
        ['b10-01-decreto-1071-2015-agropecuario', '10', 'B10-01', 'Decreto 1071 de 2015 - Sector Agropecuario', 'Decreto', '2015-05-26', 'Marco sectorial agropecuario.', 1001],
        ['b10-02-normativa-ica-especie', '10', 'B10-02', 'Normativa del ICA según especie', 'Reglamento técnico', null, 'Regulación sanitaria y técnica por especie.', 1002],
        ['b10-03-ley-914-2004-ganado-bovino', '10', 'B10-03', 'Ley 914 de 2004', 'Ley', '2004-10-21', 'Sistema Nacional de Identificación e Información de Ganado Bovino.', 1003],
        ['b10-04-decreto-2420-2015-activos-biologicos', '10', 'B10-04', 'Decreto 2420 de 2015', 'Decreto', '2015-12-14', 'Aplica para valoración contable de activos biológicos.', 1004],
    ];
    $upsert = $schema->db->getAttribute(\PDO::ATTR_DRIVER_NAME) === 'sqlite'
        ? "INSERT INTO valuation_legal_documents (slug, category_code, document_code, title, document_type,
        status, issued_at, source_reference, summary, sort_order, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, 'vigente', ?, 'Bibliografía B6-B10', ?, ?, ?, ?)
        ON CONFLICT(slug) DO UPDATE SET category_code = excluded.category_code,
        document_code = excluded.document_code, title = excluded.title, document_type = excluded.document_type,
        issued_at = excluded.issued_at, source_reference = excluded.source_reference,
        summary = excluded.summary, sort_order = excluded.sort_order, updated_at = excluded.updated_at"
        : "INSERT INTO valuation_legal_documents (slug, category_code, document_code, title, document_type,
        status, issued_at, source_reference, summary, sort_order, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, 'vigente', ?, 'Bibliografía B6-B10', ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE category_code = VALUES(category_code), document_code = VALUES(document_code),
        title = VALUES(title), document_type = VALUES(document_type), issued_at = VALUES(issued_at),
        source_reference = VALUES(source_reference), summary = VALUES(summary), sort_order = VALUES(sort_order),
        updated_at = VALUES(updated_at)";
    $query = $schema->db->prepare($upsert);
    foreach ($documents as $document) $query->execute([...$document, $now, $now]);
};
