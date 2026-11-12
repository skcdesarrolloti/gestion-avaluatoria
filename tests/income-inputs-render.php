<?php
declare(strict_types=1);
(static function(): void {
    $previousGet=$_GET;
    try {
        $record=['id'=>str_repeat('c',32),'tipo_inmueble'=>'oficina','regimen_ph'=>'si','municipio'=>'Cartagena'];
        $units=[['id'=>'office','label'=>'Oficina Principal','unit_kind'=>'property','property_type'=>'oficina']];
        $components=\App\Services\MethodologyWorkflow::components($record,$units);
        $componentKey=$sourceUnitKey='office';$componentLabel='Oficina Principal';$method='renta';
        $selected=['method'=>'renta'];$flow=['office'=>$selected];$methods=\App\Services\MethodologyWorkflow::METHODS;
        $subject=['neighborhood_name'=>'Chambacú'];$phProfile=$factorScales=$marketNeighborhoods=[];
        $guide=(new \App\Services\AppraisalComparableSearchGuide())->build(
            \App\Services\ComparableSearchContext::forMethod($record,$units,$componentKey,$method),$subject,$units,[]);
        $basePath='avaluos/'.$record['id'].'/metodologia-valuatoria';
        $flowUrl=static fn($stage)=>url($basePath.'?method=renta&stage='.$stage.'&component=office');
        $methodologyDecision=[];$allComparableRows=$comparableRows=[];
        for($index=0;$index<77;$index++) $comparableRows[]=['id'=>str_pad(dechex($index+1),32,'0',STR_PAD_LEFT),
            'component_key'=>'office','source_name'=>'FincaRaiz','source_url'=>'https://example.test/arriendo/'.$index,
            'operation'=>'Arriendo','property_type'=>'Oficina','price_amount'=>'2500000','area_m2'=>'80',
            'comparability_notes'=>'Conservar soporte <original>'];
        $allComparableRows=$comparableRows;
        $_GET=[];
        ob_start();require BASE_PATH.'/app/Views/appraisals/methodology-income-inputs.php';$light=ob_get_clean();
        expect(!str_contains($light,'id="tabla-madre-83"') && !str_contains($light,'https://example.test/arriendo/'),
            'entrada a Renta no renderiza muestras ni sus controles antes de solicitarlas');
        expect(str_contains($light,'Arriendo') && str_contains($light,'Chambacú') && str_contains($light,'renta_paso=capture'),
            'preparación ligera conserva criterios de arriendo y ruta autenticada de carga');
        $_GET=['renta_paso'=>'capture'];
        ob_start();require BASE_PATH.'/app/Views/appraisals/methodology-income-inputs.php';$complete=ob_get_clean();
        expect(substr_count($complete,'id="tabla-madre-83"')===1 && substr_count($complete,'id="income-concept-form"')===1,
            'captura bajo demanda contiene una sola matriz y un solo formulario conceptual');
        expect(substr_count($complete,'[comparability_notes]"')===77 && str_contains($complete,'Conservar soporte &lt;original&gt;'),
            'carga completa conserva las 77 muestras y sus notas escapadas');
        expect(strlen($light)<strlen($complete)/2,'entrada conceptual reduce a menos de la mitad el HTML con 77 muestras');
        $_GET=['renta_paso'=>['capture']];
        ob_start();require BASE_PATH.'/app/Views/appraisals/methodology-income-inputs.php';$invalid=ob_get_clean();
        expect(!str_contains($invalid,'id="tabla-madre-83"'),'parámetro de pestaña inválido conserva entrada ligera sin cargar muestras');
    } finally { $_GET=$previousGet; }
})();
