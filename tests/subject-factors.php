<?php
declare(strict_types=1);
(static function(): void {
    $record=['tipo_inmueble'=>'oficina'];
    foreach (\App\Services\ComparablePortalProfiles::types() as $type=>$label) {
        $unit=['unit_kind'=>'property','property_type'=>$type];
        $capture=\App\Services\SubjectFactorCapture::catalog($unit,$record);
        $expected=array_diff_key(\App\Services\ResearchFactorCatalog::forType($type),array_flip(['area','land','built','destination']));
        expect(array_keys($capture)===array_keys($expected),'captura del sujeto cubre catálogo completo para '.$type);
    }
    $annex=\App\Services\SubjectFactorCapture::catalog(['unit_kind'=>'annex','construction_type'=>'deposito'],$record);
    expect(isset($annex['humidity']) && !isset($annex['bathrooms'],$annex['view']),'depósito usa su tipo sin heredar factores de oficina');
    expect(\App\Services\SubjectFactorCapture::catalog(['unit_kind'=>'annex','construction_type'=>''],$record)===[],'anexo sin tipo queda pendiente');
    expect(\App\Services\SubjectFactorCapture::catalog(['unit_kind'=>'property'],['tipo_inmueble'=>'oficina','factor_principal_count'=>2])===[],'unidad principal ambigua no hereda el tipo general');
    $catalog=\App\Services\SubjectFactorCapture::catalog(['property_type'=>'oficina'],$record);
    $entry=static fn($key,$value,$support='Visita, fecha y fotografía')=>['value'=>$value,'support'=>$support,'scale_kind'=>$catalog[$key]['kind'],'scale_categories'=>$catalog[$key]['categories']];
    $saved=\App\Services\SubjectFactorCapture::input(['elevator'=>$entry('elevator','No'),'view'=>$entry('view','Exterior: paisajística'),'bathrooms'=>$entry('bathrooms','0')],$catalog,[]);
    expect(\App\Services\SubjectFactorCapture::input(['generator'=>$entry('generator','','')],$catalog,[])===[],'campo nuevo vacío no sustituye información anterior del capítulo 3');
    $browserEntry=$entry('elevator','Sí'); $browserEntry['scale_categories']="No\r\nSí";
    expect(\App\Services\SubjectFactorCapture::input(['elevator'=>$browserEntry],$catalog,[])['elevator']['scale_categories']==="No\nSí",'formulario multipart conserva la misma clasificación al normalizar saltos de línea');
    expect($saved['elevator']['value']==='No' && $saved['bathrooms']['value']==='0','cero y No explícitos se conservan como datos distintos de desconocido');
    expectStatus(422,fn()=>\App\Services\SubjectFactorCapture::input(['view'=>$entry('view','Esquinera')],$catalog,[]),'sujeto rechaza clase de Vista ajena al catálogo');
    expectStatus(422,fn()=>\App\Services\SubjectFactorCapture::input(['elevator'=>$entry('elevator','Sí','')],$catalog,[]),'dato confirmado requiere soporte');
    expectStatus(422,fn()=>\App\Services\SubjectFactorCapture::input(['area'=>['value'=>'10']],$catalog,[]),'áreas no se recapturan como factores');
    $legacy=['view'=>['value'=>'Exterior','support'=>'Fuente anterior','scale_kind'=>'categorical','scale_categories'=>"Interior\nExterior"]];
    $kept=\App\Services\SubjectFactorCapture::input(['view'=>$entry('view','Exterior','Fuente anterior')],$catalog,$legacy);
    expect($kept===$legacy,'guardar otros datos no recodifica una Vista antigua');
    $unit=['property_type'=>'oficina','functional_view'=>'interior','subject_factors_json'=>json_encode($saved)];
    $evidence=\App\Services\ResearchPlanEvidence::build($catalog,$unit,[],['tipo_inmueble'=>'oficina']);
    expect($evidence['subjects']['view']==='Exterior: paisajística' && $evidence['subjects']['elevator']==='No','capítulo 8 lee la captura del capítulo 3 y supera el dato anterior');
    $unit['subject_factors_json']=json_encode($legacy);
    expect(\App\Services\ResearchPlanEvidence::build($catalog,$unit,[],['tipo_inmueble'=>'oficina'])['subjects']['view']==='','escala antigua queda pendiente sin caer al dato previo del sujeto');
    $unit['subject_factors_json']=json_encode($saved);
    $changed=$saved; $changed['view']['support']='Nueva fotografía'; $unit['subject_factors_json']=json_encode($changed);
    $updated=\App\Services\ResearchPlanEvidence::build($catalog,$unit,[],['tipo_inmueble'=>'oficina']);
    expect($updated['subjectFactorSignatures']['view']!==$evidence['subjectFactorSignatures']['view'],'cambiar soporte obliga a revisar calificación antigua de investigación');
    $units=[['id'=>str_repeat('b',32),'unit_kind'=>'property','unit_index'=>1,'label'=>'Oficina <prueba>','property_type'=>'oficina']];
    $record['id']=str_repeat('a',32); $subject=[]; $factorScales=[]; $subjectActionBase='avaluos/'.$record['id'].'/bien-sujeto';
    ob_start(); require BASE_PATH.'/app/Views/appraisals/subject-factors.php'; $html=ob_get_clean();
    expect(str_contains($html,'Exterior: paisajística') && str_contains($html,'factors[generator][value]') && str_contains($html,'0 = No') && !str_contains($html,'Oficina <prueba>'),'campos del capítulo 3 muestran escalas compartidas y escapan etiquetas');
})();
