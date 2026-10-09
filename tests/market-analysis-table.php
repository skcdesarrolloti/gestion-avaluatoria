<?php
(function(): void {
    $comparableRows=[
        ['id'=>str_repeat('a',32),'property_group'=>'pair','capture_confirmation'=>'confirmed','research_primary'=>'no','price_amount'=>'600000000','area_m2'=>'100','negotiation_discount'=>'120'],
        ['id'=>str_repeat('b',32),'property_group'=>'pair','capture_confirmation'=>'confirmed','research_primary'=>'si','price_amount'=>'500000000','area_m2'=>'100','negotiation_discount'=>''],
        ['id'=>str_repeat('c',32),'capture_confirmation'=>'confirmed','price_amount'=>'400000000','area_m2'=>'80','negotiation_discount'=>''],
        ['id'=>str_repeat('d',32),'capture_confirmation'=>'excluded','price_amount'=>'900','area_m2'=>'1','negotiation_discount'=>''],
    ];
    $basePath='/avaluos/test';$componentKey='unit';$record=['comparables_version'=>3];
    ob_start();require BASE_PATH.'/app/Views/appraisals/methodology-intake-analysis.php';$html=ob_get_clean();
    expect(str_contains($html,'Tabla de análisis · 2 inmuebles'),'análisis recibe confirmados sin exigir selección estadística anterior');
    expect(str_contains($html,':value="analysisDiscounts[\''.str_repeat('b',32).'\']"'),'descuento editable se guarda en ficha principal confirmada');
    expect(substr_count($html,'name="comparables[1][negotiation_discount]"')===1,'descuento principal tiene un solo control para persistencia');
    expect(str_contains($html,'name="comparables[0][negotiation_discount]" value="120"'),'anuncio secundario conserva descuento original');
    expect(str_contains($html,'name="comparables[3][id]"'),'anuncio excluido sigue en matriz sin eliminarse');
    expect(\App\Services\ComparableNegotiation::value(['price_amount'=>'500000000','negotiation_discount'=>'50000000.0000'])==='450000000.00','servidor valida y recalcula importe equivalente a diez por ciento');
    $selection='{"selected":["bathrooms","floor_level"],"threshold":50}';
    $detail=\App\Services\ComparableCaptureDetail::normalize(['analysis_factor_selection'=>$selection,'ph_regime_source'=>'Reglamento consultado por analista']);
    expect(json_decode($detail['analysis_factor_selection'],true)['selected']===['bathrooms','floor_level'],'selección del analista se valida como dato persistente');
    expect($detail['ph_regime_source']==='Reglamento consultado por analista','soporte PH se conserva separado de la fuente de áreas');
    expectStatus(422,fn()=>\App\Services\ComparableCaptureDetail::normalize(['analysis_factor_selection'=>'{"selected":[],"threshold":101}']),'umbral fuera de rango rechaza selección sin guardar');
    expectStatus(422,fn()=>\App\Services\ComparableCaptureDetail::normalize(['analysis_factor_selection'=>'{"selected":[{}],"threshold":50}']),'factor con estructura inválida no llega a persistencia');
    $scope=\App\Services\ComparableCaptureDetail::normalize(['analysis_factor_selection'=>'{"selected":[],"threshold":50,"scope":"subject"}']);
    expect(json_decode($scope['analysis_factor_selection'],true)['scope']==='subject','filtro del régimen se conserva con selección');
    expectStatus(422,fn()=>\App\Services\ComparableCaptureDetail::normalize(['analysis_factor_selection'=>'{"selected":[],"threshold":50,"scope":"delete"}']),'filtro inválido se rechaza');
    $hint=static fn(array $row)=>\App\Services\ComparableRegimeSuggestion::hint($row);
    expect($hint(['published_attributes'=>'{"Área Privada":"60 m2"}'])['regime']==='si','área privada publicada sugiere PH');
    expect($hint(['published_text'=>'Área privada: 43,5 m²'])['regime']==='si','texto de área privada genera indicio');
    expect($hint(['published_text'=>'Área privada: 0 m²'])['regime']==='','área cero no sugiere régimen');
    expect($hint(['published_attributes'=>'{"Área Privada":"No publicado","Ascensor":"Sí"}'])['regime']==='','ascensor y área ausente no confirman PH');
    expect($hint(['published_attributes'=>'{"Ascensor":"No"}'])['regime']==='','sin ascensor no implica No PH');
    expect($hint(['published_text'=>'No está sometido a propiedad horizontal'])['regime']==='no','negación no se lee como PH afirmativo');
    expect($hint(['published_text'=>'Sometido al régimen de propiedad horizontal'])['regime']==='si','régimen PH anunciado genera indicio');
    expect($hint(['published_attributes'=>'{"Propiedad horizontal":"No","Área Privada":"40 m2"}'])['regime']==='','indicios contradictorios no clasifican');
    expect(str_contains($html,'Muestras para trabajar'),'filtro visible explica depuración reversible');
    expect(str_contains($html,'name="comparables[2][ph_regime]"'),'campos de filas ocultas permanecen en formulario');
    $view=\App\Services\ComparableCaptureDetail::normalize(['analysis_factor_selection'=>'{"selected":["bathrooms"],"threshold":50,"view":"clean"}']);
    expect(json_decode($view['analysis_factor_selection'],true)['view']==='clean','vista depurada se recupera como decisión guardada');
    expectStatus(422,fn()=>\App\Services\ComparableCaptureDetail::normalize(['analysis_factor_selection'=>'{"selected":[],"threshold":50,"view":"execute"}']),'vista no permitida se rechaza');
    expect(str_contains($html,'1. Información recogida') && str_contains($html,'2. Depurar muestras') && str_contains($html,'4. Resultado depurado'),'análisis separa pasos recogida selección y resultado');
    expect(str_contains($html,'Aplicar depuración de muestras') && str_contains($html,'Depuración de factores por parte del analista'),'muestras y factores tienen acciones separadas');
    $regime=\App\Services\ComparableCaptureDetail::normalize(['analysis_factor_selection'=>'{"selected":[],"threshold":50,"view":"regime","scope":"all","applied_scope":"subject","regime_applied":true}']);
    expect(json_decode($regime['analysis_factor_selection'],true)['applied_scope']==='subject','régimen aplicado persiste separado del propuesto');
    $review=['id'=>str_repeat('a',32),'reason'=>'unknown','at'=>'2026-10-06T15:00:00.000Z','restored_at'=>'2026-10-06T15:01:00.000Z'];
    $history=\App\Services\ComparableCaptureDetail::normalize(['analysis_factor_selection'=>json_encode(['selected'=>[],'threshold'=>50,'review'=>[$review]])]);
    expect(json_decode($history['analysis_factor_selection'],true)['review'][0]===$review,'historial conserva motivo y fechas de retiro y reincorporación');
    expectStatus(422,fn()=>\App\Services\ComparableCaptureDetail::normalize(['analysis_factor_selection'=>'{"selected":[],"threshold":50,"review":{}}']),'historial rechaza objetos en lugar de lista');
    expectStatus(422,fn()=>\App\Services\ComparableCaptureDetail::normalize(['analysis_factor_selection'=>json_encode(['selected'=>[],'threshold'=>50,'review'=>[array_replace($review,['id'=>'foreign'])]])]),'historial rechaza identificadores inválidos');
    expectStatus(422,fn()=>\App\Services\ComparableCaptureDetail::normalize(['analysis_factor_selection'=>json_encode(['selected'=>[],'threshold'=>50,'review'=>[array_replace($review,['reason'=>'delete'])]])]),'historial rechaza motivos inventados');
    $applied=\App\Services\ComparableCaptureDetail::normalize(['analysis_factor_selection'=>'{"selected":["bathrooms"],"applied":["floor_level"],"threshold":50,"view":"result"}']);
    expect(json_decode($applied['analysis_factor_selection'],true)['applied']===['floor_level'],'factores aplicados se conservan separados del borrador');
    expectStatus(422,fn()=>\App\Services\ComparableCaptureDetail::normalize(['analysis_factor_selection'=>'{"selected":[],"applied":{},"threshold":50}']),'factores aplicados rechazan objeto');
    expectStatus(422,fn()=>\App\Services\ComparableCaptureDetail::normalize(['analysis_factor_selection'=>'{"selected":[],"applied":[{}],"threshold":50}']),'factor aplicado rechaza estructura anidada');
    $stat=['at'=>'2026-10-06T15:00:00.000Z','action'=>'filter','changed'=>'','scope'=>'subject','regime'=>'si','count'=>34,'factors'=>[],'complete'=>null,'offer'=>['n'=>34,'mean'=>100,'median'=>100,'sd'=>10,'cv'=>10],'adjusted'=>['n'=>0,'mean'=>null,'median'=>null,'sd'=>null,'cv'=>null]];
    $historic=\App\Services\ComparableCaptureDetail::normalize(['analysis_factor_selection'=>json_encode(['selected'=>[],'threshold'=>50,'statistics'=>[$stat]])]);
    expect(json_decode($historic['analysis_factor_selection'],true)['statistics'][0]===$stat,'etapa estadística conserva valores congelados y número de muestras');
    expectStatus(422,fn()=>\App\Services\ComparableCaptureDetail::normalize(['analysis_factor_selection'=>json_encode(['selected'=>[],'threshold'=>50,'statistics'=>[array_replace($stat,['count'=>-1])]])]),'etapa rechaza cantidades negativas');
    expectStatus(422,fn()=>\App\Services\ComparableCaptureDetail::normalize(['analysis_factor_selection'=>json_encode(['selected'=>[],'threshold'=>50,'statistics'=>[array_replace($stat,['offer'=>['n'=>35,'mean'=>100,'median'=>100,'sd'=>10,'cv'=>10]])]])]),'estadístico rechaza más valores que muestras');
    $areaStat=array_replace($stat,['factors'=>['published:area privada','published:antiguedad','published:estado'],'complete'=>20,'model_area'=>'published:area privada','factor_count'=>3]);
    $areaHistory=\App\Services\ComparableCaptureDetail::normalize(['analysis_factor_selection'=>json_encode(['selected'=>[],'threshold'=>50,'statistics'=>[$stat,$areaStat]])]);
    expect(json_decode($areaHistory['analysis_factor_selection'],true)['statistics'][1]['factor_count']===3,'historial conserva tres factores con área privada sin añadir área publicada');
    expect(!isset(json_decode($areaHistory['analysis_factor_selection'],true)['statistics'][0]['factor_count']),'historial anterior conserva su interpretación original sin reescribir conteos');
    expectStatus(422,fn()=>\App\Services\ComparableCaptureDetail::normalize(['analysis_factor_selection'=>json_encode(['selected'=>[],'threshold'=>50,'statistics'=>[array_replace($areaStat,['factor_count'=>4])]])]),'servidor rechaza doble conteo de área privada');
    $comparableRows=[];
    $componentKey='annex';$componentLabel='<script>Depósito</script>';
    $units=[['id'=>'annex','unit_kind'=>'annex','unit_index'=>1,'property_type'=>'parqueadero']];
    $record=['tipo_inmueble'=>'oficina','finalidad'=>'judicial','value_date'=>'2026-10-09'];
    ob_start();require BASE_PATH.'/app/Views/appraisals/methodology-intake-analysis.php';$empty=ob_get_clean();
    expect(str_contains($empty,'1.1 Objetivo y unidad de análisis') && !str_contains($empty,'<form'),'preparación se consulta sin muestras ni formulario vacío que sobrescriba la matriz');
    expect(str_contains($empty,'Parqueadero') && !str_contains($empty,'<dd>Oficina</dd>'),'preparación usa el tipo del anexo en vez del tipo global del encargo');
    expect(str_contains($empty,'&lt;script&gt;Depósito&lt;/script&gt;') && !str_contains($empty,'<script>'),'contexto del componente se presenta escapado');
    expect(str_contains($empty,'Información pendiente:') && str_contains($empty,'Base de valor') && str_contains($empty,'2026-10-09'),'preparación distingue campos ausentes de fecha registrada');
})();
