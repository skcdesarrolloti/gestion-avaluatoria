<?php
declare(strict_types=1);
(static function(): void {
    $catalog=\App\Services\SubjectFactorCapture::catalog(['property_type'=>'apartamento'],[]);
    expect(isset($catalog['floor'],$catalog['ph_elevator']) && !isset($catalog['elevator']),'piso y ascensor PH son observaciones distintas disponibles para investigar su relación');
    expect(isset($catalog['ph_pool'],$catalog['ph_gym']) && !isset($catalog['pool'],$catalog['gym']),'apartamento captura piscina y gimnasio exclusivamente como amenidades comunes');
    expect(!isset($catalog['air_conditioning'],$catalog['accessible'],$catalog['corner'],$catalog['finishes'],$catalog['service']),'catálogo nuevo simplifica apartamento sin factores retirados ni acabados repetidos');
    expect($catalog['service_room']['categories']==="No\nSí" && $catalog['finish_quality']['kind']==='ordinal','servicio binario y acabados únicos conservan su clasificación explícita');
    expect($catalog['parking']['group']===$catalog['covered_parking']['group'] && $catalog['parking']['group']===$catalog['independent_parking']['group'],'cantidad y características del parqueo comparten apartado sin sumar puntajes');
    $unit=['property_type'=>'apartamento','research_pool'=>'Sí','research_elevator'=>'Sí','subject_factors_json'=>json_encode(['pool'=>['value'=>'Sí','support'=>'Registro anterior','scale_kind'=>'binary','scale_categories'=>"No\nSí"]])];
    $legacy=\App\Services\SubjectFactorCapture::catalog($unit,[]);
    expect(isset($legacy['pool']) && $legacy['pool']['group']==='Datos anteriores · revisar alcance','dato histórico privado o ambiguo permanece sin reinterpretarse como atributo PH');
    $evidence=\App\Services\ResearchPlanEvidence::build($legacy,$unit,[['id'=>'ad','property_type'=>'apartamento','operation'=>'Venta','ph_regime'=>'si','research_pool'=>'Sí','elevator'=>'Sí','source_name'=>'Portal']],['tipo_inmueble'=>'apartamento','regimen_ph'=>'si']);
    expect($evidence['subjects']['ph_pool']==='' && $evidence['subjects']['ph_elevator']==='','sujeto no hereda automáticamente atributos ambiguos como amenidades comunes verificadas');
    $ad=reset($evidence['groups'])['ads'][0];
    expect($ad['values']['ph_pool']==='' && $ad['values']['ph_elevator']==='','publicación sin alcance común explícito queda pendiente para verificación manual');
    \App\Services\ResearchPlanInput::validateScope(['factors'=>['pool'=>[],'ph_pool'=>[]]],'apartamento','mercado');
    expect(isset(\App\Services\ResearchFactorCatalog::forType('casa')['house_pool']),'casa identifica piscina propia explícitamente');
    $entry=['value'=>'Sí','support'=>'Reglamento PH, piscina común disponible para la unidad, página 7','scale_kind'=>'binary','scale_categories'=>"No\nSí"];
    $saved=\App\Services\SubjectFactorCapture::input(['ph_pool'=>$entry],$catalog,[]);
    $unit['subject_factors_json']=json_encode($saved);
    expect(\App\Services\ResearchPlanEvidence::build($catalog,$unit,[],['tipo_inmueble'=>'apartamento'])['subjects']['ph_pool']==='Sí','amenidad común con soporte llega a investigación desde capítulo 3');
})();
