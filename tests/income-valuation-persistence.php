<?php
declare(strict_types=1);
require_once __DIR__.'/income-valuation-fixture.php';
(static function(PDO $db): void {
    $ar=new \App\Models\AppraisalRepository($db);$id=$ar->create(1);$ar->ensureUnits($id,1,1,0);
    $db->prepare('UPDATE appraisals SET igac_property_units_count=1 WHERE id=?')->execute([$id]);$units=$ar->units($id,1);$key=$units[0]['id'];
    $wf=new \App\Models\MethodologyWorkflowRepository($db);$wf->save($id,1,0,$key,['method'=>'renta','income_selection'=>['excluded'=>['historical-sale'=>['reason'=>'Venta, no renta']]]]);
    $context=\App\Services\IncomeValuationContext::build($ar->find($id,1),\App\Services\MethodologyWorkflow::components($ar->find($id,1),$units)[$key]);
    $repo=new \App\Models\IncomeValuationRepository($db);$actor=['id'=>1];$s=incomeValuationFixture();
    $post=['version'=>1,'study_scope'=>'renta:arriendo','context_hash'=>$context['hash'],'payload'=>json_encode($s)];
    $read=fn()=>\App\Services\MethodologyWorkflow::saved($ar->find($id,1))[$key];
    expect($repo->save($id,$actor,$key,$post)['version']===2,'valoración guarda borrador con CAS');
    expect(!isset($read()['income_valuation']['calculation']) && isset($read()['income_selection']['excluded']['historical-sale']),'borrador no calcula y conserva exclusiones anteriores');
    expectStatus(409,fn()=>$repo->save($id,$actor,$key,$post),'versión anterior no sobrescribe valoración');
    expectStatus(404,fn()=>$repo->save($id,['id'=>2],$key,$post),'propietario ajeno no accede a valoración');
    $post['version']=2;$post['operation']='calculate';$repo->save($id,$actor,$key,$post);$snapshot=$read()['income_valuation']['calculation'];
    expect($snapshot['result']['direct']['value']===120000 && $snapshot['result']['dcf']['value']>0,'cálculo explícito persistido y recuperable');
    $s['projection']['market_rent']='1100';$post['payload']=json_encode($s);$post['operation']='save';$post['version']=3;$repo->save($id,$actor,$key,$post);
    $state=$read()['income_valuation'];expect($state['calculation']===$snapshot && $snapshot['fingerprint']!==\App\Services\IncomeValuationContext::fingerprint($state,$context),'editar mantiene resultado anterior marcado no vigente');
    $post['version']=4;expectStatus(409,fn()=>$repo->save($id,$actor,$key,array_replace($post,['context_hash'=>'old'])),'cambio del contexto bloquea cálculo o guardado obsoleto');
    $wf->save($id,1,4,$key,['method'=>'mercado']);expect(!isset($read()['income_valuation']),'Mercado no recibe tasas y flujos de Renta');
    expectStatus(409,fn()=>$repo->save($id,$actor,$key,array_replace($post,['version'=>5])),'rechaza petición Renta con recorrido Mercado');
    $wf->save($id,1,5,$key,['method'=>'renta']);expect($read()['income_valuation']===$state,'volver a Renta restaura todo el escenario y snapshot');
    $wf->save($id,1,6,$key,['analysis'=>'Memoria del formulario anterior']);expect($read()['income_valuation']===$state,'clientes anteriores no eliminan configuración nueva');
    $post['version']=7;$post['operation']='calculate';$repo->save($id,$actor,$key,$post);
    $GLOBALS['incomeValuationFixture']=['id'=>$id,'key'=>$key,'context_hash'=>$context['hash'],'version'=>8,'data'=>$s];
})($app);
