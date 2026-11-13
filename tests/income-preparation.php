<?php
declare(strict_types=1);
(static function(): void {
    $unit=['id'=>'a','unit_kind'=>'property','label'=>'Oficina <A>','property_type'=>'oficina',
        'area_private_m2'=>'85','surface_source'=>'Escritura p. 4'];
    $record=['id'=>str_repeat('a',32),'tipo_inmueble'=>'oficina','methodology_version'=>1];
    $subject=['current_occupation'=>'arrendado','current_use'=>'oficina','subject_reference_date'=>'2026-11-12'];
    $component=['label'=>'Oficina <A>','unit'=>$unit];
    $build=static fn($items,$part=[])=>\App\Services\IncomeSubjectContext::build($record,$subject,$items,$component+$part,[]);
    $context=$build([$unit]);
    expect($context['occupancy']==='rented_unspecified','ocupación global inequívoca se consulta sin inferir que está totalmente arrendado');
    expect($build([$unit,['id'=>'b','unit_kind'=>'property']])['occupancy']==='','ocupación global no se atribuye a unidades múltiples');
    expect($build([$unit],['part'=>'construccion'])['occupancy']==='','ocupación de inmueble completo no se atribuye a una parte');
    expect($context['rows'][3]['value']==='','uso observado no prueba ni rellena uso aprobado');
    expect($context['rows'][5]['value']==='85' && $context['rows'][5]['support']==='Escritura p. 4','área privada conserva su magnitud y soporte sin convertirla en área arrendable');
    $subject['current_occupation']='mixta';
    expect(\App\Services\IncomeSubjectContext::build($record,$subject,[$unit],$component,[])['occupancy']==='',
        'ocupación mixta no se convierte automáticamente en parcialmente arrendado');
    $values=['rental_scope'=>'different','lease_term'=>' Vence según cláusula <privada> ',
        'cost_ph'=>'tenant','history_occupancy'=>'none','review_notes'=>'Sin historia no equivale a cero'];
    $clean=\App\Services\IncomeConcept::input($values);
    expect($clean['lease_term']==='Vence según cláusula <privada>' && $clean['history_occupancy']==='none','nuevos antecedentes conservan texto y ausencia de soportes sin porcentajes');
    expectStatus(422,fn()=>\App\Services\IncomeConcept::input(['cost_ph'=>'5%']),'gastos rechazan porcentajes donde se pide responsable');
    expectStatus(422,fn()=>\App\Services\IncomeConcept::input(['history_payment'=>['yes']]),'historial rechaza estructuras no admitidas');
    expectStatus(422,fn()=>\App\Services\IncomeConcept::input(['lease_term'=>str_repeat('a',1001)]),'notas nuevas conservan límite de longitud');
    $subject['current_occupation']='propio_uso';$unit['occupation_details']=json_encode(['occupancy'=>'owner']);$units=[$unit];$component['unit']=$unit;$components=['a'=>$component];$record['version']=1;
    $componentKey='a';$componentLabel=$component['label'];$selected=['method'=>'renta'];$phProfile=[];
    $basePath='avaluos/'.$record['id'].'/metodologia-valuatoria';$flowUrl=static fn($s)=>url($basePath.'?stage='.$s.'&component=a');
    ob_start();require BASE_PATH.'/app/Views/appraisals/methodology-income-concept.php';$html=ob_get_clean();
    expect(str_contains($html,'Ocupado por el propietario') && !str_contains($html,'name="income_concept[occupancy]"'),
        'ocupación del origen se muestra sin pedir ni enviar una segunda copia');
    expect(str_contains($html,'Oficina &lt;A&gt;') && str_contains($html,'Escritura p. 4'),'contexto vivo escapa identidad y muestra evidencia original');
    $selected['income_concept']=['occupancy'=>'vacant'];$record['methodology_workflow']=json_encode(['a'=>$selected]);
    ob_start();require BASE_PATH.'/app/Views/appraisals/methodology-income-concept.php';$html=ob_get_clean();
    expect(str_contains($html,'Desocupado') && str_contains($html,'Ocupado por el propietario'),
        'respuesta contradictoria de Renta y referencia del sujeto se conservan visibles, sin sobrescritura');
})();
