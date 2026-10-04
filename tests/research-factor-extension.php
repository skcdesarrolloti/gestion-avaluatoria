<?php
declare(strict_types=1);
(static function():void {
    foreach (\App\Services\ComparablePortalProfiles::types() as $type=>$label) {
        $catalog=\App\Services\ResearchFactorCatalog::forType($type);
        $factors=[];
        foreach ($catalog as $key=>$factor) $factors[$key]=['kind'=>$factor['kind'],'categories'=>$factor['categories'],'definition'=>$factor['why']];
        $plan=\App\Services\ResearchPlanInput::input(json_encode(['factors'=>$factors],JSON_THROW_ON_ERROR));
        \App\Services\ResearchPlanInput::validateScope($plan,$type,'mercado');
        expect(count($plan['factors'])===count($catalog),'catálogo ampliado '.$label.' se guarda completo sin límite anterior de 20');
    }
    $office=\App\Services\ResearchFactorCatalog::forType('oficina');
    expect(isset($office['ph_elevator'],$office['finish_quality']) && !isset($office['stratum'],$office['access'],$office['air_conditioning'],$office['accessible']),'oficina conserva catálogo aprobado sin estrato ni atributos retirados');
    $warehouse=\App\Services\ResearchFactorCatalog::forType('bodega');
    expect(isset($warehouse['power'],$warehouse['floor_load'],$warehouse['loading_bays']),'bodega incorpora capacidad operativa comprobable');
    expect(\App\Services\ResearchFactorReference::portals('bodega','loading_access')!==[],'cargue de bodega tiene evidencia pública complementaria');
    expect(\App\Services\ResearchFactorReference::portals('deposito','gym')===[],'no extrapola amenidades residenciales a depósito');
    $legacy=\App\Services\ResearchFactorScaleInput::catalog($office,['view'=>['kind'=>'ordinal','categories'=>"Interior\nExterior\nSin vista"]]);
    expect(!$legacy['view']['scale_valid'] && $legacy['view']['categories']==="Interior\nExterior\nSin vista",'escala histórica incorrecta se conserva y se señala');
    $catalog=\App\Services\ResearchFactorCatalog::all();
    expect(!str_contains($catalog['view']['categories'],'Esquinera') && !str_contains($catalog['view']['categories'],'Panorámica') && isset($office['corner']) && !isset($office['landscape_view'],$office['panoramic_view']),'vista conserva cuatro clases sin duplicar factores de paisaje y panorama');
    $mixed=\App\Services\ResearchFactorScaleInput::catalog($office,['view'=>['kind'=>'categorical','categories'=>"Interior\nExterior\nPanorámica\nEsquinera\nSin vista relevante"]]);
    expect(!$mixed['view']['scale_valid'] && str_contains($mixed['view']['categories'],'Esquinera'),'mezcla histórica nominal también se conserva pero requiere revisión');
    \App\Services\ResearchPlanInput::validateScope(['factors'=>['panoramic_view'=>['decision'=>'research']]],'oficina','mercado');
    expectStatus(422,fn()=>\App\Services\ResearchPlanInput::validateScope(['factors'=>['panoramic_view'=>['decision'=>'research']]],'bodega','mercado'),'compatibilidad de vista anterior no extrapola atributos a otros tipos');
    $item=['value'=>'Sí','support'=>'Visita documentada','basis'=>str_repeat('a',64),'scale_kind'=>'binary','scale_categories'=>"No\nSí"];
    $grades=[];
    foreach ($catalog as $key=>$factor) if ($factor['kind']==='binary') $grades[$key]=$item;
    expect(count(\App\Services\ResearchAssessmentInput::input(['subject'=>$grades],$catalog)['subject'])>20,'calificaciones ampliadas conservan todos los atributos investigados');
    $rows=[['id'=>str_repeat('a',32),'property_type'=>'Oficina','operation'=>'Venta','ph_regime'=>'si','bathrooms'=>2,'private_built_m2'=>80]];
    $context=['tipo_inmueble'=>'oficina','tipo_negocio'=>'venta','regimen_ph'=>'si'];
    $old=\App\Services\ResearchPlanEvidence::build(\App\Services\ResearchFactorCatalog::forType('oficina','',true),[], $rows,$context);
    $expanded=\App\Services\ResearchPlanEvidence::build($office,[],$rows,$context);
    $historical=\App\Services\ResearchPlanEvidence::build(\App\Services\ResearchFactorCatalog::forType('oficina','',false,true),[],$rows,$context);
    expect($old['subjectSignature']===$historical['subjectSignature'] && $old['groups'][0]['signature']===$historical['groups'][0]['signature'],'catálogo histórico conserva huellas al mantener los factores anteriores');
    $rows[0]['research_corner']='Sí';
    $changed=\App\Services\ResearchPlanEvidence::build($office,[],$rows,$context);
    expect($expanded['groups'][0]['factorSignatures']['bathrooms']===$changed['groups'][0]['factorSignatures']['bathrooms'] && $expanded['groups'][0]['factorSignatures']['corner']!==$changed['groups'][0]['factorSignatures']['corner'],'huella por factor detecta cambios nuevos sin afectar otros datos');
})();
