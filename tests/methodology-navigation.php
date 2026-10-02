<?php
declare(strict_types=1);

(static function (): void {
    $record = ['id' => str_repeat('a', 32), 'titulo' => 'Oficina de prueba', 'tipo_inmueble' => 'oficina',
        'tipo_negocio' => 'venta', 'municipio' => 'Cartagena', 'regimen_ph' => 'si'];
    $subject = [];
    $units = [
        ['id' => 'office', 'label' => 'Oficina <principal>', 'unit_kind' => 'property', 'property_type' => 'oficina'],
        ['id' => 'annex', 'label' => 'Depósito de oficina', 'unit_kind' => 'annex', 'valuation_treatment' => 'integrado'],
    ];
    $components = \App\Services\MethodologyWorkflow::components($record, $units);
    $methodologyChapter = (new \App\Services\AppraisalMethodologyChapterReport())->build($record, $subject, $units);
    $guide = (new \App\Services\AppraisalComparableSearchGuide())->build($record, $subject, $units, []);
    $componentKey = '';
    $flow = $selected = $comparableRows = $marketNeighborhoods = $phProfile = [];
    $allComparableRows = [['id' => 'legacy', 'source_name' => 'Muestra anterior']];
    $method = 'mercado';
    foreach (['components', 'decision', 'report', '1', '2', 'integration'] as $stage) {
        ob_start();
        try {
            require BASE_PATH . '/app/Views/appraisals/valuation-methodology.php';
            $html = ob_get_contents();
        } finally { ob_end_clean(); }
        expect(str_contains($html, 'M1 Academia') && str_contains($html, 'M5 Entregable')
            && str_contains($html, 'Muestras sin asignar (1)'), 'navegación completa y muestras anteriores visibles en ' . $stage);
        if ($stage === 'components') {
            expect(str_contains($html, 'Oficina &lt;principal&gt;') && str_contains($html, 'Depósito de oficina')
                && str_contains($html, 'Integrado al inmueble principal'), 'entrada conserva inmuebles, anexos y tratamiento previo escapados');
            expect(str_contains($html, 'component=annex') && str_contains($html, 'component=office'), 'etapas enlazan la identidad de cada inmueble y anexo');
        }
        if ($stage === 'decision') expect(str_contains($html, 'Composición metodológica del predio')
            && str_contains($html, 'Ver matriz técnica de soporte') && !str_contains($html, "methodologyTab ="), 'matriz restaurada con navegación funcional sin estado Alpine eliminado');
        if ($stage === 'report') expect(str_contains($html, 'Texto consolidado del numeral 8')
            && str_contains($html, e($methodologyChapter['text'])), 'entregable recupera el texto generado del expediente');
        if (getenv('GA_RENDER_PREVIEW') === 'true') {
            $dir = __DIR__ . '/.runtime/methodology-preview';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            file_put_contents($dir . '/' . $stage . '.html', '<!doctype html><html lang="es"><meta charset="utf-8">'
                . '<meta name="viewport" content="width=device-width, initial-scale=1"><link rel="stylesheet" href="/public/assets/app.css">'
                . '<body class="bg-slate-50"><main class="mx-auto max-w-7xl p-5">' . $html . '</main></body></html>');
        }
    }
})();
