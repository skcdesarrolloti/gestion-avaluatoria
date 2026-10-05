<?php
declare(strict_types=1);
(static function():void {
    foreach (\App\Services\ComparablePortalProfiles::types() as $type=>$label) {
        $catalog=\App\Services\ResearchFactorCatalog::forInvestigation($type);
        $links=[]; foreach ($catalog as $factor) $links=array_merge($links,$factor['legacy_keys'] ?? []);
        foreach (\App\Support\AppraisalSpecialAttributeCatalog::groups($type) as [$group,$items]) foreach ($items as $key=>$item)
            if (!in_array($key,\App\Services\SubjectAttributeResearch::METADATA,true)) expect(in_array($key,$links,true),'investigación incluye atributo previo '.$type.' / '.$key);
    }
    $key=\App\Services\SubjectAttributeResearch::key('iluminacion_natural_oficina');
    $catalog=\App\Services\SubjectFactorCapture::catalog(['property_type'=>'oficina'],[]);
    $unit=['property_type'=>'oficina','special_attributes_json'=>json_encode(['iluminacion_natural_oficina'=>['value'=>'bueno','rating'=>'4','weight'=>'3','notes'=>'Inspección documentada']])];
    $previous=\App\Services\SubjectAttributeResearch::previous($unit,$catalog[$key]);
    expect($previous[0]['observed']==='Bueno' && $previous[0]['rating']==='4' && $previous[0]['weight']==='3','integración muestra dato, calificación y peso anteriores intactos');
    $evidence=\App\Services\ResearchPlanEvidence::build($catalog,$unit,[],['tipo_inmueble'=>'oficina']);
    expect($evidence['subjects'][$key]==='Bueno','observación anterior se reutiliza como clase sin convertir calificación ni peso');
    expect($catalog['view']['legacy_keys']===['vista_oficina'] && !isset($catalog[\App\Services\SubjectAttributeResearch::key('vista_oficina')]),'vista se vincula a la escala aprobada sin duplicar un factor equivalente');
    $plan=\App\Services\ResearchPlanInput::input(json_encode(['factors'=>[$key=>['decision'=>'model','kind'=>'categorical','categories'=>$catalog[$key]['categories'],'definition'=>$catalog[$key]['why']]]]));
    \App\Services\ResearchPlanInput::validateScope($plan,'oficina','mercado');
    expectStatus(422,fn()=>\App\Services\ResearchPlanInput::validateScope($plan,'lote','mercado'),'un atributo específico previo no invade otro tipo de inmueble');
    $entry=['value'=>'Bueno','support'=>'Visita, fecha y fotografía','scale_kind'=>$catalog[$key]['kind'],'scale_categories'=>$catalog[$key]['categories']];
    $saved=\App\Services\SubjectFactorCapture::input([$key=>$entry],$catalog,[]);
    $unit['subject_factors_json']=json_encode($saved);
    expect(\App\Services\ResearchPlanEvidence::build($catalog,$unit,[],['tipo_inmueble'=>'oficina'])['subjects'][$key]==='Bueno','atributo verificado alimenta referencia del módulo ocho con su clase y soporte');
    $unit+=['id'=>str_repeat('b',32),'unit_kind'=>'property','unit_index'=>1,'label'=>'Oficina de prueba','igac_typology_hint'=>''];
    $units=[$unit]; $record=['id'=>str_repeat('a',32),'tipo_inmueble'=>'oficina'];
    $subjectActionBase='avaluos/'.$record['id'].'/bien-sujeto';
    $specialAttributeOptions=\App\Support\AppraisalSpecialAttributeCatalog::selectOptions();
    ob_start(); require BASE_PATH.'/app/Views/appraisals/subject-attributes.php'; $html=ob_get_clean();
    expect(str_contains($html,'value="4" selected>4 Favorable') && str_contains($html,'value="3" selected>Alto'),'recargar calificación anterior selecciona correctamente rating y peso numéricos guardados');
})();
