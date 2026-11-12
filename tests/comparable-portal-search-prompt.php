<?php
declare(strict_types=1);
(static function(): void {
    $builder=new \App\Services\AppraisalComparableSearchGuide();
    $guide=$builder->build(['tipo_inmueble'=>'oficina','tipo_negocio'=>'venta','municipio'=>'Cartagena'],
        ['neighborhood_name'=>'Bocagrande'],[],[]);
    foreach ($guide['source_search']['portal_sources'] as $source) {
        $prompt=\App\Services\ComparablePortalSearchPrompt::build($guide,$source);
        expect(str_contains($prompt['query'],'Oficina venta Bocagrande Cartagena') && !$prompt['missing'], 'búsqueda por unidad y portal '.$source['label']);
        expect(str_starts_with($prompt['google'],'site:') && str_contains($prompt['google'],$prompt['query']), 'alternativa restringe fuente '.$source['label']);
        $componentLabel='Oficina <principal>';
        ob_start();require BASE_PATH.'/app/Views/appraisals/methodology-portal-search-prompt.php';$html=ob_get_clean();
        $copyLabel=$prompt['location_only'] ? 'Copiar ubicación para ' : 'Copiar búsqueda de ';
        expect(str_contains($html,$copyLabel.e($source['label'])) && str_contains($html,'Oficina &lt;principal&gt;'), 'búsqueda visible y copiable con identidad escapada '.$source['label']);
        if ($prompt['location_only']) {
            expect($prompt['input']==='Bocagrande' && str_contains($html,'sugerencia de Barrio'), 'FincaRaíz copia barrio sin convertir tipo y operación en palabras clave');
        } else expect($prompt['input']===$prompt['query'], 'conserva buscador textual de la otra fuente '.$source['label']);
    }
    $source=['label'=>'FincaRaiz'];
    $rentGuide=$builder->build(['tipo_inmueble'=>'oficina','tipo_negocio'=>'arriendo','municipio'=>'Cartagena de Indias'],['neighborhood_name'=>'Bocagrande'],[],[]);
    $rentSource=$rentGuide['source_search']['portal_sources'][0];
    $rentPrompt=\App\Services\ComparablePortalSearchPrompt::build($rentGuide,$rentSource);
    expect($rentPrompt['open_url']==='https://www.fincaraiz.com.co/arriendo/oficinas/bocagrande/cartagena'
        && $rentSource['kind']==='Filtros del portal' && $rentPrompt['input']==='Bocagrande','Renta abre resultados directos del barrio sin usar búsqueda de venta ni convertir la ubicación en una frase');
    $guideBefore=$guide;$sourceBefore=$source;$guide=$rentGuide;$source=$rentSource;
    ob_start();require BASE_PATH.'/app/Views/appraisals/methodology-portal-search-prompt.php';$rentHtml=ob_get_clean();
    expect(str_contains($rentHtml,'Abrir búsqueda en FincaRaiz') && str_contains($rentHtml,'El botón de copiar no abre otra página')
        && str_contains($rentHtml,'/arriendo/oficinas/bocagrande/cartagena'),'apertura y copia tienen acciones distintas visibles para el analista');
    $guide=$guideBefore;$source=$sourceBefore;
    $garage=$builder->build(['tipo_inmueble'=>'parqueadero','tipo_negocio'=>'arriendo','municipio'=>'Bogotá'],['neighborhood_name'=>'Chicó'],[],[]);
    $prompt=\App\Services\ComparablePortalSearchPrompt::build($garage,$source);
    expect(str_contains($prompt['query'],'arriendo') && !str_contains($prompt['query'],'Oficina') && count($prompt['alternatives'])===3, 'anexo por renta no hereda oficina ni venta y ofrece sinónimos de parqueo');
    $unknown=\App\Services\ComparablePortalSearchPrompt::build(['type_label'=>'Tipología pendiente'], $source);
    expect($unknown['missing'] && !str_contains($unknown['query'],'Oficina'), 'búsqueda incompleta no inventa tipo o ubicación');
    expect($unknown['input']==='', 'ubicación incompleta queda vacía');
    $withoutZone=$guide; $withoutZone['source_search']['neighborhood']='';
    $fallback=\App\Services\ComparablePortalSearchPrompt::build($withoutZone,$source);
    expect($fallback['input']==='Cartagena', 'sin barrio copia sólo ciudad para elegir sugerencia');
})();
