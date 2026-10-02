<?php
declare(strict_types=1);
(static function () use ($app): void {
    $ar = new App\Models\AppraisalRepository($app);
    $id = $ar->create(1);
    $flow = new App\Models\MethodologyWorkflowRepository($app);
    expect($flow->save($id, 1, 0, 'terreno', ['method' => 'mercado', 'reason' => 'Evidencia del suelo']) === 1, 'selección por componente persiste y versiona');
    expectStatus(409, fn () => $flow->save($id, 1, 0, 'terreno', ['method' => 'costo']), 'segundo equipo no sobrescribe metodología');
    expectStatus(404, fn () => $flow->save($id, 2, 1, 'terreno', ['method' => 'costo']), 'metodología rechaza propietario ajeno');
    $flow->save($id, 1, 1, 'anexo', ['method' => 'costo']);
    $saved = App\Services\MethodologyWorkflow::saved($ar->find($id, 1));
    expect($saved['terreno']['method'] === 'mercado' && $saved['anexo']['method'] === 'costo', 'recarga conserva métodos segregados');
    $cr = new App\Models\AppraisalComparableRepository($app);
    $legacyId = bin2hex(random_bytes(16)); $otherId = bin2hex(random_bytes(16));
    $cr->saveAll($id, 1, [['id' => $legacyId, 'source_name' => 'Anterior', 'evidence_detail' => 'Soporte conservado'], ['id' => $otherId, 'source_name' => 'Otro componente', 'component_key' => 'anexo']], 0);
    $rows = $cr->forAppraisal($id, 1);
    expect(count(App\Services\MethodologyComparableScope::rows($rows, '')) === 1, 'muestra anterior sigue disponible sin asignación');
    $rows[0]['component_key'] = 'terreno'; $cr->saveAll($id, 1, $rows, 1);
    $incoming = App\Services\MethodologyComparableScope::rows($cr->forAppraisal($id, 1), 'terreno');
    $incoming[0]['price_amount'] = 150;
    $merged = App\Services\MethodologyComparableScope::merge($cr->forAppraisal($id, 1), $incoming, 'terreno');
    $cr->saveAll($id, 1, $merged, 2);
    $read = $cr->forAppraisal($id, 1);
    expect(count($read) === 2 && App\Services\MethodologyComparableScope::rows($read, 'terreno')[0]['id'] === $legacyId, 'reasignar y editar conserva ID estable y número de muestras');
    expect(App\Services\MethodologyComparableScope::rows($read, 'terreno')[0]['evidence_detail'] === 'Soporte conservado', 'asignación mantiene evidencia');
    expect(App\Services\MethodologyComparableScope::rows($read, 'anexo')[0]['id'] === $otherId, 'guardar terreno conserva muestras de anexos');
    expectStatus(409, fn () => $cr->saveAll($id, 1, $merged, 2), 'matriz segregada conserva protección de concurrencia');
})();
