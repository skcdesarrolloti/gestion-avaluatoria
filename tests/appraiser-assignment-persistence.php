<?php
declare(strict_types=1);
(static function () use ($app): void {
    $experts = new \App\Models\AppraiserRepository($app);
    $records = new \App\Models\AppraisalRepository($app);
    $old = bin2hex(random_bytes(16)); $valid = bin2hex(random_bytes(16));
    $insert = $app->prepare('INSERT INTO valuation_appraisers (id, code, full_name, active, raa_expires_at, created_at, updated_at) VALUES (?, ?, ?, ?, ?, UTC_TIMESTAMP(), UTC_TIMESTAMP())');
    $insert->execute([$old, 'TO', 'Perito histórico de prueba', 'Si', '2000-01-01']);
    $insert->execute([$valid, 'TN', 'Perito vigente de prueba', 'Si', '2099-12-31']);
    $id = $records->create(1);
    $app->prepare('UPDATE appraisals SET appraiser_id = ?, expediente_number = ? WHERE id = ?')->execute([$old, 'TO-2026-01-001', $id]);
    $options = $experts->forExistingAssignment($old);
    expect(in_array($old, array_column($options, 'id'), true) && !in_array($old, array_column($experts->eligibleForAssignment(), 'id'), true), 'RAA vencido conserva responsable existente pero no habilita nueva asignación');
    $historic = array_values(array_filter($options, static fn ($row) => $row['id'] === $old))[0];
    expect(str_contains($historic['assignment_notice'], '2000-01-01'), 'aviso informa fecha real del certificado sin ocultar al perito');
    $allowed = array_column($experts->eligibleForAssignment(), 'id');
    foreach (['', $old] as $posted) {
        $record = $records->find($id, 1);
        $_POST = ['appraiser_id' => $posted, 'titulo' => 'Avance conservado'];
        $data = \App\Services\AppraisalChapterZeroInput::chapterZeroData($record['version'], [], $allowed, $record['appraiser_id']);
        $records->saveChapterZero($id, 1, $record['version'], $data);
        $after = $records->find($id, 1);
        expect($after['appraiser_id'] === $old && $after['expediente_number'] === 'TO-2026-01-001'
            && $after['titulo'] === 'Avance conservado', 'guardado conserva responsable y consecutivo con selección ' . ($posted === '' ? 'vacía' : 'histórica'));
    }
    $_POST = ['appraiser_id' => $old];
    expectStatus(422, fn () => \App\Services\AppraisalChapterZeroInput::chapterZeroData(1, [], $allowed), 'nuevo avalúo no permite perito vencido');
    expectStatus(422, fn () => \App\Services\AppraisalChapterZeroInput::chapterZeroData(1, [], $allowed, $valid), 'no se puede sustituir otro responsable por un perito vencido');
    foreach (['NULL', "'2000-01-01'"] as $date) {
        $app->prepare("UPDATE valuation_appraisers SET raa_expires_at = $date, active = 'No' WHERE id = ?")->execute([$old]);
        $rows = $experts->forExistingAssignment($old);
        expect(in_array($old, array_column($rows, 'id'), true), 'responsable inactivo sigue visible sin nueva habilitación');
    }
    $appraisers = $options; $field = static fn ($name) => $name === 'appraiser_id' ? $old : '';
    $selectedAppraiser = static fn ($value) => $value === $old ? 'selected' : '';
    $previousUser = $_SESSION['user'] ?? null;
    $_SESSION['user'] = ['analyst_id' => 'prueba', 'responsible_appraiser_id' => $old];
    ob_start(); require BASE_PATH . '/app/Views/appraisals/chapter-zero-responsible.php'; $html = ob_get_clean();
    expect(str_contains($html, 'Perito histórico de prueba') && str_contains($html, '2000-01-01')
        && str_contains($html, 'disabled') && str_contains($html, 'type="hidden" name="appraiser_id"'), 'analista ve responsable histórico y aviso sin poder cambiarlo');
    if ($previousUser === null) unset($_SESSION['user']); else $_SESSION['user'] = $previousUser;
    $_POST = [];
})();
