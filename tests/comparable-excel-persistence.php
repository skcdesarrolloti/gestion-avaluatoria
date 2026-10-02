<?php
(function (PDO $db): void {
    $repo=new \App\Models\AppraisalRepository($db);
    $samples=new \App\Models\AppraisalComparableRepository($db);
    $id=$repo->create(1);$row=['id'=>bin2hex(random_bytes(16)),'source_name'=>'Prueba Excel',
        'price_amount'=>'500','price_unit'=>'precio_total','operation'=>'Venta','negotiation_discount'=>'25'];
    $stamp=['scope'=>'oficina','filename'=>'completado.xlsx','saved_at'=>'2026-10-02T18:00:00+00:00','rows'=>1];
    $samples->saveAll($id,1,[$row],0,$stamp);
    $first=\App\Services\ComparableExcelHistory::latest($repo->find($id,1),'oficina');
    expect($first['version']===1 && $first['filename']==='completado.xlsx','Excel guarda fecha archivo y versión en misma transacción');
    expect(str_contains(\App\Services\ComparableExcelHistory::display($first),'02/10/2026 13:00:00'),'fecha Excel se presenta en hora de Colombia');
    expect(\App\Services\ComparableExcelHistory::latest($repo->find($id,1),'garaje')===[],'fecha de otra unidad no se atribuye al garaje');
    $samples->saveAll($id,1,[$row],1);
    expect(\App\Services\ComparableExcelHistory::latest($repo->find($id,1),'oficina')===$first,'edición manual no altera fecha de última importación');
    expectStatus(409,fn()=>$samples->saveAll($id,1,[$row],0,$stamp),'Excel obsoleto no modifica fecha ni muestras');
    expectStatus(422,fn()=>$samples->saveAll($id,1,[array_replace($row,['negotiation_discount'=>'501'])],2,$stamp),'Excel inválido revierte importación');
    expect((int)$repo->find($id,1)['comparables_version']===2 && \App\Services\ComparableExcelHistory::latest($repo->find($id,1),'oficina')===$first,'error conserva colección versión e historial previo');
    expectStatus(409,fn()=>$samples->saveAll($id,2,[$row],2,$stamp),'importación no cambia expediente ajeno');
    $samples->saveAll($id,1,[$row],2,array_replace($stamp,['scope'=>'garaje','filename'=>'garaje.xlsx']));
    expect(\App\Services\ComparableExcelHistory::latest($repo->find($id,1),'oficina')===$first,'registrar otra colección conserva fecha de la oficina');
    expect(\App\Services\ComparableExcelHistory::latest($repo->find($id,1),'garaje')['version']===3,'cada colección registra su última carga');
})($app);
