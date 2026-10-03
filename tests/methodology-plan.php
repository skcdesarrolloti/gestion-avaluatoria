<?php
declare(strict_types=1);
use App\Services\{MethodologyWorkflow,MethodologyValuationPlan,MethodologyStepArticles,ComparableSearchContext};
(static function(): void {
    $unit=['id'=>'house','label'=>'Casa <prueba>','unit_kind'=>'property','property_type'=>'casa','method_structure'=>'lote_construccion',
        'area_adopted_m2'=>'250','area_adopted_source'=>'Escritura','built_area_adopted_m2'=>'120','built_area_adopted_source'=>'Plano'];
    $record=['id'=>str_repeat('a',32),'regimen_ph'=>'no','methodology_workflow'=>json_encode(['house'=>['plan_parts'=>'land_building','method'=>'mercado','conclusion'=>'Valor anterior completo'],
        'house:terreno'=>['method'=>'mercado','reason'=>'Muestras de lotes'],'house:construccion'=>['method'=>'costo','reason'=>'Reposición sustentada']])];
    $components=MethodologyWorkflow::components($record,[$unit]);
    expect(array_keys($components)===['house','house:terreno','house:construccion'],'plan crea partes estables de una unidad sólo tras una decisión guardada');
    expect($components['house']['unit']===$unit && $components['house:terreno']['unit']['id']==='house' && $components['house:construccion']['unit']['id']==='house','separar valoración conserva ficha original e identidad física y jurídica');
    expect(array_keys(MethodologyValuationPlan::working($components))===['house:terreno','house:construccion'],'unidad agrupadora queda fuera de recorridos y consolidación para evitar doble conteo');
    expectStatus(422,fn()=>MethodologyValuationPlan::validateWorkKey('house',$components),'no se capturan o guardan análisis nuevos en unidad completa desagregada');
    expectStatus(422,fn()=>MethodologyWorkflow::validateKey('other:terreno',$components),'partes ajenas no habilitan acceso ni escritura');
    expectStatus(422,fn()=>MethodologyWorkflow::input(['plan_parts'=>'anything']),'organización rechaza valores inventados');
    expectStatus(422,fn()=>MethodologyWorkflow::input(['plan_parts'=>[]]),'organización rechaza entrada anidada');
    expectStatus(422,fn()=>MethodologyValuationPlan::validateParts(['regimen_ph'=>'si'],$components['house'],'land_building'),'no se crea terreno independiente desde alcance PH');
    $phChanged=MethodologyWorkflow::components(array_replace($record,['regimen_ph'=>'si']),[$unit]);
    expect(!empty($phChanged['house']['plan_blocked']) && MethodologyValuationPlan::working($phChanged)===[], 'cambio posterior a PH suspende partes de suelo sin borrar memoria ni reactivar conclusión completa');
    expectStatus(422,fn()=>MethodologyValuationPlan::validateParts(['regimen_ph'=>'no'],['unit'=>['unit_kind'=>'annex']],'land_building'),'desagregación no inventa suelo propio para un anexo');
    $land=ComparableSearchContext::forMethod($record,[$unit],'house:terreno','mercado');
    expect($land['tipo_inmueble']==='lote' && $land['estructura_metodo']==='solo_terreno','búsqueda de parte terreno busca lotes, no ofertas de casa completa');
    $report=App\Services\MethodologyWorkflowReport::text($record,[$unit]);
    expect(str_contains($report,'Terreno') && str_contains($report,'Construcción') && !str_contains($report,'Valor anterior completo'),'informe no duplica resultado anterior de casa con resultados de sus partes');
    $whole=$record; $saved=MethodologyWorkflow::saved($whole); $saved['house']['plan_parts']='whole'; $whole['methodology_workflow']=json_encode($saved);
    expect(array_keys(MethodologyWorkflow::components($whole,[$unit]))===['house'] && isset(MethodologyWorkflow::saved($whole)['house:terreno']),'volver a alcance completo conserva la memoria de partes anteriores sin activarlas');
    $check=(new App\Services\MarketSubjectChecklist())->build($record,[],$components['house:terreno']['unit'],[]);
    $checks=array_column($check['rows'],null,'key');
    expect($checks['work_state']['state']==='na' && $checks['conservation']['state']==='na' && $checks['area']['value']==='250 m²','terreno revisa su área propia sin exigir conservación de construcción');
    expect(MethodologyStepArticles::forStep('3','mercado',false)[1]===[16,17,18,19]
        && MethodologyStepArticles::forStep('4','costo',false)[1]===[29,30],'lecturas por paso distinguen captura de mercado de depreciación de costo');
    foreach (['mercado','costo','renta','residual'] as $m) foreach (['plan','1','2','3','4','5','integration'] as $s)
        foreach (MethodologyStepArticles::forStep($s,$m,true)[1] as $n) if (App\Services\Resolution941Reading::article($n)===null) throw new RuntimeException('Falta artículo '.$n);
    expect(true,'todas las lecturas por método y etapa tienen texto completo disponible');
})();
