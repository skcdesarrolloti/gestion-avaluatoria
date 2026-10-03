<?php
declare(strict_types=1);
(static function() use ($app): void {
    $ar=new App\Models\AppraisalRepository($app);
    $id=$ar->create(1);
    $ar->savePreclassification($id,1,$ar->find($id,1)['version'],['igac_category'=>'Residencial','igac_typology_hint'=>'',
        'igac_property_units_count'=>1,'igac_annex_units_count'=>0]);
    $app->prepare('UPDATE appraisals SET regimen_ph=? WHERE id=?')->execute(['no',$id]);
    $beforeUnits=$ar->units($id,1); $key=$beforeUnits[0]['id'];
    $workflow=new App\Models\MethodologyWorkflowRepository($app);
    $workflow->save($id,1,0,$key,['method'=>'mercado','conclusion'=>'Conclusión anterior conservada']);
    $cr=new App\Models\AppraisalComparableRepository($app);
    $sampleId=bin2hex(random_bytes(16));
    $cr->saveAll($id,1,[['id'=>$sampleId,'source_name'=>'Muestra casa completa','component_key'=>$key,'evidence_detail'=>'Fuente conservada']],0);
    $oldRows=$cr->forAppraisal($id,1);
    $workflow->save($id,1,1,$key,['plan_parts'=>'land_building']);
    $record=$ar->find($id,1); $components=App\Services\MethodologyWorkflow::components($record,$ar->units($id,1));
    expect(isset($components[$key.':terreno'],$components[$key.':construccion']) && $ar->units($id,1)===$beforeUnits,'desagregación persiste y recarga sin crear unidades ni modificar capítulos 1 y 3');
    expect($cr->forAppraisal($id,1)===$oldRows && App\Services\MethodologyComparableScope::rows($oldRows,$key.':terreno')===[],'crear partes no mueve ni copia muestras o soporte anterior');
    $workflow->save($id,1,2,$key.':terreno',['method'=>'mercado','coverage'=>'Sólo suelo']);
    $workflow->save($id,1,3,$key.':construccion',['method'=>'costo','coverage'=>'Sólo construcción']);
    $saved=App\Services\MethodologyWorkflow::saved($ar->find($id,1));
    expect($saved[$key]['conclusion']==='Conclusión anterior conservada' && $saved[$key.':terreno']['method']==='mercado'
        && $saved[$key.':construccion']['method']==='costo','métodos distintos de casa y partes conservan alcance y memoria sin sobrescribir');
    expectStatus(409,fn()=>$workflow->save($id,1,3,$key,['plan_parts'=>'whole']),'plan obsoleto no desactiva partes guardadas por otro equipo');
    expectStatus(404,fn()=>$workflow->save($id,2,4,$key,['plan_parts'=>'whole']),'organización rechaza escritura de otro propietario');
    $workflow->save($id,1,4,$key,['plan_parts'=>'whole']);
    expect(!isset(App\Services\MethodologyWorkflow::components($ar->find($id,1),$beforeUnits)[$key.':terreno'])
        && isset(App\Services\MethodologyWorkflow::saved($ar->find($id,1))[$key.':construccion']), 'desactivar desagregación conserva historial de partes para reactivación explícita');
    $workflow->save($id,1,5,$key,['plan_parts'=>'land_building']);
    expect(App\Services\MethodologyWorkflow::saved($ar->find($id,1))[$key.':construccion']['method']==='costo' && $cr->forAppraisal($id,1)===$oldRows,'reactivar partes recupera métodos propios y preserva banco original');
})();
