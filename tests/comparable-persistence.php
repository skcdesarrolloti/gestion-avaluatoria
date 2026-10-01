<?php
declare(strict_types=1);
// Included only by the disposable MySQL integration suite.
(static function (PDO $app, string $id): void {
    $repo = new App\Models\AppraisalComparableRepository($app);
    $photos = new App\Models\ComparablePhotoRepository($app);
    $rows = [];
    for ($i = 0; $i < 300; $i++) $rows[] = ['id' => bin2hex(random_bytes(16)), 'source_name' => 'Fuente ' . $i,
        'source_url' => 'https://example.test/' . $i, 'price_amount' => '450000000', 'ph_regime' => $i === 0 ? 'si' : 'por_verificar'];
    $rows = App\Services\AppraisalComparableInput::rows(['comparable_rows_json' => json_encode($rows)]);
    $version = $repo->saveAll($id, 1, $rows, 0);
    expect($version === 1 && count($repo->forAppraisal($id, 1)) === 300, 'matriz conserva 300 filas en MySQL');
    expect($repo->forAppraisal($id, 1)[0]['ph_regime'] === 'si', 'régimen PH persiste por muestra');
    expectStatus(409, fn () => $repo->saveAll($id, 1, [], 0), 'matriz rechaza edición obsoleta sin borrar filas');
    expect(count($repo->forAppraisal($id, 1)) === 300, 'conflicto conserva las 300 muestras');
    $rows[0]['latitude'] = '10.391';
    $rows[0]['longitude'] = '-75.55';
    $rows[0]['location_precision'] = 'aproximada';
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
    $reloaded = array_values(array_filter($repo->forAppraisal($id, 1), fn ($row) => $row['id'] === $sample))[0];
    expect((float) $reloaded['latitude'] === 10.391 && $reloaded['location_precision'] === 'aproximada', 'completar coordenadas conserva muestra y foto existentes');
    $migration = require BASE_PATH . '/database/migrations/202609300003_comparable_ph_and_photos.php';
    $migration(new App\Database\Schema($app));
    $expand = require BASE_PATH . '/database/migrations/202610010001_expand_comparable_sample_index.php';
    $expand(new App\Database\Schema($app));
    expect(count($photos->listing($id, $sample, 1)) === 1, 'migración reintentada conserva fotos y muestras');
    $version = $repo->saveAll($id, 1, [], 2);
    expect($repo->forAppraisal($id, 1) === [], 'vaciar matriz persiste sin filas al recargar');
    expectStatus(404, fn () => $photos->listing($id, $sample, 1), 'foto de muestra retirada no queda accesible sin su muestra');
    $repo->saveAll($id, 1, $rows, $version);
    expect(count($repo->forAppraisal($id, 1)) === 300 && count($photos->listing($id, $sample, 1)) === 1, 'restaurar IDs originales recupera muestras y sus fotos');
})($app, $id);
