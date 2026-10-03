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
        expect(str_contains($html,'Copiar búsqueda de '.e($source['label'])) && str_contains($html,'Oficina &lt;principal&gt;'), 'búsqueda visible y copiable con identidad escapada '.$source['label']);
    }
    $source=['label'=>'FincaRaiz'];
    $garage=$builder->build(['tipo_inmueble'=>'parqueadero','tipo_negocio'=>'arriendo','municipio'=>'Bogotá'],['neighborhood_name'=>'Chicó'],[],[]);
    $prompt=\App\Services\ComparablePortalSearchPrompt::build($garage,$source);
    expect(str_contains($prompt['query'],'arriendo') && !str_contains($prompt['query'],'Oficina') && count($prompt['alternatives'])===3, 'anexo por renta no hereda oficina ni venta y ofrece sinónimos de parqueo');
    $unknown=\App\Services\ComparablePortalSearchPrompt::build(['type_label'=>'Tipología pendiente'], $source);
    expect($unknown['missing'] && !str_contains($unknown['query'],'Oficina'), 'búsqueda incompleta no inventa tipo o ubicación');
})();
