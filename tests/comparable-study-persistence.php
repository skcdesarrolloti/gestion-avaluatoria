<?php
declare(strict_types=1);
(static function() use ($app): void {
    $ar=new \App\Models\AppraisalRepository($app);$samples=new \App\Models\AppraisalComparableRepository($app);
    $workflow=new \App\Models\MethodologyWorkflowRepository($app);
    $id=$ar->create(1);$ar->ensureUnits($id,1,1,0);
    $app->prepare('UPDATE appraisals SET igac_property_units_count=1 WHERE id=?')->execute([$id]);
    $key=$ar->units($id,1)[0]['id'];
    $workflow->save($id,1,0,$key,['method'=>'mercado','analysis'=>'Memoria original de ventas']);
    $saleId=bin2hex(random_bytes(16));$rentId=bin2hex(random_bytes(16));$unknownId=bin2hex(random_bytes(16));
    $samples->saveAll($id,1,[['id'=>$saleId,'component_key'=>$key,'operation'=>'Venta','source_name'=>'Venta conservada','price_amount'=>'450000000','evidence_detail'=>'Enlace/foto original'],
        ['id'=>$rentId,'component_key'=>$key,'operation'=>'Arriendo','source_name'=>'Canon conservado','price_amount'=>'2500000'],
        ['id'=>$unknownId,'component_key'=>$key,'operation'=>'','source_name'=>'Operación pendiente']],0);
    $photos=new \App\Models\ComparablePhotoRepository($app);
    $photos->store($id,$saleId,1,['name'=>'soporte-venta.jpg','mime'=>'image/jpeg','caption'=>'Soporte original','hash'=>hash('sha256','foto-fixture'),'blob'=>'foto-fixture']);
    $photoId=$photos->listing($id,$saleId,1)[0]['id'];
    $workflow->save($id,1,1,$key,['method'=>'renta']);$record=$ar->find($id,1);$all=$samples->forAppraisal($id,1);
    $rental=\App\Services\ComparableStudy::rows($all,$key,$record);
    expect(count($rental)===1 && $rental[0]['id']===$rentId,'BD cambiar método a Renta carga solo la muestra de arriendo');
    $rental[0]['comparability_notes']='Investigación de arriendo';
    $previousPost=$_POST;
    try {
        $_POST=['matrix_complete'=>'1','version'=>1,'component_scope'=>$key,'study_scope'=>'renta:arriendo','comparables'=>$rental];
        expect(\App\Services\ComparableStudyAction::persist($ar,$samples,1,$id)===2,'BD guardado de Renta conserva colección versionada');
        $read=array_column($samples->forAppraisal($id,1),null,'id');
        expect(count($read)===3 && $read[$saleId]['price_amount']==='450000000.00' && $read[$saleId]['evidence_detail']==='Enlace/foto original' && isset($read[$unknownId]),
            'BD guardar Renta conserva ID importe soporte y muestra desconocida de otras investigaciones');
        expect($photos->photo($id,$saleId,1,$photoId)['file_blob']==='foto-fixture','BD guardar Renta conserva la foto vinculada a la venta original');
        $_POST['version']=2;$_POST['study_scope']='mercado:venta';
        expectStatus(409,fn()=>\App\Services\ComparableStudyAction::persist($ar,$samples,1,$id),'BD pantalla de venta obsoleta no guarda sobre arriendos');
        $_POST['study_scope']='renta:arriendo';$_POST['comparables']=[$read[$saleId]];
        expectStatus(422,fn()=>\App\Services\ComparableStudyAction::persist($ar,$samples,1,$id),'BD rechaza ID de venta presentado desde Renta');
        $_POST['comparables']=$rental;$_POST['comparables'][0]['operation']='Venta';
        expectStatus(422,fn()=>\App\Services\ComparableStudyAction::persist($ar,$samples,1,$id),'BD cambio de operación no mueve muestra de investigación');
        expect((int)$ar->find($id,1)['comparables_version']===2 && count($samples->forAppraisal($id,1))===3,'BD errores no alteran versión ni número de muestras');
        expectStatus(404,fn()=>\App\Services\ComparableStudyAction::persist($ar,$samples,2,$id),'BD propietario ajeno no escribe investigación');
    } finally {$_POST=$previousPost;}
    $workflow->save($id,1,2,$key,['method'=>'mercado']);$record=$ar->find($id,1);
    $sales=\App\Services\ComparableStudy::rows($samples->forAppraisal($id,1),$key,$record);
    expect(count($sales)===1 && $sales[0]['id']===$saleId && \App\Services\MethodologyWorkflow::saved($record)[$key]['analysis']==='Memoria original de ventas',
        'BD volver a Mercado restaura muestras y memoria de ventas sin canon');
    $workflow->save($id,1,3,$key,['method'=>'renta']);$record=$ar->find($id,1);
    expect(\App\Services\ComparableStudy::rows($samples->forAppraisal($id,1),$key,$record)[0]['comparability_notes']==='Investigación de arriendo','BD volver a Renta conserva sus notas sin mezclar ventas');
})();
