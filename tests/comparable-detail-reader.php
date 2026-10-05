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
    'technicalSheet'=>[['field'=>'floor','text'=>'Piso N°','value'=>'4'],['field'=>'constructionYear','text'=>'Antigüedad','value'=>'1 a 8 años'],['field'=>'m2apto','text'=>'Área Privada','value'=>'123 m2']],
    'facilities'=>[['name'=>'Circuito cerrado de TV','group'=>'Exterior'],['name'=>'Parqueadero Visitantes','group'=>'Exterior']]];
$doc=new DOMDocument(); $doc->loadHTML('<script id="__NEXT_DATA__" type="application/json">'.json_encode(['props'=>['pageProps'=>['data'=>$data]]]).'</script>');
$ficha=App\Services\FincaraizFichaDetails::parse($doc,$fincaUrl);
$facts=json_decode($ficha['published_attributes'],true);
expect($ficha['floor_level']==='4' && $facts['Área Privada']==='123 m2','ficha técnica conserva piso y etiqueta de área privada');
expect(!isset($ficha['age_years'],$ficha['private_built_m2']),'ficha no convierte intervalo en edad ni privada en privada construida');
expect(isset($facts['Circuito cerrado de TV'],$facts['Parqueadero Visitantes']),'ficha conserva todas las instalaciones publicadas');
expect(App\Services\FincaraizFichaDetails::parse($doc,str_replace('193907764','193978243',$fincaUrl))===[],'ficha técnica exige identidad del anuncio y no lee filtros relacionados');
