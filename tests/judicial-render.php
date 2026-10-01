<?php
declare(strict_types=1);
(static function (): void {
    $oldCsrf = $_SESSION['csrf'] ?? null; $_SESSION['csrf'] = 'render-test';
    $expert = ['id' => str_repeat('a', 32), 'full_name' => 'Perito <prueba>', 'identification_number' => 'TEST'];
    $formData = ['contact' => 'Contacto recuperado', 'history' => [['kind' => 'publication', 'date' => '2025-01-01', 'title' => 'Publicación recuperada']]];
    ob_start(); view('judicial/profile', ['expert' => $expert, 'formData' => $formData, 'stored' => ['version' => 3], 'history' => []]); $html = ob_get_clean();
    expect(str_contains($html, 'Contacto recuperado') && str_contains($html, 'Publicación recuperada'), 'CGP vista recupera datos guardados usando contrato real de view');
    expect(str_contains($html, 'Perito &lt;prueba&gt;') && !str_contains($html, 'Perito <prueba>'), 'CGP escapa identidad y contenido del formulario');
    $vars = ['record' => ['id' => str_repeat('b', 32)], 'expert' => $expert, 'stored' => ['version' => 2], 'profile' => ['version' => 1, 'data' => []], 'formData' => ['directed' => 'si', 'court' => 'Juzgado recuperado'], 'eligible' => true, 'sections' => ['1.15' => ['Publicaciones', 'Texto']]];
    ob_start(); view('judicial/dossier', $vars); $html = ob_get_clean();
    expect(str_contains($html, 'Juzgado recuperado') && str_contains($html, 'Exportar anexo completo'), 'CGP anexo recupera campos y activa exportación para destinatario judicial');
    $vars['eligible'] = false; ob_start(); view('judicial/dossier', $vars); $html = ob_get_clean();
    expect(!str_contains($html, 'Exportar anexo completo'), 'CGP no ofrece exportación sin finalidad judicial y perito');
    if ($oldCsrf === null) unset($_SESSION['csrf']); else $_SESSION['csrf'] = $oldCsrf;
})();
