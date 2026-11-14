<?php
declare(strict_types=1);
require_once __DIR__.'/income-valuation-fixture.php';
(static function() use ($app): void {
    $ar=new \App\Models\AppraisalRepository($app);$id=$ar->create(1);$ar->ensureUnits($id,1,1,0);
    $app->prepare('UPDATE appraisals SET igac_property_units_count=1 WHERE id=?')->execute([$id]);$units=$ar->units($id,1);$key=$units[0]['id'];
    (new \App\Models\MethodologyWorkflowRepository($app))->save($id,1,0,$key,['method'=>'renta','income_selection'=>['excluded'=>['sale'=>['reason'=>'Venta conservada']]]]);
    $context=\App\Services\IncomeValuationContext::build($ar->find($id,1),['unit'=>$units[0]]);
    $repo=new \App\Models\IncomeValuationRepository($app);$s=incomeValuationFixture();
    $post=['version'=>1,'study_scope'=>'renta:arriendo','context_hash'=>$context['hash'],'payload'=>json_encode($s),'operation'=>'calculate'];
    $repo->save($id,['id'=>1],$key,$post);
    $read=fn()=>\App\Services\MethodologyWorkflow::saved($ar->find($id,1))[$key];$original=$read()['income_valuation']['calculation'];
    $s['projection']=array_replace($s['projection'],['income_source'=>'scenario','scenario_rent'=>'2941590.06','months'=>'60','cost_review'=>'']);
    $post=array_replace($post,['version'=>2,'payload'=>json_encode($s),'operation'=>'preview']);$repo->save($id,['id'=>1],$key,$post);
    $state=$read()['income_valuation'];
    expect(count($state['flow_preview']['result']['months'])===60 && $state['flow_preview']['result']['months'][0]['cash']===null,'preliminar persiste sesenta meses con neto pendiente');
    expect($state['calculation']===$original && isset($read()['income_selection']['excluded']['sale']),'previsualizar conserva cálculo anterior y exclusiones');
    expect($state['flow_preview']['fingerprint']===\App\Services\IncomeValuationContext::fingerprint($state,$context),'huella preliminar no se incluye a sí misma');
    expectStatus(409,fn()=>$repo->save($id,['id'=>1],$key,$post),'preliminar rechaza versión obsoleta');
    expectStatus(404,fn()=>$repo->save($id,['id'=>2],$key,$post),'preliminar rechaza propietario ajeno');
    $s['projection']['growth']='5';$post=array_replace($post,['version'=>3,'payload'=>json_encode($s),'operation'=>'save']);$repo->save($id,['id'=>1],$key,$post);
    $saved=$read()['income_valuation'];expect($saved['flow_preview']===$state['flow_preview'] && $saved['flow_preview']['fingerprint']!==\App\Services\IncomeValuationContext::fingerprint($saved,$context),'edición conserva tabla previa marcada anterior');
})();
