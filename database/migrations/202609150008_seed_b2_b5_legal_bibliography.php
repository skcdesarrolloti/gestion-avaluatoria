<?php declare(strict_types=1);

return static function (App\Database\Schema $schema): void {
    $now = gmdate('Y-m-d H:i:s');
    $documents = [
        ['b2-01-ley-160-1994', '2', 'B2-01', 'Ley 160 de 1994', 'Ley', '1994-08-03', 'Régimen de reforma agraria y propiedad rural, con modificaciones vigentes.', 201],
        ['b2-02-decreto-1071-2015', '2', 'B2-02', 'Decreto 1071 de 2015 - Sector Agropecuario, Pesquero y Desarrollo Rural', 'Decreto', '2015-05-26', 'Marco sectorial agropecuario, pesquero y de desarrollo rural.', 202],
        ['b2-03-resolucion-igac-941-2026', '2', 'B2-03', 'Resolución IGAC 941 de 2026', 'Resolución', null, 'Referencia IGAC principal indicada para inmuebles rurales.', 203],
        ['b2-04-resolucion-2965-1995-rurales', '2', 'B2-04', 'Resolución 2965 de 1995', 'Resolución', '1995-09-12', 'Procedimiento para avalúos comerciales de predios y mejoras rurales en reforma agraria.', 204],
        ['b2-05-ley-1448-2011-restitucion-tierras', '2', 'B2-05', 'Ley 1448 de 2011', 'Ley', '2011-06-10', 'Aplica cuando la finalidad se relacione con restitución de tierras.', 205],
        ['b2-06-resolucion-igac-1094-2016', '2', 'B2-06', 'Resolución IGAC 1094 de 2016', 'Resolución', '2016-10-10', 'Avalúos relacionados con restitución de tierras.', 206],
        ['b2-07-pot-pbot-eot-determinantes-rurales', '2', 'B2-07', 'POT/PBOT/EOT, determinantes ambientales y clasificación del suelo', 'Norma local', null, 'Clasificación del suelo rural, determinantes ambientales y ordenamiento territorial.', 207],
        ['b3-01-decreto-ley-2811-1974', '3', 'B3-01', 'Decreto-Ley 2811 de 1974', 'Decreto Ley', '1974-12-18', 'Código Nacional de Recursos Naturales.', 301],
        ['b3-02-ley-99-1993', '3', 'B3-02', 'Ley 99 de 1993', 'Ley', '1993-12-22', 'Sistema Nacional Ambiental.', 302],
        ['b3-03-decreto-1076-2015-ambiente', '3', 'B3-03', 'Decreto 1076 de 2015 - Sector Ambiente', 'Decreto', '2015-05-26', 'Decreto único del sector ambiente y desarrollo sostenible.', 303],
        ['b3-04-resolucion-1478-2003', '3', 'B3-04', 'Resolución 1478 de 2003', 'Resolución', '2003-12-18', 'Metodologías de valoración económica de bienes, servicios ambientales y recursos naturales.', 304],
        ['b3-05-resolucion-1669-2017', '3', 'B3-05', 'Resolución 1669 de 2017', 'Resolución', '2017-08-15', 'Herramientas económicas en licenciamiento ambiental.', 305],
        ['b3-06-resolucion-1084-2018', '3', 'B3-06', 'Resolución 1084 de 2018', 'Resolución', '2018-06-13', 'Valoración de costos económicos del deterioro y conservación ambiental.', 306],
        ['b3-07-ley-685-2001-codigo-minas', '3', 'B3-07', 'Ley 685 de 2001', 'Ley', '2001-08-15', 'Código de Minas; aplica cuando existan minas, yacimientos o derechos mineros.', 307],
        ['b3-08-pomca-areas-protegidas', '3', 'B3-08', 'POMCA, áreas protegidas, reservas, rondas, determinantes y licencias', 'Acto ambiental', null, 'Actos ambientales específicos aplicables al recurso o predio valorado.', 308],
        ['b4-01-ley-1682-2013-infraestructura', '4', 'B4-01', 'Ley 1682 de 2013', 'Ley', '2013-11-22', 'Infraestructura de transporte.', 401],
        ['b4-02-ley-1742-2014-infraestructura', '4', 'B4-02', 'Ley 1742 de 2014', 'Ley', '2014-12-26', 'Adquisición predial, expropiación e indemnizaciones para infraestructura.', 402],
        ['b4-03-resolucion-igac-898-2014', '4', 'B4-03', 'Resolución IGAC 898 de 2014', 'Resolución', '2014-08-19', 'Avalúos comerciales para proyectos de infraestructura de transporte; revisar apartes derogados.', 403],
        ['b4-04-resolucion-igac-1044-2014', '4', 'B4-04', 'Resolución IGAC 1044 de 2014', 'Resolución', '2014-09-29', 'Modifica y adiciona la Resolución IGAC 898 de 2014.', 404],
        ['b4-05-resolucion-igac-941-2026', '4', 'B4-05', 'Resolución IGAC 941 de 2026', 'Resolución', null, 'Referencia IGAC principal indicada para infraestructura.', 405],
        ['b4-06-ley-56-1981-servicios-obras', '4', 'B4-06', 'Ley 56 de 1981', 'Ley', '1981-09-01', 'Generación, transmisión eléctrica, acueductos, riego, expropiaciones y servidumbres.', 406],
        ['b4-07-ley-142-1994-servicios-publicos', '4', 'B4-07', 'Ley 142 de 1994', 'Ley', '1994-07-11', 'Aplica cuando intervengan redes o servidumbres de servicios públicos.', 407],
        ['b4-08-decreto-1079-2015-transporte', '4', 'B4-08', 'Decreto 1079 de 2015 - Sector Transporte', 'Decreto', '2015-05-26', 'Marco sectorial de transporte.', 408],
        ['b5-01-ley-397-1997-cultura', '5', 'B5-01', 'Ley 397 de 1997', 'Ley', '1997-08-07', 'Ley General de Cultura.', 501],
        ['b5-02-ley-1185-2008-patrimonio', '5', 'B5-02', 'Ley 1185 de 2008', 'Ley', '2008-03-12', 'Modifica y fortalece el régimen de patrimonio cultural.', 502],
        ['b5-03-decreto-1080-2015-cultura', '5', 'B5-03', 'Decreto 1080 de 2015 - Sector Cultura', 'Decreto', '2015-05-26', 'Decreto único del sector cultura.', 503],
        ['b5-04-ley-163-1959-patrimonio', '5', 'B5-04', 'Ley 163 de 1959', 'Ley', '1959-12-30', 'Patrimonio histórico y artístico, en lo que conserve vigencia.', 504],
        ['b5-05-decreto-264-1963', '5', 'B5-05', 'Decreto 264 de 1963', 'Decreto', '1963-02-12', 'Disposiciones aplicables sobre patrimonio, cuando conserven vigencia.', 505],
        ['b5-06-resolucion-igac-941-2026', '5', 'B5-06', 'Resolución IGAC 941 de 2026', 'Resolución', null, 'Aplica para el componente inmobiliario patrimonial.', 506],
        ['b5-07-acto-declaratoria-bic', '5', 'B5-07', 'Acto particular de declaratoria del BIC', 'Acto administrativo', null, 'Documento obligatorio del expediente cuando exista declaratoria BIC.', 507],
        ['b5-08-pemp', '5', 'B5-08', 'PEMP - Plan Especial de Manejo y Protección', 'Instrumento patrimonial', null, 'Documento obligatorio cuando exista PEMP.', 508],
        ['b5-09-pot-conservacion', '5', 'B5-09', 'POT y tratamiento urbanístico de conservación', 'Norma local', null, 'Tratamiento urbanístico de conservación aplicable al inmueble.', 509],
    ];
    $upsert = $schema->db->getAttribute(\PDO::ATTR_DRIVER_NAME) === 'sqlite'
        ? "INSERT INTO valuation_legal_documents (slug, category_code, document_code, title, document_type,
        status, issued_at, source_reference, summary, sort_order, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, 'vigente', ?, 'Bibliografía B2-B5', ?, ?, ?, ?)
        ON CONFLICT(slug) DO UPDATE SET category_code = excluded.category_code,
        document_code = excluded.document_code, title = excluded.title, document_type = excluded.document_type,
        issued_at = excluded.issued_at, source_reference = excluded.source_reference,
        summary = excluded.summary, sort_order = excluded.sort_order, updated_at = excluded.updated_at"
        : "INSERT INTO valuation_legal_documents (slug, category_code, document_code, title, document_type,
        status, issued_at, source_reference, summary, sort_order, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, 'vigente', ?, 'Bibliografía B2-B5', ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE category_code = VALUES(category_code), document_code = VALUES(document_code),
        title = VALUES(title), document_type = VALUES(document_type), issued_at = VALUES(issued_at),
        source_reference = VALUES(source_reference), summary = VALUES(summary), sort_order = VALUES(sort_order),
        updated_at = VALUES(updated_at)";
    $query = $schema->db->prepare($upsert);
    foreach ($documents as $document) $query->execute([...$document, $now, $now]);
};
