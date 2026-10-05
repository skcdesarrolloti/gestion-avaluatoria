<?php
declare(strict_types=1);
(static function():void {
    $unit=['id'=>str_repeat('b',32),'unit_kind'=>'property','unit_index'=>1,'label'=>'Oficina',
        'property_type'=>'oficina','functional_bathrooms_count'=>'2','functional_parking_spaces_count'=>'0',
        'construction_age_years'=>'18','functional_view'=>'interior','functional_finish_quality'=>'bueno'];
    $catalog=\App\Services\SubjectFactorCapture::catalog($unit,[]);
    $evidence=static fn($u)=>\App\Services\ResearchPlanEvidence::build($catalog,$u,[],['tipo_inmueble'=>'oficina']);
    $data=$evidence($unit);
    expect($data['subjects']['bathrooms']==='2' && $data['subjects']['parking']==='0' && $data['subjects']['age']==='18','investigación reutiliza características y cero confirmado de la misma unidad');
    expect($data['subjects']['view']==='Interior' && $data['subjects']['finish_quality']==='Bueno','clases compatibles del módulo 3 alimentan factores sin recaptura');
    $land=['property_type'=>'lote','front_length_m'=>'8','depth_length_m'=>'20'];
    $landCatalog=\App\Services\SubjectFactorCapture::catalog($land,[]);
    expect(\App\Services\SubjectFactorSource::resolve('frontage',$landCatalog['frontage'],$land)['value']==='8' && \App\Services\SubjectFactorSource::resolve('depth',$landCatalog['depth'],$land)['value']==='20','frente y fondo de lote se vinculan a superficies sin repetir captura');
    expect(\App\Services\SubjectFactorSource::resolve('frontage',\App\Services\ResearchFactorCatalog::forType('local')['frontage'],['property_type'=>'local','front_length_m'=>'8'])===[],'frente de lote no se supone frente comercial del local');
    $unit['functional_view']='exterior';
    expect($evidence($unit)['subjects']['view']==='','exterior genérico no se supone calle ni paisaje');
    $unit['special_attributes_json']=json_encode(['vista_oficina'=>['value'=>'mar','rating'=>'5','weight'=>'3'],
        'iluminacion_natural_oficina'=>['value'=>'bueno','rating'=>'1','weight'=>'3','notes'=>'Visita <documentada>']]);
    $unit['functional_view']='';
    $key=\App\Services\SubjectAttributeResearch::key('iluminacion_natural_oficina');
    expect($evidence($unit)['subjects']['view']==='Exterior: paisajística' && $evidence($unit)['subjects'][$key]==='Bueno','vista descriptiva y observación previa se vinculan sin convertir puntuaciones');
    $before=$evidence($unit); $unit['functional_bathrooms_count']='3';
    $after=$evidence($unit);
    expect($after['subjects']['bathrooms']==='3' && $before['subjectFactorSignatures']['bathrooms']!==$after['subjectFactorSignatures']['bathrooms'],'corregir fuente actualiza dato vinculado e invalida revisión anterior');
    $unit['special_attributes_json']=json_encode(['piso_altura'=>['value'=>'alto']]);
    expect($evidence($unit)['subjects']['floor']==='' && $evidence($unit)['subjects']['deposit']==='','no inventa piso ni cuenta depósitos desde presencia');
    $unit['subject_factors_json']=json_encode(['bathrooms'=>['value'=>'4','support'=>'Conteo confirmado',
        'scale_kind'=>'numeric','scale_categories'=>'']]);
    expect($evidence($unit)['subjects']['bathrooms']==='4','captura explícita anterior se conserva sin sobrescribir por fuente');
    $unit['subject_factors_json']=json_encode(['bathrooms'=>['value'=>'','support'=>'','scale_kind'=>'numeric','scale_categories'=>'']]);
    expect($evidence($unit)['subjects']['bathrooms']==='3','borrador vacío no obliga a volver a digitar un dato existente');
    $record=['id'=>str_repeat('a',32),'tipo_inmueble'=>'oficina']; $units=[$unit]; $subject=[]; $factorScales=[];
    $subjectActionBase='avaluos/'.$record['id'].'/bien-sujeto';
    ob_start(); require BASE_PATH.'/app/Views/appraisals/subject-factors.php'; $html=ob_get_clean();
    expect(str_contains($html,'readonly aria-readonly="true"') && str_contains($html,'bg-slate-200') && str_contains($html,'no digitado en esta sección') && !str_contains($html,'name="factors[bathrooms][value]"') && str_contains($html,'name="factors[deposit][value]"'),'dato existente sombreado y bloqueado sin enviarlo como captura nueva');
    expect(str_contains($html,'no digitado en su origen'),'dato pendiente identifica el apartado de origen sin inventar valor');
    foreach (\App\Services\ComparablePortalProfiles::types() as $type=>$label) {
        $u=['property_type'=>$type];
        foreach (\App\Support\AppraisalSpecialAttributeCatalog::groups($type) as [$label,$items]) {
            foreach ($items as $attribute=>[$label,$help,$options]) {
                $k=\App\Services\SubjectAttributeResearch::key($attribute);
                $c=\App\Services\SubjectFactorCapture::catalog($u,[]);
                if (!isset($c[$k])) continue;
                unset($options['']); $value=array_key_first($options);
                $u['special_attributes_json']=json_encode([$attribute=>['value'=>$value,'rating'=>'5','weight'=>'3']]);
                $source=\App\Services\SubjectFactorSource::resolve($k,$c[$k],$u);
                expect($source['usable'] && $source['value']===$options[$value],'reutiliza observación exacta por tipo '.$type);
                break 2;
            }
        }
    }
})();
