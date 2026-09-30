<?php
declare(strict_types=1);
(static function (): void {
    $input = App\Services\AppraisalComparableInput::rows(['comparable_rows_json' => json_encode([
        ['source_name' => 'Prueba', 'ph_regime' => 'si'], ['source_name' => 'Segunda', 'ph_regime' => 'por_verificar']])]);
    expect(count($input) === 2 && $input[1]['ph_regime'] === 'por_verificar', 'transporte JSON conserva régimen pendiente');
    expectStatus(422, fn () => App\Services\AppraisalComparableInput::rows(['comparable_rows_json' => '{truncado']), 'matriz JSON truncada no borra datos');
    expectStatus(422, fn () => App\Services\AppraisalComparableInput::rows(['comparables' => [['source_name' => ['inválido']]]]), 'campos anidados inválidos rechazados');
    $path = tempnam(sys_get_temp_dir(), 'ga-photo-input-');
    try {
        file_put_contents($path, hex2bin('89504e470d0a1a0a0000000d49484452000000010000000108060000001f15c4890000000d4944415478da63fccfc0500f000583027f94d58adf0000000049454e44ae426082'));
        $file = ['name' => 'foto.png', 'tmp_name' => $path, 'error' => UPLOAD_ERR_OK];
        $photo = App\Services\ComparablePhotoUpload::inspect($file, 'Prueba');
        expect($photo['mime'] === 'image/png' && $photo['hash'] === hash_file('sha256', $path), 'foto validada por contenido y huella');
        foreach (['foto.php', 'foto.jpg'] as $name) {
            $rejected = false;
            try { App\Services\ComparablePhotoUpload::inspect(array_replace($file, ['name' => $name]), ''); }
            catch (InvalidArgumentException) { $rejected = true; }
            expect($rejected, 'rechaza imagen con extensión incompatible: ' . $name);
        }
        file_put_contents($path, str_repeat('x', 5 * 1024 * 1024 + 1));
        $rejected = false;
        try { App\Services\ComparablePhotoUpload::inspect($file, ''); } catch (InvalidArgumentException) { $rejected = true; }
        expect($rejected, 'foto superior a 5 MB rechazada');
    } finally { unlink($path); }
})();
