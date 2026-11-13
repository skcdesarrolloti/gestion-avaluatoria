<?php
declare(strict_types=1);
(static function(PDO $db): void {
    $ar=new \App\Models\AppraisalRepository($db);$id=$ar->create(1);$ar->ensureUnits($id,1,1,0);
    $db->prepare('UPDATE appraisals SET igac_property_units_count=1 WHERE id=?')->execute([$id]);$key=$ar->units($id,1)[0]['id'];
    $wf=new \App\Models\MethodologyWorkflowRepository($db);$wf->save($id,1,0,$key,['method'=>'renta']);
    $cr=new \App\Models\AppraisalComparableRepository($db);$rows=[];
    for($i=0;$i<5;$i++) $rows[]=['id'=>bin2hex(random_bytes(16)),'component_key'=>$key,'source_name'=>'Metrocuadrado',
        'source_url'=>$i<3?'https://example.test/oficina-en-venta/'.$i:'https://example.test/oficina-en-arriendo/'.$i,
        'price_amount'=>3500000,'price_unit'=>'canon_mensual','area_m2'=>$i===3?'':'42',
        'operation'=>$i===4?'Venta':'Arriendo','research_method'=>$i===4?'mercado':'renta','research_operation'=>$i===4?'venta':'arriendo',
        'intake_note'=>'Conservar nota','published_text'=>'Texto original íntegro','private_built_m2'=>'40'];
    $cr->saveAll($id,1,$rows,0);$selection=new \App\Models\IncomeSampleSelectionRepository($db);
    for($i=0;$i<3;$i++) $selection->save($id,['id'=>1,'name'=>'Prueba'],$key,['version'=>$i+1,'samples_version'=>1,'study_scope'=>'renta:arriendo',
        'operation'=>'exclude','sample'=>$rows[$i]['id'],'category'=>'error','reason'=>'Anuncio de venta','support'=>'Enlace original']);
    $before=$cr->forAppraisal($id,1);$flow=$ar->find($id,1)['methodology_workflow'];$sample=$rows[3]['id'];
    $repo=new \App\Models\ComparableAreaRepository($db);
    $post=['version'=>1,'study_scope'=>'renta:arriendo','areas'=>[$sample=>['area_m2'=>'98,55','area_basis'=>'Construida publicada','areas_source'=>'Anuncio del 13/11/2026']]];
    expect($repo->save($id,1,$key,$post)===2,'áreas actualiza solo los campos solicitados y su versión');
    $after=$cr->forAppraisal($id,1);$saved=$after[3];
    expect($saved['area_m2']==='98.55' && $saved['area_basis']==='Construida publicada','recarga de áreas conserva decimales y significado');
    foreach ([0,1,2,4] as $index) expect($before[$index]===$after[$index],'áreas conserva íntegra otra muestra '.$index);
    expect($flow===$ar->find($id,1)['methodology_workflow'],'áreas conserva las tres exclusiones y sus datasets sin cambios');
    expect($saved['price_amount']===$before[3]['price_amount'] && $saved['published_text']===$before[3]['published_text']
        && $saved['private_built_m2']===$before[3]['private_built_m2'] && $saved['created_at']===$before[3]['created_at'],'área no modifica canon, texto, área privada ni creación');
    expectStatus(409,fn()=>$repo->save($id,1,$key,$post),'áreas rechaza versión obsoleta');
    expectStatus(404,fn()=>$repo->save($id,2,$key,$post),'áreas rechaza propietario ajeno');
    $post['version']=2;
    expect($repo->save($id,1,$key,$post)===2,'repetir el mismo guardado confirmado no altera versión ni datos');
    $bad=$post;$bad['areas']=[$rows[4]['id']=>$post['areas'][$sample]];
    expectStatus(422,fn()=>$repo->save($id,1,$key,$bad),'áreas rechaza muestra de Mercado dentro de Renta');
    $bad=$post;$bad['areas'][$sample]['area_m2']='-5';
    expectStatus(422,fn()=>$repo->save($id,1,$key,$bad),'áreas rechaza área negativa');
    $bad=$post;$bad['areas'][$sample]['price_amount']='1';
    expectStatus(422,fn()=>$repo->save($id,1,$key,$bad),'áreas no admite cambios de precios mediante campos añadidos');
    $bad=$post;$bad['areas'][$sample]=['area_m2'=>'100','area_basis'=>'','areas_source'=>''];
    expectStatus(422,fn()=>$repo->save($id,1,$key,$bad),'área nueva requiere rótulo y fuente');
    expect($after===$cr->forAppraisal($id,1),'rechazos no borran ni modifican muestras');
    $wf->save($id,1,4,$key,['method'=>'mercado']);
    expectStatus(409,fn()=>$repo->save($id,1,$key,$post),'cambio de método invalida formulario de áreas anterior');
    $wf->save($id,1,5,$key,['method'=>'renta']);
    $GLOBALS['areaFixture']=compact('id','key','sample');
})($app);
