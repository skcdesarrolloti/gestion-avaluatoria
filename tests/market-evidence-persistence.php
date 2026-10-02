<?php
declare(strict_types=1);
$marketId = $repo->create(1);
$app->prepare('UPDATE appraisals SET igac_property_units_count = 1, igac_annex_units_count = 1 WHERE id = ?')->execute([$marketId]);
$repo->ensureUnits($marketId, 1, 1, 1);
$marketUnits = $repo->units($marketId, 1);
$marketUnitId = $marketUnits[1]['id'];
$marketPosted = \App\Services\MarketSubjectEvidence::input(['identity_scope'=>'propia', 'legal_nature'=>'privada',
    'registry'=>'060-200', 'legal_source'=>'CTL garaje', 'observed_use'=>'parqueo', 'approved_use'=>'parqueo',
    'use_source'=>'Reglamento', 'coefficient'=>'0,25', 'coefficient_source'=>'Cuadro garaje',
    'included_components'=>'Garaje 12', 'scope_source'=>'Escritura']);
expect($repo->saveMarketEvidence($marketId, 1, $marketUnitId, 0, $marketPosted) === 1, 'Mercado autoguarda soporte con versión propia');
$reloadMarket = $repo->units($marketId, 1);
expect(\App\Services\MarketSubjectEvidence::decode($reloadMarket[1])['registry'] === '060-200'
    && (int) $reloadMarket[1]['market_evidence_version'] === 1, 'soporte Mercado persiste al consultar capítulo 8');
expect(empty($reloadMarket[0]['market_evidence_json']), 'guardar garaje no cambia soporte de oficina');
expectStatus(409, fn () => $repo->saveMarketEvidence($marketId, 1, $marketUnitId, 0, []), 'conflicto no sobrescribe soporte Mercado');
expectStatus(404, fn () => $repo->saveMarketEvidence($marketId, 2, $marketUnitId, 1, []), 'soporte Mercado aísla propietario');
expectStatus(404, fn () => $repo->saveMarketEvidence($id, 1, $marketUnitId, 1, []), 'soporte Mercado rechaza unidad de otro avalúo');
$app->prepare('UPDATE appraisals SET igac_annex_units_count = 0 WHERE id = ?')->execute([$marketId]);
expectStatus(404, fn () => $repo->saveMarketEvidence($marketId, 1, $marketUnitId, 1, []), 'soporte de anexo inactivo no se altera');
$app->prepare('UPDATE appraisals SET igac_annex_units_count = 1 WHERE id = ?')->execute([$marketId]);
expect(\App\Services\MarketSubjectEvidence::decode($repo->units($marketId, 1)[1]) === $marketPosted, 'reactivar anexo conserva soporte tras solicitudes rechazadas');
$marketUnit = $repo->units($marketId, 1)[1];
$marketRecord = $repo->find($marketId, 1);
$marketBefore = (new \App\Services\MarketSubjectChecklist())->build($marketRecord + ['regimen_ph'=>'si'], [], $marketUnit, []);
$surfaceMarket = array_fill_keys(['area_land_m2','area_built_m2','area_common_m2','front_length_m','depth_length_m'], null);
$_POST = ['unit_surfaces'=>[$marketUnitId=>$surfaceMarket + ['area_private_m2'=>'12,5','surface_source'=>'Escritura pág. 2']]];
$repo->saveUnitSurfaces($marketId, 1, \App\Services\AppraisalChapterZeroInput::unitSurfaceData());
$_POST = [];
$marketAfter = (new \App\Services\MarketSubjectChecklist())->build(array_replace($marketRecord, ['regimen_ph'=>'si']), [], $repo->units($marketId, 1)[1], []);
$afterRows = array_column($marketAfter['rows'], null, 'key');
expect($afterRows['area']['state'] === 'ok', 'actualizar vuelve a leer área privada autoguardada en numeral 3');
expect(\App\Services\MarketSubjectEvidence::decode($repo->units($marketId, 1)[1]) === $marketPosted, 'guardar área no borra soportes ni otra unidad');
$parentId = $marketUnits[0]['id'];
$patch = \App\Services\MarketPhScope::input(['parent_unit_id'=>$parentId, 'area_in_parent'=>'incluida',
    'parent_area_source'=>'Reglamento pág. 10', 'legal_nature'=>'integrada', 'parent_area_note'=>'Incluido en área privada oficina']);
expect($repo->saveMarketEvidence($marketId, 1, $marketUnitId, 1, $patch) === 2, 'M2 guarda parcialmente vínculo PH con versión de evidencia');
$linked = \App\Services\MarketSubjectEvidence::decode($repo->units($marketId, 1)[1]);
expect($linked['observed_use'] === 'parqueo' && $linked['parent_unit_id'] === $parentId && $linked['legal_source'] === 'CTL garaje', 'M2 conserva usos documentos y matrícula del numeral 3');
$repo->saveMarketEvidence($marketId, 1, $marketUnitId, 2, $marketPosted);
$linked = \App\Services\MarketSubjectEvidence::decode($repo->units($marketId, 1)[1]);
expect($linked['parent_unit_id'] === $parentId && $linked['area_in_parent'] === 'incluida', 'cliente anterior de numeral 3 conserva vínculo y composición omitidos');
expectStatus(409, fn () => $repo->saveMarketEvidence($marketId, 1, $marketUnitId, 2, $patch), 'M1 M2 y numeral 3 rechazan versiones antiguas sin sobrescribir');
expectStatus(422, fn () => $repo->saveMarketEvidence($marketId, 1, $marketUnitId, 3, ['parent_unit_id'=>$marketUnitId]), 'M2 rechaza anexo como principal');
expectStatus(422, fn () => $repo->saveMarketEvidence($marketId, 1, $parentId, 0, ['parent_unit_id'=>$parentId]), 'M2 rechaza padre propio de principal');
$foreignId = $repo->create(1);
$app->prepare('UPDATE appraisals SET igac_property_units_count = 1 WHERE id = ?')->execute([$foreignId]);
$repo->ensureUnits($foreignId, 1, 1, 0);
$foreignParent = $repo->units($foreignId, 1)[0]['id'];
expectStatus(422, fn () => $repo->saveMarketEvidence($marketId, 1, $marketUnitId, 3, ['parent_unit_id'=>$foreignParent]), 'M2 impide vínculos hacia otro expediente');
$app->prepare('UPDATE appraisals SET igac_property_units_count = 0 WHERE id = ?')->execute([$marketId]);
expectStatus(422, fn () => $repo->saveMarketEvidence($marketId, 1, $marketUnitId, 3, $patch), 'M2 impide seleccionar principal inactiva');
$app->prepare('UPDATE appraisals SET igac_property_units_count = 1 WHERE id = ?')->execute([$marketId]);
expect((int) $repo->units($marketId, 1)[1]['market_evidence_version'] === 3, 'vínculos rechazados conservan datos y versión');
