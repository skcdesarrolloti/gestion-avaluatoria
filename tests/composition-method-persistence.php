<?php
$before = $repo->find($structureId, 1);
$posted = ['property-1' => ['label' => 'Oficina principal', 'method_choice' => 'mercado', 'original_method' => ''],
    'annex-1' => ['label' => 'Depósito', 'method_choice' => 'costo', 'original_method' => '']];
$saved = $repo->saveComposition($structureId, 1, (int) $before['version'], $before, $posted, (int) $before['methodology_version']);
$fresh = $repo->find($structureId, 1);
$flow = \App\Services\MethodologyWorkflow::saved($fresh);
expect($flow[$namedUnits[0]['id']]['method'] === 'mercado' && $flow[$namedUnits[1]['id']]['method'] === 'costo',
    'numeral 1 guarda método por identidad y capítulo 8 lee la misma selección');
$external = new \App\Models\MethodologyWorkflowRepository($app);
$external->save($structureId, 1, (int) $fresh['methodology_version'], $namedUnits[0]['id'], ['method' => 'renta', 'reason' => 'Conservar soporte']);
$posted['property-1']['original_method'] = 'mercado';
$posted['property-1']['method_choice'] = 'residual';
expectStatus(409, fn () => $repo->saveComposition($structureId, 1, (int) $fresh['version'],
    array_replace($fresh, ['titulo' => 'No debe guardarse']), $posted, $saved['composition_method_version']),
    'conflicto del método revierte también cambios de configuración');
expect($repo->find($structureId, 1)['titulo'] === $fresh['titulo'], 'conflicto conserva título y versión');
$posted['property-1']['method_choice'] = 'mercado';
$posted['annex-1']['original_method'] = 'costo';
$repo->saveComposition($structureId, 1, (int) $fresh['version'], $fresh, $posted, $saved['composition_method_version']);
$flow = \App\Services\MethodologyWorkflow::saved($repo->find($structureId, 1));
expect($flow[$namedUnits[0]['id']]['method'] === 'renta' && $flow[$namedUnits[0]['id']]['reason'] === 'Conservar soporte',
    'guardar nombres sin cambiar selector no sobrescribe decisión posterior del capítulo 8');
expectStatus(404, fn () => $repo->saveComposition($structureId, 2, 1, $fresh, $posted, 1), 'método no se guarda en expediente ajeno');
