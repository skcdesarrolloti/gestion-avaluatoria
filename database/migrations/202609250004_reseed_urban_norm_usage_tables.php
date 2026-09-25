<?php
declare(strict_types=1);
use App\Database\Schema;

if (!function_exists('urbanNormResidentialParameters')) {
    function urbanNormResidentialParameters(): array
    {
        $base = [
            'res-a' => ['Residencial tipo A', '1 alcoba: 30 m²; 2 alcobas: 40 m²; 3 alcobas: 50 m².',
                'Unifamiliar 1 piso: de acuerdo con aislamientos. Unifamiliar 2 pisos y bifamiliar: 1 m² libre por cada 0.80 m² de área construida.',
                'Unifamiliar 1 piso: AML 120 m², frente 8 m. Unifamiliar 2 pisos: AML 90 m², frente 6 m. Bifamiliar: AML 200 m², frente 10 m.',
                '2 pisos.', 'Unifamiliar 1 piso: 0.6. Unifamiliar 2 pisos: 1.0. Bifamiliar: 1.1.',
                'Antejardín: 3 m según cuadro. Verificar retiros laterales y posteriores en el documento fuente.'],
            'res-b' => ['Residencial tipo B', '1 alcoba: 40 m²; 2 alcobas: 50 m²; 3 alcobas: 70 m².',
                '1 m² libre por cada 0.80 m² de área construida, según tipología aplicable.',
                'Unifamiliar 1 piso: AML 200 m², frente 8 m. Unifamiliar 2 pisos: AML 160 m², frente 8 m. Bifamiliar: AML 250 m², frente 10 m. Multifamiliar: AML 480 m², frente 16 m.',
                '4 pisos.', 'Unifamiliar 1 piso: 0.6. Unifamiliar 2 pisos: 1.0. Bifamiliar: 1.1. Multifamiliar: 1.2.',
                'Antejardín: unifamiliar 3 m sobre vías secundarias y 5 m sobre vías principales; verificar demás aislamientos en el cuadro.'],
            'res-c' => ['Residencial tipo C', '1 alcoba: 60 m²; 2 alcobas: 80 m²; 3 alcobas: 100 m².',
                '1 m² libre por cada 0.80 m² de área construida, según tipología aplicable.',
                'Unifamiliar 1 piso: AML 250 m², frente 10 m. Unifamiliar 2 pisos: AML 200 m², frente 8 m. Bifamiliar: AML 300 m², frente 10 m. Multifamiliar: AML 600 m², frente 20 m.',
                'Según área libre e índice de construcción.', 'Unifamiliar 1 piso: 0.6. Unifamiliar 2 pisos: 1.0. Bifamiliar: 1.2. Multifamiliar: 2.4.',
                'Antejardín: unifamiliar 7 m sobre vías secundarias y 9 m sobre vías principales; verificar demás aislamientos en el cuadro.'],
            'res-d' => ['Residencial tipo D', '1 alcoba: 60 m²; 2 alcobas: 80 m²; 3 alcobas: 100 m².',
                '1 m² libre por cada 0.80 m² de área construida, según tipología aplicable.',
                'Unifamiliar 1 piso: AML 360 m², frente 12 m. Unifamiliar 2 pisos: AML 200 m², frente 10 m. Bifamiliar: AML 300 m², frente 12 m. Multifamiliar: AML 750 m², frente 25 m.',
                'Según área libre e índice de construcción.', 'Unifamiliar 1 piso: 0.6. Unifamiliar 2 pisos: 1.0. Bifamiliar: 1.2. Multifamiliar: 2.4.',
                'Antejardín: unifamiliar 7 m sobre vías secundarias y 9 m sobre vías principales; verificar demás aislamientos en el cuadro.'],
        ];
        $labels = ['unidad_basica' => 'Unidad básica', 'area_libre' => 'Área libre',
            'area_frente_minimos' => 'Área y frente mínimos', 'altura_maxima' => 'Altura máxima',
            'indice_construccion' => 'Índice de construcción', 'aislamientos' => 'Aislamientos'];
        $rows = [];
        foreach ($base as $slug => $values) {
            $order = 1;
            foreach (array_keys($labels) as $index => $key) $rows[] = [$slug, $key, $labels[$key], $values[$index + 1], $order++];
        }
        return $rows;
    }
}

return static function (Schema $schema): void {
    $seed = require __DIR__ . '/202609240002_seed_decreto_0977_usage_tables.php';
    $seed($schema);
    $now = gmdate('Y-m-d H:i:s');
    $rows = urbanNormResidentialParameters();
    $slugs = array_values(array_unique(array_map(static fn (array $row): string => $row[0], $rows)));
    $marks = implode(', ', array_fill(0, count($slugs), '?'));
    $schema->db->prepare('DELETE FROM urban_norm_parameters WHERE category_slug IN (' . $marks . ')')->execute($slugs);
    $query = $schema->db->prepare('INSERT INTO urban_norm_parameters
        (category_slug, parameter_key, label, value_text, sort_order, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?)');
    foreach ($rows as $row) $query->execute([$row[0], $row[1], $row[2], $row[3], $row[4], $now, $now]);
};
