<?php
declare(strict_types=1);
(static function (): void {
    $posted = ['source_name'=>'Portal', 'location_source'=>'Mapa del aviso', 'land_m2'=>'250', 'ph_special'=>'condominio'];
    $rows = \App\Services\AppraisalComparableInput::rows(['comparable_rows_json'=>json_encode([$posted])]);
    expect($rows[0]['location_source'] === 'Mapa del aviso' && $rows[0]['land_m2'] === '250' && $rows[0]['ph_special'] === 'condominio', 'entrada HTTP conserva campos nuevos de captura');
    $legacy = \App\Services\AppraisalComparableInput::rows(['comparables'=>[['source_name'=>'Portal']]])[0];
    expect(!array_key_exists('land_m2', $legacy), 'cliente anterior no transforma omisión de áreas en borrado');
    $detail = \App\Services\ComparableCaptureDetail::normalize(['land_m2' => '250,50', 'built_m2' => '100', 'private_built_m2' => '80', 'evidence_detail' => 'Captura con URL y fecha']);
    expect($detail['land_m2'] === '250.50' && $detail['private_built_m2'] === '80', 'captura conserva áreas PH y NPH sin convertirlas ni calcular valores');
    expectStatus(422, fn () => \App\Services\ComparableCaptureDetail::normalize(['land_m2' => '-20']), 'captura rechaza área negativa');
    expectStatus(422, fn () => \App\Services\ComparableCaptureDetail::normalize(['built_m2' => '1.200,50']), 'captura exige área sin separadores de miles');
    expect(\App\Services\ComparableCaptureDetail::normalize([])['private_built_m2'] === '', 'área desconocida no se convierte en cero');
    $ph = \App\Services\ComparableCaptureDetail::normalize(['ph_parking_presence'=>'si', 'ph_parking_in_price'=>'',
        'ph_parking_nature'=>'comun_exclusivo', 'ph_deposit_presence'=>'no', 'ph_deposit_count'=>'0']);
    expect($ph['ph_parking_in_price'] === '' && $ph['ph_deposit_count'] === '0', 'PH distingue precio desconocido de ausencia confirmada y conserva cero informado');
    expectStatus(422, fn () => \App\Services\ComparableCaptureDetail::normalize(['ph_deposit_count'=>'1.5']), 'PH rechaza cantidad fraccionaria');
    expectStatus(422, fn () => \App\Services\ComparableCaptureDetail::normalize(['ph_parking_nature'=>'privado']), 'PH no acepta naturaleza fuera del catálogo');
    $http = \App\Services\AppraisalComparableInput::rows(['comparable_rows_json'=>json_encode([['source_name'=>'PH'] + $ph])])[0];
    expect($http['ph_parking_nature'] === 'comun_exclusivo' && $http['ph_deposit_presence'] === 'no', 'transporte HTTP conserva componentes PH estructurados');
    $negotiation = ['price_amount'=>'$ 500.000.000', 'negotiation_discount'=>'25000000', 'negotiation_kind'=>'otorgado', 'negotiation_source'=>'Confirmación del vendedor'];
    expect(\App\Services\ComparableNegotiation::value($negotiation) === '475000000.00', 'oferta menos descuento sin descontar anexos ni modificar precio original');
    expect(\App\Services\ComparableNegotiation::value(['price_amount'=>'500']) === null, 'descuento desconocido no se convierte en cero ni calcula precio negociado');
    expect(\App\Services\ComparableNegotiation::value(['price_amount'=>'500','negotiation_discount'=>'0']) === '500.00', 'cero explícito conserva oferta sin descuento');
    expectStatus(422, fn () => \App\Services\ComparableCaptureDetail::normalize(['price_amount'=>'500','negotiation_discount'=>'501']), 'descuento superior a oferta se rechaza');
    expectStatus(422, fn () => \App\Services\ComparableCaptureDetail::normalize(['price_amount'=>'500','negotiation_discount'=>'-1']), 'descuento negativo se rechaza');
    expectStatus(422, fn () => \App\Services\ComparableCaptureDetail::normalize(['negotiation_discount'=>'10']), 'descuento sin oferta no se guarda');
    expect(!array_key_exists('negotiated_amount', \App\Services\ComparableCaptureDetail::normalize($negotiation + ['negotiated_amount'=>'1'])), 'valor derivado enviado por cliente no se persiste');
})();

(static function (): void {
    $manual=json_encode(['published:estado'=>['label'=>'Estado','value'=>'Usado','source'=>'Ficha revisada; prueba ficticia']]);
    $out=\App\Services\ComparableCaptureDetail::normalize(['analysis_manual_factors'=>$manual,'published_attributes'=>'{"Estado":""}']);
    expect(json_decode($out['analysis_manual_factors'],true)['published:estado']['value']==='Usado', 'complemento manual separado del original');
    expect(json_decode($out['published_attributes'],true)['Estado']==='', 'complemento no altera atributos publicados');
    expectStatus(422, fn()=>\App\Services\ComparableCaptureDetail::normalize(['analysis_manual_factors'=>'[]']), 'complemento rechaza lista inválida');
    expectStatus(422, fn()=>\App\Services\ComparableCaptureDetail::normalize(['analysis_manual_factors'=>json_encode(['price_amount'=>['label'=>'Precio','value'=>'1','source'=>'x']])]), 'complemento no sustituye precio original');
    expectStatus(422, fn()=>\App\Services\ComparableCaptureDetail::normalize(['analysis_manual_factors'=>json_encode(['published:estado'=>['label'=>'Estado','value'=>['x'],'source'=>'x']])]), 'complemento rechaza dato estructurado');
})();

(static function (): void {
    $stage=['at'=>'2026-10-06T18:00:00.000Z','action'=>'factors','changed'=>'','scope'=>'subject','regime'=>'si','count'=>34,'factors'=>['published:area privada','bathrooms','published:antiguedad'],'complete'=>34,'model_area'=>'published:area privada','factor_count'=>3,'simulated'=>true,'offer'=>['n'=>34,'mean'=>100,'median'=>100,'sd'=>10,'cv'=>10],'adjusted'=>['n'=>0,'mean'=>null,'median'=>null,'sd'=>null,'cv'=>null]];
    $out=\App\Services\ComparableAnalysisStatistics::normalize(json_decode(json_encode([$stage])));
    expect($out[0]['simulated']===true && $out[0]['factor_count']===3, 'historial conserva marca de simulación y conteo de tres factores');
    $stage['simulated']='si';expectStatus(422,fn()=>\App\Services\ComparableAnalysisStatistics::normalize(json_decode(json_encode([$stage]))),'historial rechaza marca de simulación adulterada');
})();
