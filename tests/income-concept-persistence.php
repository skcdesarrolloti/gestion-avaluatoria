<?php
declare(strict_types=1);
(static function() use ($app): void {
    $ar=new \App\Models\AppraisalRepository($app);
    $repo=new \App\Models\MethodologyWorkflowRepository($app);
    $id=$ar->create(1);
    $repo->save($id,1,0,'office',['method'=>'mercado','additional_methods'=>['renta']]);
    $key='office:metodo:renta';
    $repo->save($id,1,1,$key,['income_concept'=>['occupancy'=>'partial','scope'=>'part','scope_detail'=>'Zona privada','support'=>'verbal']]);
    $repo->save($id,1,2,$key,['income_concept'=>['support_detail'=>'Manifestación pendiente']]);
    $read=\App\Services\MethodologyWorkflow::saved($ar->find($id,1));
    expect($read[$key]['income_concept']['occupancy']==='partial' && $read[$key]['income_concept']['support_detail']==='Manifestación pendiente','renta persiste y recarga respuestas parciales en BD');
    expect(!isset($read['office']['income_concept']) && $read['office']['method']==='mercado','contraste renta no modifica unidad de Mercado');
    expectStatus(404,fn()=>$repo->save($id,2,3,$key,['income_concept'=>['scope'=>'whole']]),'renta rechaza propietario ajeno');
    expectStatus(409,fn()=>$repo->save($id,1,2,$key,['income_concept'=>['scope'=>'whole']]),'renta rechaza escritura simultánea');
    expectStatus(422,fn()=>$repo->save($id,1,3,'office',['income_concept'=>['scope'=>'whole']]),'renta exige método guardado en servidor');
    $repo->save($id,1,3,$key,['reason'=>'Justificación posterior']);
    $repo->save($id,1,4,'office',['additional_methods'=>[]]);
    $repo->save($id,1,5,'office',['additional_methods'=>['renta']]);
    $read=\App\Services\MethodologyWorkflow::saved($ar->find($id,1));
    expect($read[$key]['income_concept']===['occupancy'=>'partial','scope'=>'part','scope_detail'=>'Zona privada','support'=>'verbal','support_detail'=>'Manifestación pendiente'],
        'formulario anterior y desactivar/reactivar conservan las respuestas de Renta');
})();
