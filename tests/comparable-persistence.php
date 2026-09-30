<?php
declare(strict_types=1);
// Included only by the disposable MySQL integration suite.
(static function (PDO $app, string $id): void {
    $repo = new App\Models\AppraisalComparableRepository($app);
    $photos = new App\Models\ComparablePhotoRepository($app);
    $rows = [];
    for ($i = 0; $i < 60; $i++) $rows[] = ['id' => bin2hex(random_bytes(16)), 'source_name' => 'Fuente ' . $i,
        'source_url' => 'https://example.test/' . $i, 'price_amount' => '450000000', 'ph_regime' => $i === 0 ? 'si' : 'por_verificar'];
    $version = $repo->saveAll($id, 1, $rows, 0);
    expect($version === 1 && count($repo->forAppraisal($id, 1)) === 60, 'matriz conserva 60 filas en MySQL');
    expect($repo->forAppraisal($id, 1)[0]['ph_regime'] === 'si', 'régimen PH persiste por muestra');
    expectStatus(409, fn () => $repo->saveAll($id, 1, [], 0), 'matriz rechaza edición obsoleta sin borrar filas');
    expect(count($repo->forAppraisal($id, 1)) === 60, 'conflicto conserva las 60 muestras');
    $sample = $rows[0]['id'];
    $photo = ['name' => 'evidencia.png', 'mime' => 'image/png', 'caption' => 'Foto prueba', 'blob' => 'test bytes', 'hash' => hash('sha256', 'test bytes')];
    $photos->store($id, $sample, 1, $photo);
    $photos->store($id, $sample, 1, $photo);
    $list = $photos->listing($id, $sample, 1);
    expect(count($list) === 1, 'reintentar foto idéntica no duplica el soporte');
    expectStatus(404, fn () => $photos->listing($id, $sample, 2), 'fotos rechazan propietario ajeno');
    expectStatus(404, fn () => $photos->photo($id, $rows[1]['id'], 1, $list[0]['id']), 'foto no se atribuye a otro comparable');
    expectStatus(404, fn () => $photos->store(str_repeat('0', 32), $sample, 1, $photo), 'foto rechaza avalúo diferente');
    $repo->saveAll($id, 1, array_reverse($rows), 1);
    expect($photos->photo($id, $sample, 1, $list[0]['id'])['file_blob'] === 'test bytes', 'reordenar y guardar conserva foto por ID estable');
    $migration = require BASE_PATH . '/database/migrations/202609300003_comparable_ph_and_photos.php';
    $migration(new App\Database\Schema($app));
    expect(count($photos->listing($id, $sample, 1)) === 1, 'migración reintentada conserva fotos y muestras');
})($app, $id);
