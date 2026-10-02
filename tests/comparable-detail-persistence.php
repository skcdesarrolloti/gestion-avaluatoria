<?php
declare(strict_types=1);
(static function () use ($app): void {
    $appraisals = new App\Models\AppraisalRepository($app); $aid = $appraisals->create(1);
    $repo = new App\Models\AppraisalComparableRepository($app); $cid = bin2hex(random_bytes(16));
    $row = ['id'=>$cid, 'source_name'=>'Prueba áreas', 'ph_regime'=>'no', 'land_m2'=>'250.5', 'built_m2'=>'120',
        'private_built_m2'=>'90', 'latitude'=>'10.4', 'longitude'=>'-75.5', 'evidence_detail'=>'Captura original',
        'ph_parking_presence'=>'si', 'ph_parking_nature'=>'privado_integrado', 'ph_deposit_count'=>'0', 'ph_components_source'=>'Contacto; prueba ficticia'];
    $repo->saveAll($aid, 1, App\Services\AppraisalComparableInput::rows(['comparable_rows_json'=>json_encode([$row])]), 0);
    $saved = $repo->forAppraisal($aid, 1)[0];
    expect($saved['land_m2'] === '250.5' && $saved['evidence_detail'] === 'Captura original', 'áreas y evidencia persisten en la misma muestra');
    $repo->saveAll($aid, 1, [['id'=>$cid, 'source_name'=>'Pantalla anterior', 'ph_regime'=>'si']], 1);
    $saved = $repo->forAppraisal($aid, 1)[0];
    expect($saved['land_m2'] === '250.5' && $saved['private_built_m2'] === '90', 'cambio de régimen y cliente anterior conserva componentes omitidos');
    expect($saved['ph_parking_nature'] === 'privado_integrado' && $saved['ph_deposit_count'] === '0'
        && $saved['ph_components_source'] === 'Contacto; prueba ficticia', 'cliente anterior conserva clasificación PH y soporte por ID');
    $repo->saveAll($aid, 1, [$saved + []], 2);
    expect($repo->forAppraisal($aid, 2) === [], 'áreas y evidencia no se leen desde otro propietario');
    expectStatus(422, fn () => $repo->saveAll($aid, 1, [array_replace($saved, ['land_m2'=>'-1'])], 3), 'error de área revierte toda la escritura');
    expect($repo->forAppraisal($aid, 1)[0]['land_m2'] === '250.5', 'error de validación conserva información anterior');
})();
