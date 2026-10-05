<?php
declare(strict_types=1);
(static function (): void {
    $a = str_repeat('a',32); $b = str_repeat('b',32);
    $rows = [['id'=>$a,'property_group'=>$a,'intake_state'=>'selected_pending','price_amount'=>100],
        ['id'=>$b,'property_group'=>$a,'intake_state'=>'selected_pending','price_amount'=>110],
        ['id'=>str_repeat('c',32),'intake_state'=>'review','price_amount'=>120]];
    $groups=\App\Services\ComparableIntake::groups($rows,true);
    expect(count($groups)===1 && count($groups[$a])===2 && array_column($groups[$a],'price_amount')===[100,110], 'un inmueble seleccionado conserva dos ofertas sin mezclar precios');
    $rows[1]['intake_state']='review';
    expect(\App\Services\ComparableIntake::groups($rows,true)===[], 'estado inconsistente del grupo no pasa automáticamente a análisis');
    expect(\App\Services\MarketComponentReview::build($rows)['groups']===[], 'anuncios vinculados no alimentan estadísticas como muestras independientes');
    expectStatus(422,fn()=>\App\Services\ComparableCaptureDetail::normalize(['property_group'=>'ajeno']), 'identidad de inmueble rechaza identificador inválido');
    expectStatus(422,fn()=>\App\Services\ComparableCaptureDetail::normalize(['intake_state'=>'aprobado']), 'captura no admite aprobación inventada');
    expectStatus(422,fn()=>\App\Services\ComparableCaptureDetail::normalize(['location_verification'=>'exact','latitude'=>10,'longitude'=>-75]), 'ubicación manual requiere fuente y soporte');
    $detail=\App\Services\ComparableCaptureDetail::normalize(['location_verification'=>'approximate','latitude'=>10,'longitude'=>-75,'location_source'=>'Prueba','verification_detail'=>'Analista, fecha, soporte']);
    expect($detail['location_verification']==='approximate', 'verificación aproximada permanece distinta de punto exacto');
    $facts=\App\Services\ComparablePublishedDetails::parse('Área privada construida: 85,5 m². 2 parqueaderos y 1 depósito.');
    expect($facts['private_built_m2']==='85.5' && $facts['parking_spaces']==='2' && $facts['ph_deposit_count']==='1' && !isset($facts['ph_parking_nature']), 'lector PHP captura datos explícitos sin inferir naturaleza jurídica');
    $full=\App\Services\ComparablePublishedDetails::parse("Baños: 3\nAscensor: Sí\nJacuzzi privado: No\n".str_repeat('Descripción original ',150));
    $saved=\App\Services\ComparableCaptureDetail::normalize($full);
    expect($full['bathrooms']==='3' && $full['elevator']==='Sí', 'lector recoge baños y ascensor explícitos');
    foreach (['2 Baños395 m²'=>'2','1 Baños60 m²'=>'1','Baños: 0'=>'0'] as $text=>$expected) {
        expect((\App\Services\ComparablePublishedDetails::parse($text)['bathrooms'] ?? null)===$expected,'lector distingue cantidad de baños del área: '.$text);
    }
    expect(!isset(\App\Services\ComparablePublishedDetails::parse('Baños 104 m²')['bathrooms']), 'lector no toma área sin cantidad de baños como conteo');
    $description=\App\Services\ComparablePublishedDetails::parse('Oficina con 2 baños y 4 garajes. Servicios básicos de agua y electricidad. Vista panorámica. El edificio cuenta con ascensor y acceso para discapacitados.');
    expect($description['bathrooms']==='2' && $description['parking_spaces']==='4' && $description['elevator']==='Sí'
        && str_contains($description['published_attributes'],'Acceso para discapacitados'), 'descripción conserva atributos explícitos y cantidades separados de la calificación');
    expect(json_decode($saved['published_attributes'],true)['Jacuzzi privado']==='No' && mb_strlen($saved['published_text'])>1600, 'captura conserva atributo no catalogado y texto amplio');
    expectStatus(422,fn()=>\App\Services\ComparableCaptureDetail::normalize(['published_attributes'=>'{"dato":{"objeto":"no"}}']), 'atributos originales rechazan estructuras no previstas');
})();
