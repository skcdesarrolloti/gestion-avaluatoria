<?php
declare(strict_types=1);
(static function(PDO $db): void {
    $ar=new \App\Models\AppraisalRepository($db);$id=$ar->create(1);$ar->ensureUnits($id,1,1,0);
    $db->prepare('UPDATE appraisals SET igac_property_units_count=1 WHERE id=?')->execute([$id]);$key=$ar->units($id,1)[0]['id'];
    $wf=new \App\Models\MethodologyWorkflowRepository($db);$wf->save($id,1,0,$key,['method'=>'renta']);
    $cr=new \App\Models\AppraisalComparableRepository($db);$rows=[];
    foreach ([100,100,300] as $i=>$price) $rows[]=['id'=>bin2hex(random_bytes(16)),'price_amount'=>$price,'area_m2'=>1,'operation'=>'Arriendo',
        'price_unit'=>'canon_mensual','component_key'=>$key,'source_name'=>'Fuente <prueba>','research_method'=>'renta','research_operation'=>'arriendo'];
    $saleId=bin2hex(random_bytes(16));$rows[]=['id'=>$saleId,'price_amount'=>99999,'area_m2'=>1,'operation'=>'Venta','component_key'=>$key];
    $cr->saveAll($id,1,$rows,0);$before=$cr->forAppraisal($id,1);
    $repo=new \App\Models\IncomeSampleSelectionRepository($db);$actor=['id'=>1,'name'=>'Analista de prueba'];
    $post=['version'=>1,'samples_version'=>1,'study_scope'=>'renta:arriendo','operation'=>'draft','sample'=>$rows[2]['id'],'category'=>'different','reason'=>'Modalidad <distinta>','support'=>'Aviso 3'];
    expect($repo->save($id,$actor,$key,$post)===2,'borrador persiste sin aplicar exclusión');
    $read=fn()=>\App\Services\MethodologyWorkflow::saved($ar->find($id,1))[$key]['income_selection'];
    expect(empty($read()['events']) && $read()['drafts'][$rows[2]['id']]['reason']==='Modalidad <distinta>','recarga recupera borrador');
    $post['version']=2;$post['operation']='exclude';$repo->save($id,$actor,$key,$post);$state=$read();
    expect($state['events'][0]['before']['n']===3 && $state['events'][0]['after']['n']===2 && $state['events'][0]['after']['cv']==0,'servidor calcula y registra CV sin mezclar ventas');
    expect($before===$cr->forAppraisal($id,1),'excluir conserva matriz, fotos e IDs sin escritura a captura');
    expectStatus(409,fn()=>$repo->save($id,$actor,$key,$post),'rechaza decisión con versión obsoleta');
    expectStatus(404,fn()=>$repo->save($id,['id'=>2,'name'=>'Otro'],$key,array_replace($post,['version'=>3])),'rechaza expediente ajeno');
    expectStatus(422,fn()=>$repo->save($id,$actor,$key,array_replace($post,['version'=>3,'sample'=>$saleId])),'rechaza ID de venta en investigación de Renta');
    $rows[0]['price_amount']=120;$cr->saveAll($id,1,$rows,1);
    expectStatus(409,fn()=>$repo->save($id,$actor,$key,array_replace($post,['version'=>3,'operation'=>'restore'])),'matriz modificada invalida decisión pendiente');
    $savedEvent=$state['events'][0];$repo->save($id,$actor,$key,array_replace($post,['version'=>3,'samples_version'=>2,'operation'=>'restore','category'=>'restore','reason'=>'Datos contrastados']));
    expect(count($read()['events'])===2 && $read()['events'][0]===$savedEvent,'reincorporación conserva snapshot anterior aunque la matriz cambió');
    $wf->save($id,1,4,$key,['method'=>'mercado']);
    expect(!isset(\App\Services\MethodologyWorkflow::saved($ar->find($id,1))[$key]['income_selection']),'Mercado no recibe exclusiones de Renta');
    $wf->save($id,1,5,$key,['method'=>'renta']);
    expect(count($read()['events'])===2,'volver a Renta recupera constancia y selección');
    $wf->save($id,1,6,$key,['analysis'=>'Memoria anterior']);
    expect(count($read()['events'])===2,'formulario anterior conserva el historial nuevo');
    $GLOBALS['incomeSelectionFixture']=['id'=>$id,'key'=>$key,'sample'=>$rows[2]['id']];
})($app);
