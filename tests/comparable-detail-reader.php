<?php
use App\Services\ComparableDetailReader;
use App\Services\ComparableDetailParser;
$detailUrl='https://www.araujoysegovia.com/inmueble/oficina-123';
expect(ComparableDetailReader::canonicalUrl($detailUrl.'#vista')===$detailUrl,'ficha admite inmobiliaria del catálogo y elimina fragmento');
expect(str_contains(ComparableDetailReader::canonicalUrl('https://inmueble.mercadolibre.com.co/MCO-123-oficina-venta'),'inmueble.mercadolibre'),'ficha admite subdominio público de avisos Mercado Libre');
foreach (['https://127.0.0.1/oficina-123','https://www.araujoysegovia.com.evil.test/oficina-123','https://user@www.araujoysegovia.com/oficina-123','https://www.araujoysegovia.com:443/oficina-123','http://www.araujoysegovia.com/oficina-123','https://www.araujoysegovia.com/'] as $bad) {
    try { ComparableDetailReader::canonicalUrl($bad); throw new LogicException('URL insegura aceptada'); }
    catch (InvalidArgumentException) { expect(true,'ficha rechaza URL insegura o portada'); }
}
$listing=['@type'=>'RealEstateListing','url'=>$detailUrl,'name'=>'Oficina de prueba',
    'description'=>"Recepción y ascensor.\nAcabados: Mármol; Vista: Exterior paisajística;",
    'offers'=>['price'=>987000000,'priceCurrency'=>'COP'],
    'mainEntity'=>['@type'=>'Place','numberOfBathroomsTotal'=>2,'floorSize'=>['value'=>117.5,'unitCode'=>'MTK'],
        'additionalProperty'=>[['name'=>'Rampa de acceso','value'=>true],['name'=>'Altura libre','value'=>'3.5 m']]]];
$markup=static fn ($data)=>'<script type="application/ld+json">'.json_encode($data).'</script>';
$row=(new ComparableDetailParser())->parse($markup($listing),$detailUrl)['row'];
expect($row['bathrooms']==='2' && $row['area_m2']==='117.5' && $row['price_amount']==='987000000','ficha conserva medidas explícitas y precio COP');
$facts=json_decode($row['published_attributes'],true);
expect($facts['Rampa de acceso']==='Sí' && $facts['Acabados']==='Mármol' && $facts['Altura libre']==='3.5 m','ficha conserva atributos originales sin inventar calificación');
expect(str_contains($row['published_text'],'Recepción y ascensor'),'ficha conserva descripción completa');
$other=$listing; $other['url']='https://www.araujoysegovia.com/inmueble/oficina-999'; $other['mainEntity']['numberOfBathroomsTotal']=999;
$row=(new ComparableDetailParser())->parse($markup(['@graph'=>[$listing,$other]]),$detailUrl)['row'];
expect($row['bathrooms']==='2','ficha no mezcla baños de anuncios relacionados');
try { (new ComparableDetailParser())->parse($markup($other),$detailUrl); throw new LogicException('Anuncio ajeno aceptado'); }
catch (RuntimeException) { expect(true,'ficha sin identidad inequívoca queda pendiente'); }

$fincaUrl='https://www.fincaraiz.com.co/oficina-en-venta-en-manga-cartagena/193907764';
$data=['id'=>193907764,'link'=>'/oficina-en-venta-en-manga-cartagena/193907764','description'=>'Recepción del edificio.',
    'owner'=>['name'=>'Inmobiliaria de prueba','type'=>'inmobiliaria','masked_phone'=>'+5731'],
    'technicalSheet'=>[['field'=>'floor','text'=>'Piso N°','value'=>'4'],['field'=>'constructionYear','text'=>'Antigüedad','value'=>'1 a 8 años'],['field'=>'m2apto','text'=>'Área Privada','value'=>'123 m2']],
    'facilities'=>[['name'=>'Circuito cerrado de TV','group'=>'Exterior'],['name'=>'Parqueadero Visitantes','group'=>'Exterior']]];
