<?php
declare(strict_types=1);
(static function():void {
    $post=['label'=>'Jacuzzi verificado de prueba','why'=>'Presencia comprobada en la unidad','unit'=>'sí/no','kind'=>'binary','categories'=>'No / Sí','group'=>'Unidad privada','types'=>['apartamento','casa']];
    $data=\App\Services\UserResearchFactorInput::input($post);
    expect($data['categories']==="No\nSí" && $data['types']===['apartamento','casa'],'nuevo binario conserva ausencia cero y presencia uno para todos sus tipos');
    expectStatus(422,fn()=>\App\Services\UserResearchFactorInput::input(array_replace($post,['types'=>['inventado']])),'no admite asignar factor a tipo inexistente');
    expectStatus(422,fn()=>\App\Services\UserResearchFactorInput::input(array_replace($post,['label'=>'Baños'])),'nombre repetido orienta a editar el existente');
    expectStatus(422,fn()=>\App\Services\UserResearchFactorInput::input(array_replace($post,['kind'=>'ordinal','categories'=>"No\nPendiente\nSí"])),'desconocido no se convierte en un nivel de jerarquía');
    expectStatus(422,fn()=>\App\Services\UserResearchFactorInput::input(array_replace($post,['kind'=>'ordinal','categories'=>"No\nno"])),'rechaza niveles repetidos aunque cambien mayúsculas');
    expectStatus(422,fn()=>\App\Services\UserResearchFactorInput::input($post,'area'),'bases de cálculo no se redefinen como atributos');
    $base=\App\Services\ResearchFactorCatalog::all()['age'];
    expectStatus(422,fn()=>\App\Services\UserResearchFactorInput::input(array_replace($post,['label'=>'Edad','unit'=>'meses','kind'=>'numeric']),'age'),'no cambia años por meses sobre calificaciones previas');
    $office=\App\Services\ResearchFactorCatalog::forType('oficina');
    expect(count(array_diff_key($office,array_flip(['area','destination'])))===13 && $office['corner']['group']==='Unidad privada','oficina queda con trece atributos aprobados y esquina privada');
    $old=['unit_kind'=>'property','property_type'=>'oficina','subject_factors_json'=>json_encode(['air_conditioning'=>['value'=>'Sí']])];
    expect(isset(\App\Services\SubjectFactorCapture::catalog($old,[])['air_conditioning']),'retirar aire acondicionado no elimina captura histórica del sujeto');
    $finish=\App\Services\ResearchFactorCatalog::all()['finish_quality'];
    expect(\App\Services\SubjectFactorCapture::code('Básico / económico',$finish)==='Código: 0' && \App\Services\SubjectFactorCapture::code('Superior / lujo',$finish)==='Código: 4' && \App\Services\SubjectFactorCapture::code('Obra gris',$finish)==='Pendiente','acabados tienen cinco niveles definidos sin confundir obra gris con calidad');
})();
