<?php
declare(strict_types=1);
(static function(): void {
    $input=['occupancy'=>'partial','scope'=>'part','support'=>'verbal',
        'scope_detail'=>' Zona <privada> ', 'support_detail'=>'Pendiente de corroborar'];
    $concept=\App\Services\IncomeConcept::input($input);
    expect($concept['scope_detail']==='Zona <privada>' && count($concept)===5,'renta conserva texto original y borrador parcial sin inventar datos');
    expect(\App\Services\IncomeConcept::input(['occupancy'=>''])===['occupancy'=>''],'renta acepta respuesta vacía sin convertirla en desocupación');
    foreach (['occupancy'=>['array'], 'scope'=>'inventado', 'support_detail'=>str_repeat('a',1001)] as $key=>$value) {
        try { \App\Services\IncomeConcept::input([$key=>$value]); throw new RuntimeException('No rechazó dato inválido'); }
        catch (\App\Core\HttpException $error) { expect($error->status===422 && isset($error->errors['income_concept['.$key.']']),'renta devuelve error asociado al campo '.$key); }
    }
    expectStatus(422,fn()=>\App\Services\IncomeConcept::input(['rate'=>'6']),'renta no admite variables numéricas o campos nuevos por petición');
    expectStatus(422,fn()=>\App\Services\IncomeConcept::validate(['income_concept'=>$concept],['method'=>'mercado']),'renta impide guardar conceptos en Mercado');
    $record=['id'=>str_repeat('a',32),'methodology_version'=>8,'lease_version'=>8,'version'=>2];
    $unitId=str_repeat('b',32);$units=[['id'=>$unitId,'label'=>'Oficina <principal>','unit_kind'=>'property','property_type'=>'oficina']];
    $componentKey=$unitId.':metodo:renta';$components=[$componentKey=>['unit'=>$units[0],'label'=>'Oficina <principal>']];$componentLabel='Oficina <principal>';
    $selected=['method'=>'renta','income_concept'=>$concept];
    $record['methodology_workflow']=json_encode([$componentKey=>$selected]);
    $basePath='avaluos/'.$record['id'].'/metodologia-valuatoria';
    $flowUrl=static fn($stage)=>url($basePath.'?stage='.$stage.'&component='.$componentKey);
    ob_start();require BASE_PATH.'/app/Views/appraisals/methodology-income-concept.php';$html=ob_get_clean();
    expect(str_contains($html,'name="lease[start_date]"') && str_contains($html,'name="lease[adjustment_rule]"')
        && str_contains($html,'Consultar ayudas y soportes en Academia') && str_contains($html,'name="lease[expenses]"'),
        'ficha reúne vigencia, incrementos y condiciones con acceso a Academia');
    expect(str_contains($html,'Zona &lt;privada&gt;') && !str_contains($html,'Zona <privada>') && str_contains($html,'Oficina &lt;principal&gt;'),'renta escapa textos y unidad');
    expect(str_contains($html,'Parcialmente arrendado') && !str_contains($html,'name="income_concept[occupancy]"') && str_contains($html,'value="8"') && str_contains($html,'data-module-autosave'),'renta restaura selección y versión para autoguardado');
    expect(!str_contains($html,'type="number"') && !str_contains($html,'name="rate"'),'bloque conceptual no captura cálculos');
    $errorKey=\App\Services\IncomeConceptAction::flashKey($record['id'],$componentKey);
    \App\Core\Session::flash($errorKey,json_encode(['values'=>['scope'=>'no_valida','scope_detail'=>'Nota pendiente'],
        'errors'=>['income_concept[scope]'=>'Selecciona una opción.'],'message'=>'Revisa','version'=>3]));
    ob_start();require BASE_PATH.'/app/Views/appraisals/methodology-income-concept.php';$html=ob_get_clean();
    expect(str_contains($html,'value="no_valida" selected') && str_contains($html,'Nota pendiente')
        && str_contains($html,'aria-invalid="true"') && str_contains($html,'value="3"'),'fallback conserva valores, error de campo y versión obsoleta sin reintento silencioso');
    expect(str_starts_with(file_get_contents(BASE_PATH.'/public/assets/normativa/renta-orientacion-v1.pdf'),'%PDF-'),'resumen completo empaquetado con el aplicativo');
    ob_start();require BASE_PATH.'/app/Views/appraisals/methodology-income-academy.php';$academy=ob_get_clean();
    expect(substr_count($academy,'id="renta-tema-')===4 && str_contains($academy,'Qué debes reunir o aclarar')
        && str_contains($academy,'Cómo se conecta con el estudio:'),'academia Renta incorpora cuatro temas didácticos conectados con la captura');
    expect(str_contains($academy,'ivs-104-data-inputs/archivo') && str_contains($academy,'ivs-106-reporting/archivo')
        && str_contains($academy,'nts-m01-metodologias-valuacion') && str_contains($academy,'gts-e02-valuacion-arrendamiento')
        && str_contains($academy,'igac/documento/resolucion-igac-941-2026'),'temas de Renta enlazan soportes reales IGAC, IVS y sectoriales dentro del aplicativo');
    expect(str_contains($academy,'no citas literales') && str_contains($academy,'Edición 2016-02-12')
        && str_contains($academy,'No mezcles conclusiones con bases diferentes.')
        && !str_contains($academy,'name="income_concept['),'academia distingue síntesis y ediciones sin crear captura ni mezclar bases');
})();
