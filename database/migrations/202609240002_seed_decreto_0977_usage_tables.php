<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $now = gmdate('Y-m-d H:i:s');
    $doc = ['pot-0977-cuadros-uso', 'Cuadros de reglamentación de usos del Decreto 0977 de 2001',
        'cuadro_uso', 'Alcaldía Mayor de Cartagena de Indias', 'Cartagena de Indias',
        'Decreto 0977 de 2001', '2001-11-20', 'vigente', 'POT 2001', '',
        'pdf_descargas_pot2001_decreto_0977_2001_cuadro_uso_250519_140954.pdf',
        'pot-0977-cuadros-uso.pdf', 'Cuadros de usos urbanos, expansión, suburbanos y rurales.', 10];
    $query = $schema->db->prepare('INSERT INTO urban_norm_documents
        (slug, title, document_type, issuer, jurisdiction, normative_reference, issued_on, status,
        version_label, source_url, source_filename, storage_filename, summary, sort_order, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE title = VALUES(title), document_type = VALUES(document_type),
        issuer = VALUES(issuer), jurisdiction = VALUES(jurisdiction), normative_reference = VALUES(normative_reference),
        issued_on = VALUES(issued_on), status = VALUES(status), version_label = VALUES(version_label),
        source_url = VALUES(source_url), source_filename = VALUES(source_filename),
        storage_filename = VALUES(storage_filename), summary = VALUES(summary), sort_order = VALUES(sort_order),
        updated_at = VALUES(updated_at)');
    $query->execute([...$doc, $now, $now]);
    $tables = [
        ['pot-0977-cuadro-1-residencial', 'Cuadro No. 1', 'Actividad residencial', 'Suelo urbano y expansión', 1, 1, 10],
        ['pot-0977-cuadro-2-institucional', 'Cuadro No. 2', 'Actividad institucional', 'Suelo urbano y expansión', 2, 4, 20],
        ['pot-0977-cuadro-3-comercial', 'Cuadro No. 3', 'Actividad comercial', 'Suelo urbano y expansión', 5, 7, 30],
        ['pot-0977-cuadro-4-industrial', 'Cuadro No. 4', 'Actividad industrial', 'Suelo urbano y expansión', 8, 9, 40],
        ['pot-0977-cuadro-5-turistica', 'Cuadro No. 5', 'Actividad turística Bocagrande y La Boquilla', 'Zonas turísticas', 10, 10, 50],
        ['pot-0977-cuadro-6-portuaria', 'Cuadro No. 6', 'Actividad portuaria', 'Suelo urbano', 11, 11, 60],
        ['pot-0977-cuadro-7-mixta', 'Cuadro No. 7', 'Actividad mixta', 'Suelo urbano y expansión', 12, 12, 70],
        ['pot-0977-cuadro-8-rural-suburbano', 'Cuadro No. 8', 'Áreas de actividad en suelo rural suburbano', 'Zona Norte y Barú', 13, 13, 80],
        ['pot-0977-cuadro-9-rural', 'Cuadro No. 9', 'Áreas de actividad localizadas en suelo rural', 'Suelo rural', 14, 14, 90],
    ];
    $tableQuery = $schema->db->prepare('INSERT INTO urban_norm_tables
        (slug, document_slug, table_code, title, scope, page_start, page_end, sort_order, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE table_code = VALUES(table_code), title = VALUES(title), scope = VALUES(scope),
        page_start = VALUES(page_start), page_end = VALUES(page_end), sort_order = VALUES(sort_order),
        updated_at = VALUES(updated_at)');
    foreach ($tables as $table) $tableQuery->execute([$table[0], $doc[0], ...array_slice($table, 1), $now, $now]);
    $categories = [
        ['res-a', 'pot-0977-cuadro-1-residencial', 'Residencial A', 'Residencial tipo A', 'residencial', 1],
        ['res-b', 'pot-0977-cuadro-1-residencial', 'Residencial B', 'Residencial tipo B', 'residencial', 2],
        ['res-c', 'pot-0977-cuadro-1-residencial', 'Residencial C', 'Residencial tipo C', 'residencial', 3],
        ['res-d', 'pot-0977-cuadro-1-residencial', 'Residencial D', 'Residencial tipo D', 'residencial', 4],
        ['inst-1', 'pot-0977-cuadro-2-institucional', 'Institucional 1', 'Institucional 1', 'institucional', 11],
        ['inst-2', 'pot-0977-cuadro-2-institucional', 'Institucional 2', 'Institucional 2', 'institucional', 12],
        ['inst-3', 'pot-0977-cuadro-2-institucional', 'Institucional 3', 'Institucional 3', 'institucional', 13],
        ['inst-4', 'pot-0977-cuadro-2-institucional', 'Institucional 4', 'Institucional 4', 'institucional', 14],
        ['com-1', 'pot-0977-cuadro-3-comercial', 'Comercial 1', 'Comercial 1', 'comercial', 21],
        ['com-2', 'pot-0977-cuadro-3-comercial', 'Comercial 2', 'Comercial 2', 'comercial', 22],
        ['com-3', 'pot-0977-cuadro-3-comercial', 'Comercial 3', 'Comercial 3', 'comercial', 23],
        ['com-4', 'pot-0977-cuadro-3-comercial', 'Comercial 4', 'Comercial 4', 'comercial', 24],
        ['ind-1', 'pot-0977-cuadro-4-industrial', 'Industrial 1', 'Industrial 1', 'industrial', 31],
        ['ind-2', 'pot-0977-cuadro-4-industrial', 'Industrial 2', 'Industrial 2', 'industrial', 32],
        ['ind-3', 'pot-0977-cuadro-4-industrial', 'Industrial 3', 'Industrial 3', 'industrial', 33],
        ['tur-bocagrande-boquilla', 'pot-0977-cuadro-5-turistica', 'Turístico', 'Turística Bocagrande y La Boquilla', 'turistica', 41],
        ['port-1', 'pot-0977-cuadro-6-portuaria', 'Portuario 1', 'Portuario 1', 'portuaria', 51],
        ['port-2', 'pot-0977-cuadro-6-portuaria', 'Portuario 2', 'Portuario 2', 'portuaria', 52],
        ['port-3', 'pot-0977-cuadro-6-portuaria', 'Portuario 3', 'Portuario 3', 'portuaria', 53],
        ['port-4', 'pot-0977-cuadro-6-portuaria', 'Portuario 4', 'Portuario 4', 'portuaria', 54],
        ['mixto-1', 'pot-0977-cuadro-7-mixta', 'Mixto 1', 'Actividad mixta 1', 'mixta', 61],
        ['mixto-2', 'pot-0977-cuadro-7-mixta', 'Mixto 2', 'Actividad mixta 2', 'mixta', 62],
        ['mixto-3', 'pot-0977-cuadro-7-mixta', 'Mixto 3', 'Actividad mixta 3', 'mixta', 63],
        ['mixto-4', 'pot-0977-cuadro-7-mixta', 'Mixto 4', 'Actividad mixta 4', 'mixta', 64],
        ['mixto-5', 'pot-0977-cuadro-7-mixta', 'Mixto 5', 'Actividad mixta 5', 'mixta', 65],
        ['rural-suburbano-turistico', 'pot-0977-cuadro-8-rural-suburbano', 'Rural suburbano turístico', 'Desarrollo turístico Zona Norte y Barú', 'rural_suburbano', 71],
        ['rural-parcelaciones', 'pot-0977-cuadro-9-rural', 'Parcelaciones', 'Parcelaciones rurales', 'rural', 81],
        ['rural-agroindustrial', 'pot-0977-cuadro-9-rural', 'Agroindustrial', 'Actividad productora agroindustrial', 'rural', 82],
    ];
    $categoryQuery = $schema->db->prepare('INSERT INTO urban_norm_use_categories
        (slug, table_slug, code, name, activity_group, sort_order, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE table_slug = VALUES(table_slug),
        code = VALUES(code), name = VALUES(name), activity_group = VALUES(activity_group),
        sort_order = VALUES(sort_order), updated_at = VALUES(updated_at)');
    foreach ($categories as $category) $categoryQuery->execute([$category[0], $category[1], $category[2], $category[3], $category[4], $category[5], $now, $now]);
    $categorySlugs = array_map(static fn (array $category): string => $category[0], $categories);
    $marks = implode(', ', array_fill(0, count($categorySlugs), '?'));
    $delete = $schema->db->prepare('DELETE FROM urban_norm_use_rules WHERE category_slug IN (' . $marks . ')');
    $delete->execute($categorySlugs);
    $ruleQuery = $schema->db->prepare('INSERT INTO urban_norm_use_rules
        (category_slug, rule_type, content, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)');
    foreach (urbanNormSeedRules() as $slug => $rules) {
        $order = 1;
        foreach ($rules as $type => $content) $ruleQuery->execute([$slug, $type, $content, $order++, $now, $now]);
    }
};

if (!function_exists('urbanNormSeedRules')) {
    function urbanNormSeedRules(): array
    {
    $resProhibido = 'Comercio 3 y 4; Industrial 2 y 3; Turístico; Portuario 2, 3 y 4; Institucional 3 y 4.';
    return [
        'res-a' => ['principal' => 'Residencial. Vivienda unifamiliar y bifamiliar.', 'compatible' => 'Comercio 1; Industrial 1.', 'complementario' => 'Institucional 1 y 2; Portuario 1.', 'restringido' => 'Comercial 2.', 'prohibido' => $resProhibido],
        'res-b' => ['principal' => 'Residencial. Vivienda unifamiliar, bifamiliar y multifamiliar.', 'compatible' => 'Comercio 1; Industrial 1.', 'complementario' => 'Institucional 1 y 2; Portuario 1.', 'restringido' => 'Comercio 2.', 'prohibido' => $resProhibido],
        'res-c' => ['principal' => 'Residencial. Vivienda unifamiliar, bifamiliar y multifamiliar.', 'compatible' => 'Comercio 1; Industrial 1.', 'complementario' => 'Institucional 1 y 2; Portuario 1 solo embarcaderos.', 'restringido' => 'Comercio 2.', 'prohibido' => $resProhibido],
        'res-d' => ['principal' => 'Residencial. Vivienda unifamiliar, bifamiliar y multifamiliar.', 'compatible' => 'Comercio 1; Industrial 1.', 'complementario' => 'Institucional 1 y 2; Portuario 1 solo embarcaderos.', 'restringido' => 'Comercio 2.', 'prohibido' => $resProhibido],
        'inst-1' => ['principal' => 'Institucional 1.', 'compatible' => 'Residencial; Turístico.', 'complementario' => 'Comercial 1; Portuario 1.', 'restringido' => 'Comercial 2; Industrial 1; Portuario 2.', 'prohibido' => 'Comercial 3 y 4; Portuario 3 y 4; Industrial 2 y 3.'],
        'inst-2' => ['principal' => 'Institucional 2.', 'compatible' => 'Institucional 1; Residencial; Turístico.', 'complementario' => 'Comercial 1 y 2; Industrial 1; Portuario 1.', 'restringido' => 'Industrial 2; Portuario 2.', 'prohibido' => 'Comercial 3 y 4; Portuario 3 y 4; Industrial 3.'],
        'inst-3' => ['principal' => 'Institucional 3.', 'compatible' => 'Institucional 1 y 2; Turístico.', 'complementario' => 'Comercial 1 y 2; Industrial 1; Portuario 1.', 'restringido' => 'Industrial 2; Portuario 2.', 'prohibido' => 'Comercial 3 y 4; Portuario 3 y 4; Industrial 3; Residencial.'],
        'inst-4' => ['principal' => 'Institucional 4.', 'compatible' => 'Institucional 1, 2 y 3.', 'complementario' => 'Portuario 1 y 2.', 'restringido' => 'Comercial 1, 2, 3 y 4; Industrial 3; Portuario 3 y 4.', 'prohibido' => 'Residencial; Turístico.'],
        'com-1' => ['principal' => 'Comercial 1.', 'compatible' => 'Residencial; Turístico; Industrial 1.', 'complementario' => 'Institucional 1; Portuario 1.', 'restringido' => 'Industrial 2; Institucional 2.', 'prohibido' => 'Industrial 3; Institucional 3 y 4; Portuario 2, 3 y 4.'],
        'com-2' => ['principal' => 'Comercial 2.', 'compatible' => 'Comercial 1; Industrial 1.', 'complementario' => 'Institucional 1 y 2; Portuario 1.', 'restringido' => 'Industrial 2; Institucional 3; Residencial; Turístico.', 'prohibido' => 'Industrial 3; Institucional 4; Portuario 2, 3 y 4.'],
        'com-3' => ['principal' => 'Comercial 3.', 'compatible' => 'Portuario 1, 2, 3 y 4.', 'complementario' => 'Industrial 2 y 3; Institucional 3 y 4.', 'restringido' => 'Institucional 1 y 2; Comercio 2.', 'prohibido' => 'Residencial; Industrial 1; Turístico; Comercio 1.'],
        'com-4' => ['principal' => 'Comercial 4.', 'compatible' => 'Portuario 2; Industrial 2; Comercio 3.', 'complementario' => 'Institucional 1 y 2; Portuario 1; Comercio 2.', 'restringido' => 'Portuario 3 y 4; Institucional 3 y 4.', 'prohibido' => 'Residencial; Industrial 3; Turismo; Comercio 1.'],
        'ind-1' => ['principal' => 'Industrial 1.', 'compatible' => 'Comercial 1 y 2.', 'complementario' => 'Institucional 1; Portuario 1; Residencial; Turístico.', 'restringido' => 'Ninguno.', 'prohibido' => 'Comercial 3 y 4; Institucional 2, 3 y 4; Portuario 2, 3 y 4.'],
        'ind-2' => ['principal' => 'Industrial 2.', 'compatible' => 'Industrial 1; Comercial 1, 2 y 3; Portuario 1 y 2.', 'complementario' => 'Institucional 1, 2 y 3.', 'restringido' => 'Comercial 4; Institucional 4.', 'prohibido' => 'Residencial; Turístico.'],
        'ind-3' => ['principal' => 'Industrial 3.', 'compatible' => 'Industrial 2; Comercial 3 y 4; Institucional 2, 3 y 4.', 'complementario' => 'Comercial 2; Portuario 3.', 'restringido' => 'Industrial 1; Portuario 1 y 2.', 'prohibido' => 'Residencial; Turístico; Comercio 1.'],
        'tur-bocagrande-boquilla' => ['principal' => 'Turístico.', 'compatible' => 'Comercial 1 y 2; Industrial 1; Institucional 1; Portuario 1; Residencial.', 'complementario' => 'Institucional 2.', 'restringido' => 'Institucional 3.', 'prohibido' => 'Industrial 2 y 3; Portuario 2, 3 y 4; Comercial 3 y 4.'],
        'port-1' => ['principal' => 'Portuario 1.', 'compatible' => 'Comercial 1 y 2; Industrial 1; Institucional 1.', 'complementario' => 'Residencial; Institucional 2; Turístico.', 'restringido' => 'Portuario 2 y 4.', 'prohibido' => 'Industrial 2 y 3; Portuario 3; Comercial 3 y 4.'],
        'port-2' => ['principal' => 'Portuario 2.', 'compatible' => 'Portuario 1; Industrial 1 y 2; Institucional 1 y 2.', 'complementario' => 'Institucional 3; Turístico; Comercial 2.', 'restringido' => 'Institucional 4; Portuario 4; Comercial 3.', 'prohibido' => 'Comercial 1; Industrial 3; Residencial.'],
        'port-3' => ['principal' => 'Portuario 3.', 'compatible' => 'Comercial 3; Portuario 2.', 'complementario' => 'Institucional 3; Comercial 4; Industrial 3.', 'restringido' => 'Institucional 4; Portuario 1 y 4; Industrial 2.', 'prohibido' => 'Comercial 1 y 2; Residencial; Industrial 1; Institucional 1 y 2; Turístico.'],
        'port-4' => ['principal' => 'Portuario 4.', 'compatible' => 'Comercial 3; Institucional 1, 2 y 3; Industrial 2.', 'complementario' => 'Comercial 2.', 'restringido' => 'Institucional 4; Portuario 1, 2 y 3; Comercial 1.', 'prohibido' => 'Residencial; Industrial 3; Turístico.'],
        'mixto-1' => ['principal' => 'Residencial y Comercio 1.', 'compatible' => 'Comercial 2; Industrial 1; Portuaria 1; Institucional 1 y 2; Turístico.', 'complementario' => 'Institucional 1.', 'restringido' => 'Institucional 2; Portuario 2.', 'prohibido' => 'Comercial 3 y 4; Industrial 2 y 3; Portuario 3 y 4.'],
        'mixto-2' => ['principal' => 'Institucional 3; Comercial 2.', 'compatible' => 'Comercial 1; Industrial 1; Portuario 1 y 2; Institucional 1 y 2; Turístico; Residencial.', 'complementario' => 'Institucional 3; Portuario 4.', 'restringido' => 'Institucional 4; Comercio 3.', 'prohibido' => 'Industrial 2 y 3; Portuario 3; Comercial 4.'],
        'mixto-3' => ['principal' => 'Comercial 1 y 2; Institucional 2.', 'compatible' => 'Portuario 1.', 'complementario' => 'Industrial 1; Institucional 1.', 'restringido' => 'Institucional 3; Residencial.', 'prohibido' => 'Industrial 2 y 3; Institucional 4; Portuario 2, 3 y 4; Comercial 3 y 4.'],
        'mixto-4' => ['principal' => 'Comercial 3; Industrial 2.', 'compatible' => 'Portuario 1 y 2.', 'complementario' => 'Comercial 2.', 'restringido' => 'Comercial 4; Portuario 3 y 4.', 'prohibido' => 'Residencial; Turístico.'],
        'mixto-5' => ['principal' => 'Industrial 3; Comercial 3; Portuario 3.', 'compatible' => 'Institucional 3; Transporte.', 'complementario' => 'Portuario 1 y 2; Comercial 4.', 'restringido' => 'Institucional 4; Portuario 3 y 4.', 'prohibido' => 'Residencial; Turístico; Comercial 1.'],
        'rural-suburbano-turistico' => ['principal' => 'Turística; Residencial; Vivienda temporal.', 'compatible' => 'Comercial 1 y 2; Industrial 1; Portuario 1; Agroindustrial.', 'complementario' => 'Institucional 1 y 2.', 'restringido' => 'Institucional 4: Jardines cementerios.', 'prohibido' => 'Comercial 3 y 4; Industrial 3; Portuario 2, 3 y 4.'],
        'rural-parcelaciones' => ['principal' => 'Residencial unifamiliar.', 'compatible' => 'Institucional 4; Jardines cementerio.', 'complementario' => 'Vivienda unifamiliar; celaduría; servicios comunales.', 'restringido' => '', 'prohibido' => 'No definido en el extracto inicial; requiere revisión del cuadro.'],
        'rural-agroindustrial' => ['principal' => 'Labores agrícolas, horticultura, floricultura, pastos, ganadería, aves, apiarios, acuicultura, explotaciones forestales y camaroneras.', 'compatible' => 'Vivienda unifamiliar; bodegas; silos; establos. Intensidad compatible 75% del uso principal.', 'complementario' => 'Portuario 1; bodegas; silos; establos; galpones; plantas de procesamiento; estanques artificiales.', 'restringido' => '', 'prohibido' => 'No definido en el extracto inicial; requiere revisión del cuadro.'],
    ];
}
}
