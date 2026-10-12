<?php
(function (PDO $db): void {
    $repo=new \App\Models\AppraisalRepository($db);$samples=new \App\Models\AppraisalComparableRepository($db);$id=$repo->create(1);
    $fixture=require __DIR__.'/course-execution-fixture.php';
    $row=['id'=>str_repeat('a',32),'source_name'=>'Prueba','price_amount'=>'100','area_m2'=>'1','analysis_course_execution'=>$fixture];
    $samples->saveAll($id,1,[$row],0);
    $reloaded=$samples->forAppraisal($id,1);
    $saved=json_decode($reloaded[0]['analysis_course_execution'],true,8,JSON_THROW_ON_ERROR);
    expect($saved['result']['summary']['median']===150 && $saved['step']===3,'recarga BD recupera medidas y cuartiles sin una nueva ejecución');
    expectStatus(409,fn()=>$samples->saveAll($id,1,[$row],0),'conflicto conserva ejecución anterior');
    expectStatus(409,fn()=>$samples->saveAll($id,2,[$row],1),'propietario ajeno no sobrescribe ejecución');
    expect($samples->forAppraisal($id,1)[0]['analysis_course_execution']===$reloaded[0]['analysis_course_execution'],'solicitudes rechazadas conservan ejecución guardada');
})($app);
