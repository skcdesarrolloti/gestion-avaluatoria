<?php
declare(strict_types=1);
$judicialRepo = new App\Models\JudicialExpertRepository($app);
$judicialExpertId = str_repeat('a', 32);
expect($judicialRepo->saveProfile($judicialExpertId, 1, ['profession' => 'Valuación'], 0) === 1, 'CGP crea perfil versionado');
expect($judicialRepo->profile($judicialExpertId, 2)['data'] === [], 'CGP antecedentes privados por propietario');
expectStatus(409, fn () => $judicialRepo->saveProfile($judicialExpertId, 1, [], 0), 'CGP no pisa perfil creado en otra pestaña');
$judicialRepo->saveDossier($id, 1, $judicialExpertId, ['court' => 'Juzgado A'], 0);
expect($judicialRepo->history($judicialExpertId, 1) === [], 'CGP borrador no agrega casos al historial');
expectStatus(409, fn () => $judicialRepo->saveDossier($id, 1, str_repeat('b', 32), [], 1), 'CGP no traslada declaraciones a otro perito');
expectStatus(409, fn () => $judicialRepo->present($id, 2, $judicialExpertId, 1, '2026-10-01', []), 'CGP no registra presentación ajena');
$judicialRepo->present($id, 1, $judicialExpertId, 1, '2026-10-01', ['case' => ['court' => 'Juzgado A', 'docket' => '001'], 'sections' => ['1.15' => ['Título', 'Texto firmado']]]);
expect(count($judicialRepo->history($judicialExpertId, 1)) === 1 && $judicialRepo->history($judicialExpertId, 2) === [], 'CGP presentación alimenta historial solo del propietario');
expect($judicialRepo->history($judicialExpertId, 1, $id) === [], 'CGP dictamen actual no se cita a sí mismo');
expectStatus(409, fn () => $judicialRepo->present($id, 1, $judicialExpertId, 2, '2026-10-01', []), 'CGP repetir presentación no duplica historial');
expectStatus(409, fn () => $judicialRepo->saveDossier($id, 1, $judicialExpertId, [], 2), 'CGP protege versión presentada');
$judicialRepo->saveProfile($judicialExpertId, 1, ['profession' => 'Otra información'], 1);
expect(str_contains($judicialRepo->dossier($id, 1)['snapshot'], 'Texto firmado'), 'CGP actualización del maestro conserva copia presentada');
