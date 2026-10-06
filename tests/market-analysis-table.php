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
})();
