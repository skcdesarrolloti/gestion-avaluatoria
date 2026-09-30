<?php
declare(strict_types=1);
(static function (): void {
    $url=\App\Services\FincaraizAreaSearch::url('Bocagrande',2);
    expect($url==='https://www.fincaraiz.com.co/venta/oficinas/bocagrande/cartagena/pagina2','busqueda barrio y pagina mantienen oficina venta Cartagena');
    expect(str_contains(\App\Services\FincaraizAreaSearch::url('Castillo Grande'),'/castillogrande/'),'alias de barrio normalizado');
    try { \App\Services\FincaraizAreaSearch::url('Bocagrande',11); $rejected=false; } catch(\InvalidArgumentException) { $rejected=true; }
    expect($rejected,'paginacion acotada');
    $listing=['@type'=>['RealEstateListing','Product'],'url'=>'https://www.fincaraiz.com.co/oficina-en-venta-en-bocagrande-cartagena/123','name'=>'Oficina en Venta en Bocagrande, Cartagena','offers'=>['price'=>450000000,'priceCurrency'=>'COP'],'mainEntity'=>['floorSize'=>['value'=>46,'unitCode'=>'MTK']]];
    $html='<link rel="canonical" href="'.$url.'"><script type="application/ld+json">'.json_encode(['@type'=>'CollectionPage','mainEntity'=>['itemListElement'=>[$listing,$listing]]]).'</script><a href="/venta/oficinas/bocagrande/cartagena/pagina3">3</a>';
    $service=new \App\Services\FincaraizAreaSearch(); $result=$service->parse($html,$url,2);
    expect(count($result['results'])===1 && $result['has_next'],'listado deduplica enlaces y detecta pagina siguiente real');
    expect($result['results'][0]['row']['neighborhood']==='Bocagrande' && $result['results'][0]['row']['area_m2']==='46','captura resumen publicado sin imponer barrio del sujeto');
    try { $service->parse(str_replace($url,'https://www.fincaraiz.com.co/venta',$html),$url,2); $rejected=false; } catch(\RuntimeException) { $rejected=true; }
    expect($rejected,'rechaza listado cuyo canonico no confirma la zona');
})();
