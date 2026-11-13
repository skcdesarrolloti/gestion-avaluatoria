<?php
declare(strict_types=1);
(static function(): void {
    $previousGet=$_GET;
    try {
        $record=['id'=>str_repeat('c',32),'tipo_inmueble'=>'oficina','regimen_ph'=>'si','municipio'=>'Cartagena','version'=>1];
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
        expect(substr_count($complete,'id="tabla-madre-83"')===1 && substr_count($complete,'id="lease-form"')===1,
            'captura bajo demanda contiene una sola matriz y una sola ficha de contrato');
        expect(substr_count($complete,'[comparability_notes]"')===77 && str_contains($complete,'Conservar soporte &lt;original&gt;'),
            'carga completa conserva las 77 muestras y sus notas escapadas');
        expect(strlen($light)<strlen($complete)/2,'entrada conceptual reduce a menos de la mitad el HTML con 77 muestras');
        $_GET=['renta_paso'=>['capture']];
        ob_start();require BASE_PATH.'/app/Views/appraisals/methodology-income-inputs.php';$invalid=ob_get_clean();
        expect(!str_contains($invalid,'id="tabla-madre-83"'),'parámetro de pestaña inválido conserva entrada ligera sin cargar muestras');
        $_GET=['renta_paso'=>'search','renta_portal'=>'Ciencuadras'];
        ob_start();require BASE_PATH.'/app/Views/appraisals/methodology-income-inputs.php';$portalHtml=ob_get_clean();
        preg_match('/data-income-state="([^"]+)"/',$portalHtml,$portalState);
        $restoredPortal=json_decode(html_entity_decode($portalState[1],ENT_QUOTES,'UTF-8'),true);
        expect($restoredPortal['incomePortal']==='Ciencuadras' && $restoredPortal['incomeTab']==='search',
            'recargar el enlace restaura portal y paso sin atribuirse otro método o reconstruir la captura');
        $_GET=['renta_portal'=>['Ciencuadras']];
        ob_start();require BASE_PATH.'/app/Views/appraisals/methodology-income-inputs.php';$invalidPortalHtml=ob_get_clean();
        preg_match('/data-income-state="([^"]+)"/',$invalidPortalHtml,$invalidState);
        $fallbackPortal=json_decode(html_entity_decode($invalidState[1],ENT_QUOTES,'UTF-8'),true);
        expect($fallbackPortal['incomePortal']==='FincaRaiz','portal inválido usa una fuente configurada, sin aceptar parámetros arbitrarios');
        foreach (['concept','search','capture','evidence','valuation','review'] as $testIncomeStep) {
            $_GET=['renta_paso'=>$testIncomeStep];
            ob_start();require BASE_PATH.'/app/Views/appraisals/methodology-income-inputs.php';$page=ob_get_clean();
            preg_match('/data-income-state="([^"]+)"/',$page,$stateMatch);
            $state=json_decode(html_entity_decode($stateMatch[1],ENT_QUOTES,'UTF-8'),true);
            expect($state['incomeTab']===$testIncomeStep && count($state['incomeSteps'])===5,'recarga restaura etapa de Renta sin crear otro recorrido: '.$testIncomeStep);
            expect(substr_count($page,'id="lease-form"')===1 && substr_count($page,'id="renta-tema-')===4
                && str_contains($page,'Ayuda / Academia de esta etapa'),'cinco etapas comparten ficha y cuatro temas únicos: '.$testIncomeStep);
        }
        expect(\App\Services\IncomeStudyNavigation::initial([],'4')==='valuation'
            && \App\Services\IncomeStudyNavigation::initial([],'5')==='review','rutas anteriores de análisis y entregable abren sus etapas nuevas');
        expect(\App\Services\IncomeStudyNavigation::initial(['renta_paso'=>['capture']],'3')==='concept','etapa inválida mantiene consulta ligera');
    } finally { $_GET=$previousGet; }
})();
