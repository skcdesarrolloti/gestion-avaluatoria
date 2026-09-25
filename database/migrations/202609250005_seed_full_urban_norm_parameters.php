<?php
declare(strict_types=1);
use App\Database\Schema;

if (!function_exists('urbanNormFullPotentialParameters')) {
    function urbanNormFullPotentialParameters(): array
    {
        $rows = [];
        $add = static function (string $slug, array $params) use (&$rows): void {
            $labels = [
                'unidad_basica' => 'Unidad básica', 'area_libre' => 'Área libre',
                'area_frente_minimos' => 'Área y frente mínimos', 'altura_maxima' => 'Altura máxima',
                'indice_construccion' => 'Índice / área de construcción', 'aislamientos' => 'Aislamientos',
                'area_ocupacion' => 'Área de ocupación', 'estacionamientos' => 'Estacionamientos',
                'normas_comunes' => 'Normas comunes', 'intensidad_uso' => 'Intensidad del uso',
                'tipo_establecimiento' => 'Tipo de establecimiento', 'condiciones_especiales' => 'Condiciones especiales',
            ];
            $order = 1;
            foreach ($params as $key => $value) $rows[] = [$slug, $key, $labels[$key] ?? $key, $value, $order++];
        };
        $instFronts = ['inst-1' => 'Frente mínimo: 12 m.', 'inst-2' => 'Frente mínimo: 20 m.',
            'inst-3' => 'Frente mínimo: 30 m.', 'inst-4' => 'Frente mínimo: 50 m.'];
        foreach ($instFronts as $slug => $front) $add($slug, [
            'tipo_establecimiento' => 'El lote lo determina el tamaño del establecimiento y el servicio. El uso no se ubica dentro de vivienda; puede requerir englobe de predios según categoría.',
            'area_frente_minimos' => $front,
            'altura_maxima' => 'Según aplicación del área libre e índice de construcción. Para altura superior a 4 pisos, la edificación debe dotarse de ascensor.',
            'indice_construccion' => '1 piso: 0.6. Verificar tabla para pisos superiores y condiciones del establecimiento.',
            'estacionamientos' => 'Lote autosuficiente dentro de sus linderos, incluidos parqueos para empleados y visitantes. Tarifas por actividad asistencial, educativa, administrativa, cultural, seguridad, culto o recreativa según cuadro.',
        ]);
        $add('com-1', ['tipo_establecimiento' => 'Comercio detallista de artículos y servicios de primera necesidad, bajo impacto y compatible con uso residencial.',
            'area_libre' => 'Rigen los indicadores de las áreas residenciales de la cual forma parte.',
            'area_frente_minimos' => 'Rigen los indicadores de las áreas residenciales de la cual forma parte.',
            'altura_maxima' => 'Rigen los indicadores de las áreas residenciales de la cual forma parte.',
            'indice_construccion' => 'Rigen los indicadores de las áreas residenciales de la cual forma parte.',
            'aislamientos' => 'Rigen los indicadores de las áreas residenciales de la cual forma parte.']);
        $add('com-2', ['tipo_establecimiento' => 'Locales de suministro de servicios y artículos para el uso principal de la zona; bajo impacto ambiental y urbanístico.',
            'area_libre' => '1 m2 de área libre por cada 1.5 m2 de área construida, para 1 y 2 pisos.',
            'area_frente_minimos' => 'Área mínima de lote: 250 m2. Frente mínimo: 10 m.',
            'altura_maxima' => 'No podrá ser mayor a 2 pisos.',
            'indice_construccion' => '1.0.',
            'aislamientos' => 'Rigen los indicadores de las áreas residenciales de la cual forma parte.']);
        $add('com-3', ['tipo_establecimiento' => 'Intercambio de servicios de ciudad; alto impacto ambiental y urbanístico con soluciones particulares.',
            'area_libre' => '1 m2 de área libre por cada 4 m2 de área construida, para 1 y 2 pisos.',
            'area_frente_minimos' => 'Área mínima de lote: 250 m2. Frente mínimo: 10 m.',
            'altura_maxima' => 'No podrá ser mayor a 2 pisos.',
            'indice_construccion' => '2.4.',
            'aislamientos' => 'Rigen los indicadores de las áreas residenciales de la cual forma parte.']);
        $add('com-4', ['tipo_establecimiento' => 'Intercambio de servicios intermunicipal o interregional; alto impacto ambiental y urbanístico.',
            'area_libre' => 'Indicadores de área libre, lote, altura, índice, aislamientos y estacionamientos concertados con Secretaría Distrital de Planeación y autoridad competente.',
            'area_frente_minimos' => 'AML no inferior a 260 m2. Frente: 10 m.',
            'area_ocupacion' => 'Para plataforma básica: 85%. Para usar 100% del lote deben asegurarse iluminación, ventilación y acceso a bomberos; en tal caso no se requiere aislamiento posterior.',
            'normas_comunes' => 'El mezanine cuenta como parte de la plataforma básica si no supera el 55% del área ocupada por la plataforma.']);
        foreach ([
            'ind-1' => ['60%', 'AML: 600 m2. FM: 20 m.', 'Hasta 2 pisos.', 'Hasta 130% del área del lote.', 'Antejardín: 7 m sobre vías principales y 5 m sobre vías secundarias. Patio interior mínimo 15 m2 con lado menor de 3 m.'],
            'ind-2' => ['70%', 'AML: 2000 m2. FM: 30 m.', 'Hasta 3 pisos en edificios de administración y operativos; mezanine o segundo piso sobre fachada principal y profundidad máxima 8 m.', 'Hasta 140% del área del lote.', 'Antejardín: 10 m sobre vías principales y 5 m sobre vías secundarias. Patio interior mínimo 25 m2 con lado menor de 5 m.'],
            'ind-3' => ['50%', 'AML: 2500 m2. FM: 40 m.', 'Hasta 3 pisos en edificios de administración y operativos; mezanine o segundo piso sobre fachada principal y profundidad máxima 8 m.', 'Hasta 100% del área del lote.', 'Antejardín: 10 m sobre cada lindero. Patio interior mínimo 42 m2 con lado menor de 6 m.'],
        ] as $slug => $v) $add($slug, ['tipo_establecimiento' => 'Actividad industrial según clasificación del Cuadro No. 4.',
            'area_ocupacion' => 'Hasta un ' . $v[0] . ' del área del lote.', 'area_frente_minimos' => $v[1],
            'altura_maxima' => $v[2], 'indice_construccion' => $v[3], 'aislamientos' => $v[4],
            'estacionamientos' => 'Verificar cupos privados, visitantes y área de cargue/descargue según tipo industrial.']);
        $add('tur-bocagrande-boquilla', ['tipo_establecimiento' => 'Hoteles, apartahoteles y establecimientos turísticos de alojamiento; vivienda unifamiliar, bifamiliar y multifamiliar.',
            'area_libre' => '1 m2 de área libre por cada 0.80 m2 de área construida.',
            'area_frente_minimos' => 'Lote 1: AML 960 m2, F 24 m. Lote 2: AML 1440 m2, F 24 m. Lote 3: AML 3000 m2, F 50 m.',
            'altura_maxima' => 'Resultante de área libre e índice de construcción. Para La Boquilla la altura máxima permitida es 6 pisos.',
            'indice_construccion' => 'Lote 1: 2.4. Lote 2: 3.5. Lote 3: 2.0.',
            'aislamientos' => 'Antejardín: 9 m vías principales y 7 m vías secundarias. Posterior: 5 m mínimo. Lateral: Bocagrande 3.5 m mínimo; La Boquilla 5 m mínimo.']);
        foreach (['port-1', 'port-2', 'port-3', 'port-4'] as $slug) $add($slug, [
            'tipo_establecimiento' => 'Actividad portuaria según clase del puerto y terminal del Cuadro No. 6.',
            'normas_comunes' => 'Muelles, puertos y obras marítimas se rigen por Ministerio de Transporte y DIMAR o entidades que hagan sus veces; requieren licencia o permiso ambiental competente.',
            'area_frente_minimos' => 'Definir con norma portuaria específica, autoridad competente y condiciones del terminal.',
            'altura_maxima' => 'Definir según proyecto, autoridad portuaria, ambiental y urbanística.',
        ]);
        foreach (['mixto-1','mixto-2','mixto-3','mixto-4','mixto-5'] as $slug) $add($slug, [
            'intensidad_uso' => 'Uso compatible y complementario hasta 50% según cuadro, salvo condición particular del uso adoptado.',
            'area_frente_minimos' => 'Aplican las normas del uso dominante o de la actividad específica adoptada; revisar compatibilidad y restricciones.',
            'altura_maxima' => 'Definir con el cuadro del uso dominante o actividad específica adoptada.',
            'indice_construccion' => 'Definir con el cuadro del uso dominante o actividad específica adoptada.',
            'normas_comunes' => 'La vía mixta sirve para evaluar escenarios de mayor y mejor uso; el perito debe sustentar cuál adopta.',
        ]);
        $add('rural-suburbano-turistico', ['area_frente_minimos' => '1 a 10 ha: FML 30 m. 10.1 a 30 ha: FML 100 m. Más de 30 ha: FML 200 m.',
            'area_ocupacion' => 'Índice de ocupación: 10%.', 'altura_maxima' => '1 piso para 1 a 10 ha; 2 pisos para 10.1 a 30 ha y más de 30 ha.',
            'normas_comunes' => 'Aplica a desarrollos turísticos Zona Norte y Barú; verificar instrumentos que desarrollen el POT.']);
        $add('rural-parcelaciones', ['area_frente_minimos' => 'AML: 100000 m2 (10 ha), FML 50 m. Área mínima de parcela: 10000 m2, FM 50 m.',
            'area_libre' => '20 m2 de área libre por cada 20 m2 de área construida.', 'aislamientos' => '10 m por cualquier lindero del lote.',
            'condiciones_especiales' => 'Debe asegurarse acceso vehicular a cada parcela. No se permite subdivisión de parcelas.']);
        $add('rural-agroindustrial', ['area_frente_minimos' => 'AML: 10000 m2. FM: 70 m.',
            'area_libre' => '5 m2 de área libre por cada 2 m2 de área construida.', 'aislamientos' => '10 m por cualquier lindero del lote.',
            'condiciones_especiales' => 'Los predios con frente sobre el corredor Bayunca-Pontezuela se rigen por normas de suelo rural suburbano Zona Norte y Barú.']);
        return $rows;
    }
}

return static function (Schema $schema): void {
    $seed = require __DIR__ . '/202609240002_seed_decreto_0977_usage_tables.php';
    $seed($schema);
    $now = gmdate('Y-m-d H:i:s');
    $rows = urbanNormFullPotentialParameters();
    $slugs = array_values(array_unique(array_map(static fn (array $row): string => $row[0], $rows)));
    $schema->db->prepare('DELETE FROM urban_norm_parameters WHERE category_slug IN (' . implode(', ', array_fill(0, count($slugs), '?')) . ')')->execute($slugs);
    $query = $schema->db->prepare('INSERT INTO urban_norm_parameters (category_slug, parameter_key, label, value_text, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)');
    foreach ($rows as $row) $query->execute([$row[0], $row[1], $row[2], $row[3], $row[4], $now, $now]);
};
