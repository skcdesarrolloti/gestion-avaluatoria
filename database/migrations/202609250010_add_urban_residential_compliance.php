<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('appraisal_urban_norm_profiles', 'lot_front_normative_m', 'DECIMAL(10,2) NULL AFTER land_area_normative_m2');
    $schema->addColumn('appraisal_urban_norm_profiles', 'normative_modality', "VARCHAR(80) NOT NULL DEFAULT '' AFTER lot_front_normative_m");
    $schema->addColumn('appraisal_urban_norm_profiles', 'normative_compliance_summary', 'TEXT NULL AFTER constructive_potential_notes');
    $now = gmdate('Y-m-d H:i:s');
    $rows = [
        ['res-a','modalidades_evaluables','Modalidades evaluables','Unifamiliar 1 piso: AML 120 m2, F 8 m, IC 0.6. Unifamiliar 2 pisos: AML 90 m2, F 6 m, IC 1.0. Bifamiliar: AML 200 m2, F 10 m, IC 1.1.',7],
        ['res-a','estacionamientos','Estacionamientos','1 por cada 10 viviendas.',8],
        ['res-a','nivel_piso','Nivel de piso','Lotes sin inclinación: 0.30 m de la rasante en el eje de la vía.',9],
        ['res-b','modalidades_evaluables','Modalidades evaluables','Unifamiliar 1 piso: AML 200 m2, F 8 m, IC 0.6. Unifamiliar 2 pisos: AML 160 m2, F 8 m, IC 1.0. Bifamiliar: AML 250 m2, F 10 m, IC 1.1. Multifamiliar: AML 480 m2, F 16 m, IC 1.2.',7],
        ['res-b','estacionamientos','Estacionamientos','Unifamiliar y bifamiliar: 1 cupo por cada 100 m2 de área construida. Multifamiliar: 1 cupo por cada 70 m2 y visitantes 1 por cada 210 m2.',8],
        ['res-b','nivel_piso','Nivel de piso','Lotes sin inclinación: 0.30 m de la rasante en el eje de la vía.',9],
        ['res-c','modalidades_evaluables','Modalidades evaluables','Unifamiliar 1 piso: AML 250 m2, F 10 m, IC 0.6. Unifamiliar 2 pisos: AML 200 m2, F 8 m, IC 1.0. Bifamiliar: AML 300 m2, F 10 m, IC 1.2. Multifamiliar: AML 600 m2, F 20 m, IC 2.4.',7],
        ['res-c','estacionamientos','Estacionamientos','Unifamiliar y bifamiliar: 1 cupo por cada 100 m2 de área construida. Multifamiliar: 1 cupo por cada 100 m2 y visitantes 1 por cada 400 m2.',8],
        ['res-c','nivel_piso','Nivel de piso','Lotes sin inclinación: 0.30 m de la rasante en el eje de la vía.',9],
        ['res-d','modalidades_evaluables','Modalidades evaluables','Unifamiliar 1 piso: AML 360 m2, F 12 m, IC 0.6. Unifamiliar 2 pisos: AML 200 m2, F 10 m, IC 1.0. Bifamiliar: AML 300 m2, F 12 m, IC 1.2. Multifamiliar: AML 750 m2, F 25 m, IC 2.4.',7],
        ['res-d','estacionamientos','Estacionamientos','Unifamiliar y bifamiliar: 1 cupo por cada 100 m2 de área construida. Multifamiliar: 1 cupo por cada 100 m2 y visitantes 1 por cada 400 m2.',8],
        ['res-d','nivel_piso','Nivel de piso','Lotes sin inclinación: 0.30 m de la rasante en el eje de la vía.',9],
    ];
    $schema->db->prepare("DELETE FROM urban_norm_parameters WHERE category_slug IN ('res-a','res-b','res-c','res-d') AND parameter_key IN ('modalidades_evaluables','estacionamientos','nivel_piso')")->execute();
    $q = $schema->db->prepare('INSERT INTO urban_norm_parameters (category_slug, parameter_key, label, value_text, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)');
    foreach ($rows as $row) $q->execute([$row[0], $row[1], $row[2], $row[3], $row[4], $now, $now]);
};
