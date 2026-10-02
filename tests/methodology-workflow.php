<?php
declare(strict_types=1);
(static function (): void {
    $units = [['id' => 'unit-a', 'label' => 'Principal', 'unit_kind' => 'property'], ['id' => 'unit-b', 'label' => 'Cerramiento', 'unit_kind' => 'annex']];
    $components = \App\Services\MethodologyWorkflow::components([], $units);
    expect(array_keys($components) === ['unit-a', 'unit-b'] && $components['unit-b']['unit'] === $units[1], 'capítulo 8 refleja solo unidades registradas con identidad y datos intactos');
    $namedUnits = [
        ['id' => 'main', 'unit_kind' => 'property', 'unit_index' => 1, 'label' => 'Unidad 1'],
        ['id' => 'wall', 'unit_kind' => 'annex', 'unit_index' => 1, 'label' => 'Anexo 1', 'construction_type' => 'cerramiento'],
        ['id' => 'custom', 'unit_kind' => 'annex', 'unit_index' => 2, 'label' => 'Placa vial norte', 'construction_type' => 'placa'],
        ['id' => 'unknown', 'unit_kind' => 'annex', 'unit_index' => 3, 'label' => 'Anexo 3'],
    ];
    $named = \App\Services\MethodologyWorkflow::components(['tipo_inmueble' => 'oficina'], $namedUnits);
    expect(array_column($named, 'label') === ['Oficina · Unidad 1', 'Cerramiento · Anexo 1', 'Placa vial norte', 'Anexo 3'], 'nombres propios del capítulo 1 prevalecen y nombres genéricos se identifican con su tipo registrado');
    expect(array_column($named, 'unit') === $namedUnits, 'identificar pestañas no renombra ni modifica unidades almacenadas');
    $namedUnits[] = ['id' => 'other', 'unit_kind' => 'property', 'unit_index' => 2, 'label' => 'Unidad 2'];
    expect(\App\Services\MethodologyWorkflow::components(['tipo_inmueble' => 'oficina'], $namedUnits)['other']['label'] === 'Unidad 2', 'no atribuye el tipo general a múltiples unidades sin clasificación propia');
    expect(\App\Services\MethodologyWorkflow::components(['methodology_workflow' => '{"terreno":{"method":"mercado"}}'], []) === [], 'sin unidades registradas no inventa principal ni resucita terreno virtual');
    $sourceUnits = [$units[1], ['id' => 'common', 'unit_kind' => 'common'], $units[0]];
    expect(array_keys(\App\Services\MethodologyWorkflow::components([], $sourceUnits)) === ['unit-b', 'unit-a'], 'respeta el orden de capítulos 1 y 3 y excluye ficha común');
    expectStatus(422, fn () => \App\Services\MethodologyWorkflow::validateKey('terreno', $components), 'no permite guardar metodología en componentes inventados');
    expectStatus(422, fn () => \App\Services\MethodologyWorkflow::validateKey('foreign', $components), 'flujo rechaza componente ajeno o inactivo');
    $a = ['id' => 'a', 'component_key' => 'unit-a', 'price_amount' => 100];
    $b = ['id' => 'b', 'component_key' => 'unit-b', 'price_amount' => 200];
    $legacy = ['id' => 'c', 'price_amount' => 300];
    $merged = \App\Services\MethodologyComparableScope::merge([$a, $b, $legacy], [array_replace($a, ['price_amount' => 110])], 'unit-a');
    expect(count($merged) === 3 && $merged[0] === $b && $merged[1] === $legacy, 'editar componente conserva los demás y el banco anterior');
    expectStatus(422, fn () => \App\Services\MethodologyComparableScope::merge([$a, $b], [$b], 'unit-a'), 'matriz impide sobrescribir muestra de otro componente');
    expect(count(\App\Services\MethodologyComparableScope::rows([$a, $legacy], '')) === 1, 'muestras anteriores quedan sin asignar sin pérdida');
    $row = ['id' => 'x', 'active' => 'si', 'status' => 'usada', 'property_type' => 'Lote', 'price_amount' => 1000, 'area_m2' => 10, 'area_basis' => 'Terreno', 'operation' => 'Venta', 'price_unit' => 'precio_total', 'ph_regime' => 'no'];
    $result = \App\Services\MarketComponentReview::build([$row, array_replace($row, ['price_amount' => 2000]), array_replace($row, ['operation' => 'Arriendo', 'price_unit' => 'canon_mensual']), array_replace($row, ['status' => 'por_verificar'])]);
    expect(count($result['groups']) === 2 && $result['pending'] === 1, 'análisis no mezcla venta arriendo ni muestras no revisadas');
    $group = array_values($result['groups'])[0];
    expect($group['mean'] === 150.0 && $group['median'] === 150.0 && abs($group['sd'] - sqrt(5000)) < .001, 'media mediana y desviación muestral calculadas sobre grupo compatible');
    expect(\App\Services\MethodologyWorkflow::fingerprint([$a]) === \App\Services\MethodologyWorkflow::fingerprint([$a + ['updated_at' => 'now', 'sample_index' => 42]]), 'cambios administrativos no invalidan memoria de otro componente');
    expectStatus(422, fn () => \App\Services\MethodologyWorkflow::input(['method' => 'inventado']), 'selección valida catálogo en servidor');
    expect(str_contains(\App\Services\MethodologyWorkflowReport::text(['methodology_workflow' => null], $units), 'pendiente'), 'informe no presenta sugerencia como método adoptado');
})();
