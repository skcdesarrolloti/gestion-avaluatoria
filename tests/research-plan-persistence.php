<?php
declare(strict_types=1);
(static function(PDO $app):void {
    $id=(new \App\Models\AppraisalRepository($app))->create(1);
    $repo=new \App\Models\MethodologyWorkflowRepository($app);
    $repo->save($id,1,0,'office',['method'=>'mercado','coverage'=>'Oficina propia']);
    $plan=\App\Services\ResearchPlanInput::input('{"target_ratio":10,"factors":{"bathrooms":{"decision":"investigate","definition":"Baños privados","reason":"Datos por portal"}}}');
    $repo->save($id,1,1,'office',['research_plan'=>$plan]);
    $saved=(new \App\Models\AppraisalRepository($app))->find($id,1);
    $flow=\App\Services\MethodologyWorkflow::saved($saved);
    expect($flow['office']['research_plan']['factors']['bathrooms']['decision']==='investigate' && $flow['office']['coverage']==='Oficina propia','MySQL recarga plan sin alterar método ni alcance');
    expectStatus(409,fn()=>$repo->save($id,1,1,'office',['research_plan'=>$plan]),'MySQL no sobrescribe plan con versión antigua');
    expectStatus(404,fn()=>$repo->save($id,2,2,'office',['research_plan'=>$plan]),'MySQL impide acceso al plan de otro propietario');
})($app);
