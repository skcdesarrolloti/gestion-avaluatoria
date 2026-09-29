<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $now = gmdate('Y-m-d H:i:s');
    $query = $schema->db->prepare("INSERT INTO valuation_ifrs_standards
        (slug, group_code, standard_code, title, applicable_categories, measurement_focus,
        summary, field_relevance, source_reference, status, sort_order, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'vigente', ?, ?, ?)
        ON DUPLICATE KEY UPDATE group_code = VALUES(group_code), standard_code = VALUES(standard_code),
        title = VALUES(title), applicable_categories = VALUES(applicable_categories),
        measurement_focus = VALUES(measurement_focus), summary = VALUES(summary),
        field_relevance = VALUES(field_relevance), source_reference = VALUES(source_reference),
        status = VALUES(status), sort_order = VALUES(sort_order), updated_at = VALUES(updated_at)");
    $query->execute([
        'academia-niif-valor-razonable',
        'G',
        'Academia NIIF',
        'Valor razonable y jerarquía de datos',
        'A,1,2,3,4,5,6,7,8,9,10,11,12,13',
        'Jerarquía NIIF 13: Nivel 1, Nivel 2 y Nivel 3',
        'Ficha académica para explicar valor razonable, activo medido, datos observables, datos no observables y revelaciones del entregable.',
        'Permite subir la academia sin reemplazar el PDF fuente de NIIF 13 / IFRS 13.',
        'Academia interna SuCasa basada en NIIF 13 / IFRS 13 y referencias IFRS Foundation',
        2,
        $now,
        $now,
    ]);
};
