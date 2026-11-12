<?php
declare(strict_types=1);
(static function(): void {
    $record=['tipo_negocio'=>'venta','methodology_workflow'=>json_encode(['office'=>['method'=>'mercado']])];
    $rental=$record;$rental['methodology_workflow']=json_encode(['office'=>['method'=>'renta']]);
    $sale=['id'=>str_repeat('1',32),'component_key'=>'office','operation'=>'Venta','source_name'=>'Venta original','price_amount'=>'450000000','evidence_detail'=>'Soporte venta'];
    $rent=['id'=>str_repeat('2',32),'component_key'=>'office','operation'=>'Arriendo','source_name'=>'Arriendo original','price_amount'=>'2500000'];
    $unknown=['id'=>str_repeat('3',32),'component_key'=>'office','operation'=>'','source_name'=>'Sin operación'];
    $rows=[$sale,$rent,$unknown];
    expect(\App\Services\ComparableStudy::rows($rows,'office',[])===[],'unidad todavía sin método puede configurarse sin atribuirle investigación');
    expect(\App\Services\ComparableStudy::rows($rows,'office',$record)===[$sale],'Mercado muestra solo venta explícita de la misma unidad');
    expect(\App\Services\ComparableStudy::rows($rows,'office',$rental)===[$rent],'cambiar a Renta no reutiliza ventas ni clasifica operaciones desconocidas');
    expect(\App\Services\ComparableStudy::counts([$rent,$unknown],'office',$record)===['mercado'=>0,'renta'=>1],'conteos y continuación no atribuyen arriendos a Mercado al cambiar el selector');
    $changed=array_replace($rent,['comparability_notes'=>'Nota del arriendo']);
    $merged=\App\Services\ComparableStudy::merge($rows,[$changed],'office',$rental);
    expect(count($merged)===3 && $merged[0]===$sale && $merged[1]===$unknown,'guardar Renta conserva íntegramente venta y muestra sin clasificar');
    $savedRent=$merged[2];
    expect($savedRent['research_method']==='renta' && $savedRent['research_operation']==='arriendo','servidor fija pertenencia al estudio en JSON existente');
    expect(\App\Services\ComparableStudy::rows($merged,'office',$record)===[$sale],'volver a Mercado recupera ventas sin arriendos');
    expectStatus(422,fn()=>\App\Services\ComparableStudy::merge($rows,[$sale],'office',$rental),'Renta no puede apropiarse del ID de venta');
    expectStatus(422,fn()=>\App\Services\ComparableStudy::merge($rows,[array_replace($rent,['operation'=>'Venta'])],'office',$rental),'Renta rechaza cambiar el canon por una venta');
    expectStatus(409,fn()=>\App\Services\ComparableStudy::assertRequest($rental,'office',['study_scope'=>'mercado:venta']),'pantalla antigua de Mercado no guarda después de seleccionar Renta');
    expectStatus(409,fn()=>\App\Services\ComparableStudy::assertRequest($rental,'office',[]),'pantalla anterior a la separación debe recargarse antes de guardar');
    $bankSale=array_replace($sale,['component_key'=>'']);
    expectStatus(422,fn()=>\App\Services\ComparableStudy::merge([$bankSale],[array_replace($bankSale,['operation'=>'Arriendo'])],'',$record),'banco tampoco convierte una venta conocida en arriendo');
    $rentalMarket=$record;$rentalMarket['tipo_negocio']='arriendo';
    expect(\App\Services\ComparableStudy::rows([$savedRent],'office',$rentalMarket)===[],'mismo tipo de operación tampoco mezcla métodos distintos');
    $flow=\App\Services\MethodologyAlternativeMethods::save([], 'office',['method'=>'mercado','analysis'=>'Memoria de venta','research_plan'=>['factors'=>[],'study_scope'=>'mercado:venta']]);
    expectStatus(422,fn()=>\App\Services\MethodologyAlternativeMethods::save($flow,'office',['method'=>'renta','analysis'=>'Memoria de ventas']),
        'una petición combinada no traslada la memoria de Mercado al cambiar a Renta');
    $flow=\App\Services\MethodologyAlternativeMethods::save($flow,'office',['method'=>'renta']);
    expect(!isset($flow['office']['analysis'],$flow['office']['research_plan']),'cambiar método separa memoria y plan anteriores');
    $flow=\App\Services\MethodologyAlternativeMethods::save($flow,'office',['income_concept'=>['occupancy'=>'vacant'],'research_plan'=>['factors'=>[],'study_scope'=>'renta:arriendo']]);
    $flow=\App\Services\MethodologyAlternativeMethods::save($flow,'office',['method'=>'mercado']);
    expect($flow['office']['analysis']==='Memoria de venta' && $flow['office']['research_plan']['study_scope']==='mercado:venta' && !isset($flow['office']['income_concept']),
        'regresar a Mercado restaura solo su investigación');
    $flow=\App\Services\MethodologyAlternativeMethods::save($flow,'office',['method'=>'renta']);
    expect($flow['office']['income_concept']['occupancy']==='vacant' && $flow['office']['research_plan']['study_scope']==='renta:arriendo',
        'regresar a Renta restaura su captura conceptual y su plan');
    $legacy=['method'=>'renta','research_plan'=>['factors'=>['old'=>[]]]];
    expect(!isset(\App\Services\MethodStudyState::visible($legacy)['research_plan']) && isset($legacy['research_plan']),
        'plan histórico sin origen no se atribuye a Renta y se conserva en el expediente');
})();