$doc=new DOMDocument(); $doc->loadHTML('<script id="__NEXT_DATA__" type="application/json">'.json_encode(['props'=>['pageProps'=>['data'=>$data]]]).'</script>');
$ficha=App\Services\FincaraizFichaDetails::parse($doc,$fincaUrl);
$facts=json_decode($ficha['published_attributes'],true);
expect($ficha['floor_level']==='4' && $facts['Área Privada']==='123 m2','ficha técnica conserva piso y etiqueta de área privada');
expect(!isset($ficha['age_years'],$ficha['private_built_m2']),'ficha no convierte intervalo en edad ni privada en privada construida');
expect(isset($facts['Circuito cerrado de TV'],$facts['Parqueadero Visitantes']),'ficha conserva todas las instalaciones publicadas');
expect($ficha['contact_name']==='Inmobiliaria de prueba' && $facts['Anunciante']==='Inmobiliaria de prueba'
    && $facts['Tipo de anunciante']==='inmobiliaria','ficha conserva anunciante distinto del portal y de propiedad jurídica');
expect(!isset($ficha['contact_phone']),'no presenta teléfono enmascarado como contacto completo');
expect(App\Services\FincaraizFichaDetails::parse($doc,str_replace('193907764','193978243',$fincaUrl))===[],'ficha técnica exige identidad del anuncio y no lee filtros relacionados');

$sources=(new App\Services\AppraisalComparableSourceSearchBuilder())->build([],['city_name'=>'Cartagena'],'oficina','Oficina','Venta');
foreach (array_merge($sources['portal_sources'],$sources['agency_sources']) as $source) {
    $parts=parse_url($source['url']); $url='https://'.($source['domain'] ?? $parts['host']).'/inmueble/oficina-123';
    $html='<link rel="canonical" href="'.$url.'"><main><h1>Oficina</h1><dl><dt>Baños</dt><dd>2</dd><dt>Rampa de acceso</dt><dd>Sí</dd></dl><p>Anunciante: Agencia publicada</p><p>Recepción privada.</p><section class="related"><p>Baños: 999</p></section></main>';
    $parsed=(new ComparableDetailParser())->parse($html,$url)['row'];
    expect($parsed['bathrooms']==='2','lector visible preserva baños explícitos de '.$source['label']);
    $facts=json_decode($parsed['published_attributes'],true);
    expect($facts['Rampa de acceso']==='Sí' && !str_contains($parsed['published_text'],'999'),'lector de '.$source['label'].' conserva atributos sin mezclar relacionados');
}
$wasiUrl='https://asesorarinmobiliaria.com/inmueble/oficina-123/';
$wasi=['url'=>['detail'=>rtrim($wasiUrl,'/')],'nombre'=>'Oficina publicada','descripcion'=>'Recepción privada.',
    'gestion'=>['esVenta'=>true],'valor_venta'=>100000000,'n_baños'=>2,'area_construida'=>85,
    'caracteristicas'=>[['nombre'=>'Rampa de acceso','valor'=>null],['nombre'=>'Piso','valor'=>'4']]];
$wasiHtml='<script>window.VISUALINMUEBLE_INMUEBLE = '.json_encode($wasi).';</script>';
$parsed=(new ComparableDetailParser())->parse($wasiHtml,$wasiUrl)['row'];
expect($parsed['bathrooms']==='2' && $parsed['area_m2']==='85' && $parsed['floor_level']==='4','formato de inmobiliaria conserva medidas e instalaciones de su ficha');
expect(json_decode($parsed['published_attributes'],true)['Rampa de acceso']==='Publicado en características','instalación sin valoración conserva lo publicado sin inventar calificación');
try { (new ComparableDetailParser())->parse($wasiHtml,str_replace('123','456',$wasiUrl)); throw new LogicException('Identidad ajena'); }
catch (RuntimeException) { expect(true,'formato de inmobiliaria no lee objeto de otra propiedad'); }
