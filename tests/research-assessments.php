<?php
declare(strict_types=1);
(static function(): void {
    expectStatus(422,fn()=>\App\Services\ResearchPlanInput::input('{"factors":{"area":{"decision":"model"}}}'),'área no aumenta los candidatos ni la meta');
    $scale=\App\Services\ResearchFactorScaleInput::input(['factor_key'=>'view','kind'=>'ordinal','categories'=>"Sin vista\nInterior\nExterior\nPanorámica"]);
    expect($scale['kind']==='ordinal' && str_starts_with($scale['categories'],'Sin vista'),'escala ordenada guarda los niveles definidos por el analista');
    expectStatus(422,fn()=>\App\Services\ResearchFactorScaleInput::input(['factor_key'=>'bathrooms','kind'=>'ordinal','categories'=>"No\nSí"]),'no sustituye cantidades por códigos');
    $factor=['kind'=>'ordinal','categories'=>"No\nParcial\nTotal"];
    $entry=['value'=>'Parcial','support'=>'Visita, 2026-10-04, evidencia de respaldo','basis'=>str_repeat('a',64),'scale_kind'=>'ordinal','scale_categories'=>$factor['categories']];
    $data=\App\Services\ResearchAssessmentInput::input(['subject'=>['generator'=>$entry]],['generator'=>$factor]);
    expect($data['subject']['generator']['value']==='Parcial','guarda etiqueta, soporte y escala separados de anuncios');
    $entry['value']='Sí';
    expectStatus(422,fn()=>\App\Services\ResearchAssessmentInput::input(['subject'=>['generator'=>$entry]],['generator'=>$factor]),'no acepta Sí como cobertura total');
    expectStatus(422,fn()=>\App\Services\ResearchAssessmentInput::validateScope(['assessments'=>[str_repeat('b',32)=>['generator'=>$entry]]],[]),'no califica inmueble de otro expediente');
})();
