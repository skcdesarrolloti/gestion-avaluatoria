<?php
declare(strict_types=1);
(static function (): void {
    $previousCsrf = $_SESSION['csrf'] ?? null;
    $_SESSION['csrf'] = 'sector-test-token';
    $record = ['id' => str_repeat('a', 32)];
    $photos = [['id' => str_repeat('b', 32), 'caption' => 'sector:mapa-delimitacion',
        'unit_id' => null, 'display_name' => 'Mapa', 'source_filename' => 'mapa.png', 'file_available' => true]];
    $photoMessage = $photoError = null;
    $photoUploadCaption = 'sector:mapa-delimitacion';
    $photoUploadReturnTo = 'avaluos/' . $record['id'] . '/sector#banco-02';
    ob_start(); require BASE_PATH . '/app/Views/appraisals/photo-upload.php'; $html = ob_get_clean();
    expect(strpos($html, '>Eliminar foto<') < strpos($html, '<img class="max-h-72'), 'eliminar foto visible antes de imagen sectorial');
    expect(str_contains($html, '/fotos/' . str_repeat('b', 32) . '/eliminar') && str_contains($html, 'sector#banco-02'), 'control elimina foto específica y conserva retorno a 2.2');
    if ($previousCsrf === null) unset($_SESSION['csrf']); else $_SESSION['csrf'] = $previousCsrf;
})();
