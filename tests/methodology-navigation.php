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
    foreach (['plan', 'components', 'decision', 'report', '1', '2', 'integration'] as $stage) {
        ob_start();
        try {
            require BASE_PATH . '/app/Views/appraisals/valuation-methodology.php';
            $html = ob_get_contents();
        } finally { ob_end_clean(); }
        expect(str_contains($html, '>Configuración</a>') && !str_contains($html, 'Herramientas y consultas del expediente')
            && !str_contains($html, 'aria-label="Etapas de Mercado"'), 'navegación común sin menús duplicados en ' . $stage);
        foreach (['Insumos','Análisis','Entregable'] as $label) expect(str_contains($html,'>'.$label.'</a>'), 'paso visible incluso sin recorrido en '.$stage.': '.$label);
        if ($stage==='plan') {
            expect(str_contains($html,'Configuración de la valoración') && !str_contains($html,'Verificación de datos guardados')
                && !str_contains($html,'Resolución 941 · Artículos completos para este paso') && str_contains($html,'academy=general'), 'configuración remite artículos generales a academia sin desplegar checklist');
            expect(str_contains($html,'aria-label="Unidades en configuración"') && str_contains($html,'Método y alcance · Oficina &lt;principal&gt;')
                && !str_contains($html,'Método y alcance · Depósito de oficina'),'configuración muestra pestañas pero sólo formularios de la unidad elegida');
        }
        if ($stage === 'components') {
            expect(str_contains($html, 'Verificación de la unidad principal y los anexos')
                && str_contains($html, 'Ver controles de Depósito de oficina') && str_contains($html, 'Sin descripción propia'),
                'resumen incluye anexo y su descripción pendiente aunque la academia visible es oficina');
            $checkPosition = strpos($html, 'Verificación de datos guardados');
            $navPosition = strpos($html, 'aria-label="Apartados académicos');
            expect($checkPosition !== false && $navPosition !== false && $checkPosition < $navPosition
                && substr_count($html, 'Verificación de datos guardados') === 1,
                'verificación de Mercado visible antes de las pestañas académicas y sin duplicados');
            expect(str_contains($html, '1. Revisión') && str_contains($html, 'Antes de continuar')
                && str_contains($html, 'Leer artículo 16 completo'), 'apartados visibles y lectura contextual de mejoras');
            expect(str_contains($html, 'orientación para Mercado') && str_contains($html, 'Leer artículo 36 completo')
                && str_contains($html, 'Leer artículo 19 completo') && !str_contains($html, '→ Academia'),
                'unidad sin método abre orientación de Mercado en sitio con PH y comparables diferenciados');
            expect(str_contains($html, 'Oficina &lt;principal&gt;') && str_contains($html, 'Depósito de oficina')
                && !str_contains($html, 'Revisar Depósito de oficina'), 'entrada conserva inmuebles, anexos y tratamiento previo escapados');
            expect(str_contains($html, 'component=annex') && str_contains($html, 'component=office'), 'etapas enlazan la identidad de cada inmueble y anexo');
            expect(!str_contains($html, 'Ver capítulo 1 · Composición') && str_contains($html, '#unidades-capitulo-3') && str_contains($html, 'section=tipologias'), 'sin retorno redundante al capítulo 1; consulta del capítulo 3 conserva destino exacto');
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
    foreach ([['lote', 'property', 'solo_terreno', 31], ['casa', 'property', 'lote_construccion', 18], ['', 'annex', 'solo_construccion', 27]] as [$type, $kind, $structure, $article]) {
        $unit = ['unit_kind' => $kind, 'method_structure' => $structure];
        $component = ['label' => 'Componente de prueba']; $orientationPh = 'no';
        ob_start(); require BASE_PATH . '/app/Views/appraisals/methodology-unit-reading.php'; $reading = ob_get_clean();
        expect(str_contains($reading, 'Leer artículo ' . $article . ' completo'), 'lectura según componente ' . $structure);
    }
    $componentKey = 'office'; $stage = '1';
    ob_start(); require BASE_PATH . '/app/Views/appraisals/valuation-methodology.php'; $html = ob_get_clean();
    expect(!str_contains($html, 'aria-label="Etapas de Mercado"') && str_contains($html, 'Una academia por método') && str_contains($html, 'academy=general') && !str_contains($html,'Verificación de datos guardados'), 'academia sin método asignado ofrece teoría sin fingir controles de una unidad');
    $componentKey = 'annex'; $stage = 'decision';
    ob_start(); require BASE_PATH . '/app/Views/appraisals/valuation-methodology.php'; $html = ob_get_clean();
    expect(str_contains($html, "componentTab: 'annex'"), 'matriz abre componente solicitado en lugar de primera unidad');
    $stage = '1';
    ob_start(); require BASE_PATH . '/app/Views/appraisals/valuation-methodology.php'; $html = ob_get_clean();
    expect(str_contains($html, 'Academia General · Resolución 941') && !str_contains($html,'Verificación de datos guardados'),
        'academia general disponible sin asignación ni tratamiento adoptado');
    $stage='plan'; $componentKey='annex';
    ob_start(); require BASE_PATH.'/app/Views/appraisals/valuation-methodology.php'; $html=ob_get_clean();
    expect(str_contains($html,'Método y alcance · Depósito de oficina') && !str_contains($html,'Método y alcance · Oficina &lt;principal&gt;'),
        'pestaña de anexo abre sus campos propios sin formularios de la oficina');
    $flow=['office'=>['method'=>'mercado'], 'annex'=>['method'=>'costo']];
    $stage='1'; $componentKey=''; $method='mercado'; $_GET['academy']='general';
    ob_start(); require BASE_PATH.'/app/Views/appraisals/valuation-methodology.php'; $html=ob_get_clean();
    expect(str_contains($html,'method=mercado&amp;stage=3&amp;component=office'), 'academia general abre insumos del recorrido de Mercado registrado');
    $method='costo';
    ob_start(); require BASE_PATH.'/app/Views/appraisals/valuation-methodology.php'; $html=ob_get_clean();
    expect(str_contains($html,'method=costo&amp;stage=3&amp;component=annex'), 'academia general conserva el método al abrir insumos de Costo');
    unset($_GET['academy']);
})();
