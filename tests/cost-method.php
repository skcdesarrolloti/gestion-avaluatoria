<?php
declare(strict_types=1);
use App\Services\{CostMethodScope,CostMethodReview};
(static function(): void {
    $_SESSION ??= [];
    $_SESSION['csrf'] ??= str_repeat('a',64);
    $scope=CostMethodScope::input(['objective'=>'remaining','process'=>'replacement','basis'=>'apu',
        'direct_scope'=>' Materiales existentes ', 'depreciation_model'=>'fitto']);
    expect($scope['direct_scope']==='Materiales existentes' && !isset($scope['depreciation_model']), 'C2 conserva alcance conocido sin introducir un selector Fitto');
    expectStatus(422,fn()=>CostMethodScope::input(['objective'=>'otro']),'C2 rechaza objetos de costo ajenos al catálogo');
    expectStatus(422,fn()=>CostMethodScope::input(['indirect_scope'=>str_repeat('a',2001)]),'C2 limita explicaciones sin truncarlas silenciosamente');
    expectStatus(422,fn()=>CostMethodScope::input(['basis'=>[]]),'C2 rechaza entrada anidada inválida');
    expectStatus(422,fn()=>CostMethodScope::validate(['cost_scope'=>$scope],['method'=>'mercado']),'alcance C2 no sobrescribe selección de Mercado');
    CostMethodScope::validate(['cost_scope'=>$scope,'method'=>'costo'],['method'=>'mercado']);
    $checks=CostMethodReview::checks([],[]);
    expect(count(array_filter($checks,static fn($r)=>$r['state']==='ok'))===0,'C1 no completa construcción ni costo por defecto');
    $unit=['notes'=>'Garaje propio','construction_measure_unit'=>'m2','construction_quantity'=>'100',
        'built_area_adopted_m2'=>'15','construction_age_years'=>'0','construction_useful_life_years'=>'70',
        'conservation_result_json'=>'{"state_global_adopted":"2.5"}'];
    $checks=array_column(CostMethodReview::checks($unit,[]),null,'label');
    expect($checks['Edad adoptada']['state']==='ok' && $checks['Cantidad y unidad']['state']==='missing','C1 conserva cero de edad confirmado y exige fuente para área adoptada');
    $unit['built_area_adopted_source']='manual';
    expect(CostMethodReview::checks($unit,[])[1]['state']==='ok','C1 verifica área propia del anexo y fuente sin sumar oficina');
    $removal=array_column(CostMethodReview::checks($unit,['cost_scope'=>['objective'=>'removal','process'=>'replacement']]),null,'label');
    expect($removal['Conservación adoptada']['state']==='na' && $removal['Coherencia del presupuesto de retiro']['state']==='missing','retiro no exige depreciar presupuesto y señala proceso incompatible');
    $record=['id'=>str_repeat('c',32),'titulo'=>'Prueba costo']; $subject=[];
    $units=[['id'=>'garage','label'=>'Garaje <propio>','unit_kind'=>'annex']+$unit];
    $components=App\Services\MethodologyWorkflow::components($record,$units);
    $componentKey='garage';$selected=['method'=>'costo','cost_scope'=>$scope];$flow=['garage'=>$selected];
    $method='costo';$stage='1';$phProfile=$allComparableRows=$comparableRows=[];
    $methodologyChapter=(new App\Services\AppraisalMethodologyChapterReport())->build($record,$subject,$units);
    $guide=(new App\Services\AppraisalComparableSearchGuide())->build($record,$subject,$units,[]);
    ob_start();try{ require BASE_PATH.'/app/Views/appraisals/valuation-methodology.php';$html=ob_get_contents(); }finally{ob_end_clean();}
    expect(str_contains($html,'C1 · Academia y revisión del costo') && str_contains($html,'C3 · Insumos y presupuesto') && !str_contains($html,'M3 · Insumos y comparables'),'recorrido de costo usa C1–C5 y academia propia');
    expect(str_contains($html,'Leer artículo 30 completo') && str_contains($html,'Ross–Heideck'),'C1 mantiene consulta normativa y depreciación vigente');
    expect(str_contains($html,'assets/normativa/igac-941-anexo-costo.pdf') && str_contains($html,'Páginas 22–39'),'C1 permite consultar el apartado original completo de costo');
    expect(!preg_match('/<details[^>]*\sopen(?:\s|>)/',$html),'C1 mantiene las lecturas cerradas al entrar');
    expect(str_contains($html,'Garaje &lt;propio&gt;') && !str_contains($html,'Garaje <propio>'),'academia del costo escapa nombre de unidad');
    $selected=['method'=>'mercado']; $flow=['garage'=>$selected];
    ob_start();try{require BASE_PATH.'/app/Views/appraisals/valuation-methodology.php';$html=ob_get_contents();}finally{ob_end_clean();}
    expect(str_contains($html,'Academia C1 · Método del costo') && str_contains($html,'Leer artículo 30 completo') && !str_contains($html,'Descripción propia') && !str_contains($html,'Garaje &lt;propio&gt;'),'consulta C1 con unidad de Mercado conserva teoría sin inmueble ni controles de costo');
    expect(!str_contains($html,'aria-label="Siguiente paso"') && !str_contains($html,'aria-label="Etapas de Costo"'),'consulta teórica no envía una unidad de Mercado a insumos de costo');
    $flow['cost-deposit']=['method'=>'costo'];
    $components['cost-deposit']=['label'=>'Depósito costo','unit'=>['id'=>'cost-deposit']];
    ob_start();try{require BASE_PATH.'/app/Views/appraisals/valuation-methodology.php';$html=ob_get_contents();}finally{ob_end_clean();}
    expect(str_contains($html,'Depósito costo') && !str_contains($html,'Garaje &lt;propio&gt;') && !str_contains($html,'Descripción propia'),'C1 lista sólo unidades con Costo registrado y no revisa el componente de Mercado consultado');
    unset($components['cost-deposit']); $selected=['method'=>'costo','cost_scope'=>$scope]; $flow=['garage'=>$selected];
    $stage='2';ob_start();try{require BASE_PATH.'/app/Views/appraisals/valuation-methodology.php';$html=ob_get_contents();}finally{ob_end_clean();}
    expect(str_contains($html,'name="cost_scope[indirect_scope]"') && str_contains($html,'name="cost_scope[removal_scope]"'),'C2 ofrece directos indirectos y retiro dentro del autoguardado');
})();
