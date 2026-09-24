<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $tableExists = static function (string $table) use ($schema): bool {
        $query = $schema->db->prepare('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?');
        $query->execute([$table]);
        return (int) $query->fetchColumn() > 0;
    };
    if ($tableExists('appraisal_urban_norm_profiles')) {
        foreach ([
            'cadastral_reference' => "VARCHAR(80) NOT NULL DEFAULT ''",
            'midas_query_option' => "VARCHAR(80) NOT NULL DEFAULT 'Uso del suelo'",
            'midas_usage_result' => 'TEXT NULL',
            'midas_activity' => "VARCHAR(180) NOT NULL DEFAULT ''",
            'midas_support_reference' => "VARCHAR(220) NOT NULL DEFAULT ''",
            'official_concept_scope' => 'TEXT NULL',
            'urban_norms_applied' => 'TEXT NULL',
            'heritage_context' => 'TEXT NULL',
            'environmental_context' => 'TEXT NULL',
            'risk_context' => 'TEXT NULL',
            'source_limitations' => 'TEXT NULL',
        ] as $column => $definition) $schema->addColumn('appraisal_urban_norm_profiles', $column, $definition);
    }
    if (!$tableExists('urban_norm_documents')) return;
    $now = gmdate('Y-m-d H:i:s');
    $docs = [
        ['midas-cartagena-uso-suelo', 'MIDAS Cartagena - consulta Uso del suelo', 'sistema_consulta', 'Secretaría de Planeación Distrital', 'Cartagena de Indias', 'Mapa Interactivo Digital de Asuntos del Suelo', null, 'consulta', 'MIDAS', 'https://midas.cartagena.gov.co/', '', '', 'Consulta predial de capas de suelo. El numeral 5 registra número predial, opción consultada y resultado leído.', 50],
        ['concepto-uso-suelo-planeacion-cartagena', 'Concepto de uso del suelo - Planeación Distrital', 'tramite', 'Secretaría de Planeación Distrital', 'Cartagena de Indias', 'Concepto de uso del suelo', null, 'tramite', 'Trámite oficial', 'https://tramites.cartagena.gov.co/tramites-y-servicios/concepto-de-uso-del-suelo', '', '', 'Dictamen escrito sobre usos permitidos conforme al POT y los instrumentos que lo desarrollen.', 60],
    ];
    $query = $schema->db->prepare('INSERT IGNORE INTO urban_norm_documents
        (slug, title, document_type, issuer, jurisdiction, normative_reference, issued_on, status,
        version_label, source_url, source_filename, storage_filename, summary, sort_order, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    foreach ($docs as $doc) $query->execute([...$doc, $now, $now]);
};