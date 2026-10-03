<?php
declare(strict_types=1);
(static function(): void {
    $record=['id'=>str_repeat('a',32),'regimen_ph'=>'si','tipo_inmueble'=>'oficina'];
    $units=[['id'=>'office','label'=>'Oficina <principal>','unit_kind'=>'property','property_type'=>'oficina'],
        ['id'=>'garage','label'=>'Garaje','unit_kind'=>'annex'],['id'=>'deposit','label'=>'Depósito','unit_kind'=>'annex']];
    $saved=['office'=>['method'=>'mercado','additional_methods'=>['renta']],
        'office:metodo:renta'=>['method'=>'renta'],'garage'=>['method'=>'mercado'],'deposit'=>['method'=>'mercado'],
        'inactive:metodo:costo'=>['method'=>'costo']];
    $record['methodology_workflow']=json_encode($saved);
    $components=\App\Services\MethodologyWorkflow::components($record,$units);
    $flow=\App\Services\MethodologyWorkflow::saved($record);
    $groups=\App\Services\MethodologyAcademy::groups($components,$flow);
    expect(array_keys($groups)===['mercado','renta'] && count($groups['mercado'])===3 && count($groups['renta'])===1,
        'academia agrupa tres unidades de Mercado y contraste de Renta sin incluir métodos inactivos');
    $hidden=$components+['container'=>['container'=>true,'label'=>'Casa completa','unit'=>[]],'pending'=>['label'=>'Sin método','unit'=>[]]];
    expect(\App\Services\MethodologyAcademy::groups($hidden,$flow+['container'=>['method'=>'costo']])===$groups,
        'academia excluye contenedores y decisiones pendientes');
    $subject=$phProfile=$allComparableRows=$comparableRows=[];
    $methodologyChapter=(new \App\Services\AppraisalMethodologyChapterReport())->build($record,$subject,$units);
    $guide=(new \App\Services\AppraisalComparableSearchGuide())->build($record,$subject,$units,[]);
    $componentKey='office';$selected=$flow[$componentKey];$stage='1';$method='mercado';
    ob_start();require BASE_PATH.'/app/Views/appraisals/valuation-methodology.php';$html=ob_get_clean();
    expect(substr_count($html,'Método de Mercado</h4>')===1 && str_contains($html,'Mercado · 3 recorridos')
        && str_contains($html,'Renta · 1 recorrido') && !str_contains($html,'aria-label="Unidades y anexos del predio"'),
        'vista ofrece una sola academia de Mercado con pestaña de contraste y todas sus unidades');
    expect(str_contains($html,'Leer artículo 16 completo') && str_contains($html,'Leer artículo 21 completo')
        && str_contains($html,'academy=general') && str_contains($html,'Oficina &lt;principal&gt;')
        && substr_count($html,'Verificación de datos guardados')===1,
        'artículos del método y enlace a reglas comunes conservados con revisión sólo de la unidad elegida');
    $componentKey='office:metodo:renta';$selected=$flow[$componentKey];$method='renta';
    ob_start();require BASE_PATH.'/app/Views/appraisals/valuation-methodology.php';$html=ob_get_clean();
    expect(str_contains($html,'Leer artículo 22 completo') && str_contains($html,'Leer artículo 26 completo')
        && !str_contains($html,'Método de Mercado</h4>') && !str_contains($html,'Verificación de datos guardados'),
        'contraste abre su academia de Renta sin copiar teoría ni verificaciones de Mercado');
    $_GET['academy']='general';
    ob_start();require BASE_PATH.'/app/Views/appraisals/valuation-methodology.php';$html=ob_get_clean();
    unset($_GET['academy']);
    foreach ([5,11,14,15,27,36,37] as $number) expect(str_contains($html,'Leer artículo '.$number.' completo'),
        'academia general centraliza lectura completa del artículo '.$number);
    expect(str_contains($html,'El artículo 15 reúne los métodos. El 27 explica Costo')
        && str_contains($html,'reglas especiales de PH') && !str_contains($html,'Método de Renta</h4>')
        && !str_contains($html,'Verificación de datos guardados'), 'academia general distingue reglas comunes y especiales sin duplicar teoría o controles del método');
    expect(str_contains($html,'Control para el análisis:') && str_contains($html,'no certifica cumplimiento automático'),
        'academia recuerda advertir discrepancias sin asegurar cumplimiento');
    $stage='4';
    ob_start();require BASE_PATH.'/app/Views/appraisals/valuation-methodology.php';$html=ob_get_clean();
    expect(str_contains($html,'Control normativo · no omitir discrepancias')
        && str_contains($html,'no existe todavía una verificación automática de todos los artículos'),
        'análisis de cualquier método conserva aviso normativo y límite de controles actuales');
})();
