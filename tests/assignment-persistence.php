<?php
declare(strict_types=1);
// Included inside the delegated-analyst scenario, against disposable MySQL only.
$_POST = ['version' => 1, 'appraiser_id' => $expert, 'requester_email' => 'solicitante@example.test',
    'requester_phone' => '+57 300 123 4567', 'requester_municipality' => 'Cartagena, Bolívar',
    'request_date' => '2026-10-01', 'value_date' => '2026-09-30',
    'value_date_notes' => 'Valor referido a la fecha del encargo.', 'intended_use' => str_repeat('á', 1400)];
$_POST['source_document_details'] = ['escritura_publica' => 'Escritura de prueba', 'planos' => 'No suministrado'];
$assignment = App\Services\AppraisalChapterZeroInput::chapterZeroData(1, [], [$expert]);
$analystRepo->saveChapterZero($own, 1, 1, $assignment);
$reloaded = $ownerRepo->find($own, 1);
expect(App\Services\AppraisalDocumentTable::rows($reloaded)['planos']['text'] === 'No suministrado', 'tabla documental del analista persiste y titular la recupera');
expect($reloaded['requester_email'] === $_POST['requester_email'] && $reloaded['requester_phone'] === $_POST['requester_phone']
    && $reloaded['requester_municipality'] === $_POST['requester_municipality'] && $reloaded['request_date'] === '2026-10-01',
    'datos del solicitante guardados por analista se recuperan desde titular');
expect(mb_strlen($reloaded['intended_use']) === 1400 && $reloaded['value_date_notes'] === $_POST['value_date_notes'],
    'MySQL conserva 1400 caracteres unicode y explicación de fecha');
expectStatus(409, fn () => $analystRepo->saveChapterZero($own, 1, 1, $assignment), 'edición simultánea no sobrescribe nuevos datos');
$report = (new App\Services\AppraisalChapterOneReport())->build($reloaded, [], []);
expect(str_contains($report['text'], 'solicitante@example.test') && str_contains($report['text'], 'Valor referido a la fecha del encargo.'), 'datos nuevos alimentan informe');
unset($assignment['requester_email'], $assignment['requester_phone'], $assignment['requester_municipality'], $assignment['value_date_notes']);
unset($assignment['source_document_details']);
$analystRepo->saveChapterZero($own, 1, 2, $assignment);
expect($ownerRepo->find($own, 1)['requester_email'] === 'solicitante@example.test', 'formulario anterior no borra campos nuevos omitidos');
expect(App\Services\AppraisalDocumentTable::rows($ownerRepo->find($own, 1))['planos']['text'] === 'No suministrado', 'formulario anterior conserva tabla documental omitida');
$_POST['requester_email'] = 'correo inválido';
expectStatus(422, fn () => App\Services\AppraisalAssignmentInput::data(), 'correo inválido se rechaza sin guardar');
$tmp = tempnam(sys_get_temp_dir(), 'ga-photo-');
file_put_contents($tmp, hex2bin('89504e470d0a1a0a0000000d4948445200000001000000010804000000b51c0c020000000b4944415478da6364f80f00010501012718e3660000000049454e44ae426082'));
putenv('APPRAISAL_PHOTO_STORAGE_DIR=' . __DIR__ . '/.runtime/assignment-photos');
$upload = ['name' => ['mapa.png'], 'tmp_name' => [$tmp], 'error' => [UPLOAD_ERR_OK]];
(new App\Services\AppraisalPhotoUploadService())->store($upload, $own, 1, $analystRepo, null, 'location:chapter-one', 'Mapa de prueba');
$photos = App\Services\AppraisalLocationPhotos::items($ownerRepo, $own, 1);
expect(count($photos) === 1 && $photos[0]['name'] === 'Mapa de prueba', 'foto subida por analista queda disponible al titular');
$photo = $ownerRepo->findPhoto($photos[0]['id'], 1);
expect($photo['file_blob'] !== null && is_file(App\Services\AppraisalPhotoStorage::path($photo['storage_filename'])), 'foto conserva archivo y respaldo en base de datos');
expectStatus(404, fn () => $ownerRepo->findPhoto($photos[0]['id'], 2), 'foto de otro titular rechazada');
$_POST = [];
