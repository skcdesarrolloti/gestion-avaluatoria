<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $now = gmdate('Y-m-d H:i:s');
    $docs = [
        ['ley-388-1997-ordenamiento-territorial', 'Ley 388 de 1997', 'ley', 'Congreso de Colombia', 'Colombia', 'Ley 388 de 1997', '1997-07-18', 'vigente', 'Ordenamiento territorial', '', '', '', 'Base nacional de ordenamiento territorial, POT, usos del suelo e instrumentos de gestión.', 20],
        ['decreto-1077-2015-sector-vivienda', 'Decreto 1077 de 2015', 'decreto', 'Gobierno Nacional', 'Colombia', 'Decreto 1077 de 2015', '2015-05-26', 'vigente', 'Sector Vivienda', '', '', '', 'Decreto único reglamentario del sector vivienda, ciudad y territorio; licencias, usos y desarrollo urbano.', 30],
        ['pot-cartagena-decreto-0977-2001', 'POT Cartagena - Decreto 0977 de 2001', 'pot', 'Alcaldía Mayor de Cartagena de Indias', 'Cartagena de Indias', 'Decreto 0977 de 2001', '2001-11-20', 'vigente', 'POT 2001', 'https://vuc.cartagena.gov.co/documentos/normatividad/DECRETO_0977_de_2001.pdf', '', '', 'Adopta el POT de Cartagena; contiene estructura, clasificación, tratamientos, planos y reglamentación urbana.', 40],
        ['midas-cartagena-uso-suelo', 'MIDAS Cartagena - consulta Uso del suelo', 'sistema_consulta', 'Secretaría de Planeación Distrital', 'Cartagena de Indias', 'Mapa Interactivo Digital de Asuntos del Suelo', null, 'consulta', 'MIDAS', 'https://midas.cartagena.gov.co/', '', '', 'Consulta predial de capas de suelo. El numeral 5 debe registrar número predial, opción consultada y resultado leído.', 50],
        ['concepto-uso-suelo-planeacion-cartagena', 'Concepto de uso del suelo - Planeación Distrital', 'tramite', 'Secretaría de Planeación Distrital', 'Cartagena de Indias', 'Concepto de uso del suelo', null, 'tramite', 'Trámite oficial', 'https://tramites.cartagena.gov.co/tramites-y-servicios/concepto-de-uso-del-suelo', '', '', 'Dictamen escrito sobre uso o usos permitidos conforme al POT y los instrumentos que lo desarrollen.', 60],
        ['resolucion-043-1994-centro-historico', 'Resolución 043 de 1994 - Centro Histórico', 'resolucion', 'Autoridad patrimonial competente', 'Cartagena de Indias', 'Resolución 043 de 1994', '1994-01-01', 'verificar', 'Patrimonio', '', '', '', 'Ruta de revisión para Centro Histórico, áreas de influencia, periferia histórica y bienes de interés cultural.', 70],
        ['determinantes-ambientales-riesgo', 'Determinantes ambientales, protección y riesgo', 'determinante', 'Autoridad ambiental o distrital competente', 'Cartagena de Indias', 'POT, cartografía, conceptos o determinantes aplicables', null, 'verificar', 'Ambiente y riesgo', '', '', '', 'Rondas, protección, amenazas, riesgo, restricciones ambientales o condiciones que limiten el uso o desarrollo.', 80],
    ];
    $query = $schema->db->prepare('INSERT INTO urban_norm_documents
        (slug, title, document_type, issuer, jurisdiction, normative_reference, issued_on, status,
        version_label, source_url, source_filename, storage_filename, summary, sort_order, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE title = VALUES(title), document_type = VALUES(document_type),
        issuer = VALUES(issuer), jurisdiction = VALUES(jurisdiction), normative_reference = VALUES(normative_reference),
        issued_on = VALUES(issued_on), status = VALUES(status), version_label = VALUES(version_label),
        source_url = VALUES(source_url), summary = VALUES(summary), sort_order = VALUES(sort_order),
        updated_at = VALUES(updated_at)');
    foreach ($docs as $doc) $query->execute([...$doc, $now, $now]);
};
