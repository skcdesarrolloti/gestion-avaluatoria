<?php
declare(strict_types=1);
(static function (): void {
    $service = new \App\Services\MarketSubjectChecklist();
    $record = ['regimen_ph'=>'si'];
    $unit = ['id'=>str_repeat('a', 32), 'unit_kind'=>'annex', 'label'=>'Garaje', 'method_structure'=>'area_privada'];
    $subject = ['property_registry'=>'060-100', 'current_use'=>'oficina'];
    $ph = ['coefficient'=>'1.5', 'regulation_document'=>'Reglamento pág. 10', 'technical'=>['usos_permitidos'=>'oficina']];
    $byKey = static fn ($result) => array_column($result['rows'], null, 'key');
    $empty = $byKey($service->build($record, $subject, $unit, $ph));
    expect($empty['registry']['state'] === 'missing' && $empty['coefficient']['state'] === 'missing'
        && $empty['use']['state'] === 'missing', 'anexo sin vínculo no hereda matrícula uso ni coeficiente global');
    $data = ['identity_scope'=>'propia', 'legal_nature'=>'privada', 'registry'=>'060-200', 'legal_source'=>'CTL garaje pág. 1',
        'observed_use'=>'parqueadero', 'approved_use'=>'Parqueadero', 'use_source'=>'Reglamento pág. 15',
        'coefficient'=>'0,25', 'coefficient_source'=>'Reglamento cuadro garaje',
        'included_components'=>'Garaje 12 sin depósito', 'scope_source'=>'Escritura de garaje'];
    $unit += ['notes'=>'Celda 12 cubierta en sótano, con acceso por rampa.', 'construction_type'=>'parqueo',
        'market_evidence_json'=>json_encode($data), 'area_private_m2'=>'12.50', 'surface_source'=>'Escritura pág. 2',
        'area_adopted_m2'=>'12.50', 'construction_state'=>'completa',
        'conservation_result_json'=>json_encode(['state_global_adopted'=>'2', 'items'=>[['state_adopted'=>'2','evidence'=>'Foto garaje 12']]])];
    $complete = $service->build($record, $subject, $unit, $ph);
    expect($complete['pending'] === 0 && $complete['ok'] === 9, 'datos propios completos confrontan sin copiar los de oficina');
    $missingDescription = array_replace($unit, ['notes'=>'']);
    expect($byKey($service->build($record, $subject, $missingDescription, $ph))['description']['state'] === 'missing',
        'anexo sin descripción propia queda pendiente aunque tiene soportes jurídicos');
    expect(\App\Services\MarketUnitDescriptionCheck::row(array_replace($unit, ['construction_type'=>'deposito']))['state'] === 'difference',
        'nombre garaje y clasificación depósito requieren confrontación');
    expect(\App\Services\MarketUnitDescriptionCheck::row(array_replace($unit, ['label'=>'Celda de parqueo 12']))['state'] === 'ok',
        'celda de parqueo corresponde al tipo parqueo sin cambiar clasificación');
    expect(\App\Services\MarketUnitDescriptionCheck::row(array_replace($unit, ['label'=>'Anexo 1']))['state'] === 'missing',
        'nombre genérico del anexo no acredita identificación propia');
    $duplicate = array_replace($unit, ['id'=>str_repeat('b',32), 'label'=>'Otra unidad independiente']);
    expect($byKey($service->build($record, $subject, $unit, $ph, [], [$unit,$duplicate]))['registry']['state'] === 'difference',
        'dos unidades independientes con misma matrícula se confrontan como diferencia');
    $before = $unit;
    $unit['area_private_m2'] = '';
    expect($byKey($service->build($record, $subject, $unit, $ph))['area']['state'] === 'missing', 'vaciar área vuelve a pendiente sin checklist manual');
    $unit['area_private_m2'] = '13';
    expect($byKey($service->build($record, $subject, $unit, $ph))['area']['state'] === 'difference', 'área privada distinta de adoptada para área privada dispara diferencia');
    $unit = $before; $data['identity_scope'] = 'sujeto'; $unit['market_evidence_json'] = json_encode($data);
    $linked = $byKey($service->build($record, $subject, $unit, $ph));
    expect($linked['registry']['state'] === 'difference' && $linked['coefficient']['state'] === 'difference', 'vínculo explícito confronta matrícula y coeficiente con ficha general');
    $unit = $before; $data['identity_scope'] = 'propia'; $data['approved_use'] = 'oficina'; $unit['market_evidence_json'] = json_encode($data);
    expect($byKey($service->build($record, $subject, $unit, $ph))['use']['state'] === 'difference', 'uso observado distinto de aprobado pide contraste sustentado');
    $data['use_reconciliation'] = 'Uso incompatible, pendiente'; $unit['market_evidence_json'] = json_encode($data);
    expect($byKey($service->build($record, $subject, $unit, $ph))['use']['state'] === 'difference', 'una explicación sin compatibilidad sustentada no resuelve diferencia');
    $data['use_contrast'] = 'compatible'; $data['use_reconciliation'] = 'Parqueadero es uso complementario permitido en reglamento pág. 15';
    $unit['market_evidence_json'] = json_encode($data);
    expect($byKey($service->build($record, $subject, $unit, $ph))['use']['state'] === 'ok', 'contraste técnico explícito sustentado resuelve distinta redacción de usos');
    $unit['conservation_result_json'] = json_encode(['state_global_adopted'=>'2', 'items'=>[['state_adopted'=>'','evidence'=>'']]]);
    expect($byKey($service->build($record, $subject, $unit, $ph))['conservation']['state'] === 'missing', 'estado global generado sin evidencia no es OK');
    $data['legal_nature'] = 'comun_exclusivo'; $unit['market_evidence_json'] = json_encode($data);
    $common = $byKey($service->build($record, $subject, $unit, $ph, ['treatment'=>'separado']));
    expect($common['scope']['state'] === 'difference' && $common['coefficient']['state'] === 'na', 'común exclusivo confronta valor separado sin inventar coeficiente propio');
    expectStatus(422, fn () => \App\Services\MarketSubjectEvidence::input(['coefficient'=>'101']), 'coeficiente superior a 100 se rechaza');
    expectStatus(422, fn () => \App\Services\MarketSubjectEvidence::input(['registry'=>['texto']]), 'campo estructurado malicioso se rechaza');
    expectStatus(422, fn () => \App\Services\MarketSubjectEvidence::input(['legal_nature'=>'inventado']), 'naturaleza no definida se rechaza');
    $_GET = ['unit'=>$before['id'], 'detail'=>'conservacion'];
    expect(\App\Services\SubjectUnitNavigation::selected([['id'=>'otra'], $before]) === $before['id']
        && \App\Services\SubjectUnitNavigation::detail(['conservacion'], 'basicos') === 'conservacion', 'enlace abre unidad y apartado solicitado');
    $_GET = [];
})();
