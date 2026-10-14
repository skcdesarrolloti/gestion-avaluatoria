<?php
(function (PDO $db): void {
    $repo=new \App\Models\AppraisalRepository($db);$samples=new \App\Models\AppraisalComparableRepository($db);$id=$repo->create(1);
    $fixture=require __DIR__.'/course-execution-fixture.php';
    $row=['id'=>str_repeat('a',32),'source_name'=>'Prueba','price_amount'=>'100','area_m2'=>'1','analysis_course_execution'=>$fixture];
    $samples->saveAll($id,1,[$row],0);
    $reloaded=$samples->forAppraisal($id,1);
    $saved=json_decode($reloaded[0]['analysis_course_execution'],true,8,JSON_THROW_ON_ERROR);
    expect($saved['result']['summary']['median']===150 && $saved['step']===3,'recarga BD recupera medidas y cuartiles sin una nueva ejecución');
    $corrected=$row;$corrected['price_amount']='200';$corrected['area_m2']='2';$corrected['source_updates']='Corrección: oferta100→200; área1→2; fuente verificada';
    $samples->saveAll($id,1,[$corrected],1);
    $correctedReload=$samples->forAppraisal($id,1)[0];
    expect((float)$correctedReload['price_amount']===200.0 && (float)$correctedReload['area_m2']===2.0 && str_contains($correctedReload['source_updates'],'oferta100'),'corrección guarda precio, área y evidencia del valor anterior');
    expect($correctedReload['analysis_course_execution']===$reloaded[0]['analysis_course_execution'],'corrección conserva ejecución previa hasta actualizar estadística');
    expectStatus(409,fn()=>$samples->saveAll($id,1,[$row],0),'conflicto conserva ejecución anterior');
    expectStatus(409,fn()=>$samples->saveAll($id,2,[$row],1),'propietario ajeno no sobrescribe ejecución');
    expect($samples->forAppraisal($id,1)[0]['analysis_course_execution']===$reloaded[0]['analysis_course_execution'],'solicitudes rechazadas conservan ejecución guardada');
})($app);
