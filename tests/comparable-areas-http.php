<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';require __DIR__.'/support.php';
$port=getenv('GA_TEST_PORT');
if (getenv('GA_TEST_HTTP')!=='true' || !$port || !ctype_digit($port) || $port==='3306') throw new RuntimeException('Solo fixtures locales desechables.');
$config=['host'=>'127.0.0.1','port'=>$port,'database'=>'ga_test_app','username'=>'root','password'=>''];
$app=\App\Core\Database::connect($config);require __DIR__.'/comparable-areas-persistence.php';
['id'=>$id,'key'=>$key,'sample'=>$sample]=$GLOBALS['areaFixture'];$cookie=tempnam(sys_get_temp_dir(),'ga_areas_');
function areaRequest(string $path,?array $post=null): array {
    $c=curl_init('http://127.0.0.1:8088'.$path);curl_setopt_array($c,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_HEADER=>true,
        CURLOPT_COOKIEJAR=>$GLOBALS['cookie'],CURLOPT_COOKIEFILE=>$GLOBALS['cookie'],CURLOPT_TIMEOUT=>15]);
    if ($post!==null) curl_setopt_array($c,[CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>http_build_query($post),CURLOPT_HTTPHEADER=>['Accept: application/json']]);
    $raw=curl_exec($c);if ($raw===false) throw new RuntimeException(curl_error($c));
    $response=['status'=>curl_getinfo($c,CURLINFO_HTTP_CODE),'body'=>substr($raw,curl_getinfo($c,CURLINFO_HEADER_SIZE))];curl_close($c);return $response;
}
$login=areaRequest('/login');preg_match('/name="csrf-token" content="([a-f0-9]+)"/',$login['body'],$m);
expect(areaRequest('/login',['_token'=>$m[1],'username'=>'ga_test','password'=>'Only-test-2026!'])['status']===303,'áreas usa login real');
$home=areaRequest('/');preg_match('/name="csrf-token" content="([a-f0-9]+)"/',$home['body'],$m);
$path='/avaluos/'.$id.'/metodologia-valuatoria/comparables/areas';$url=$path.'?component='.$key;
$page=areaRequest($url);
expect($page['status']===200 && str_contains($page['body'],'Completar áreas de los anuncios') && !str_contains($page['body'],'id="tabla-madre-83"'),'vista ligera no monta la matriz completa');
$post=['_token'=>$m[1],'component_scope'=>$key,'study_scope'=>'renta:arriendo','version'=>2,
    'areas'=>[$sample=>['area_m2'=>'99,25','area_basis'=>'Construida publicada','areas_source'=>'Anuncio, verificación HTTP']]];
expect(areaRequest($path,array_replace($post,['_token'=>'bad']))['status']===419,'guardar áreas exige CSRF');
$bad=$post;$bad['areas'][$sample]['area_m2']='-1';$res=areaRequest($path,$bad);
expect($res['status']===422 && isset(json_decode($res['body'],true)['errors']["areas[$sample][area_m2]"]),'áreas devuelve error junto al campo');
expect(areaRequest($path,$post)['status']===200,'guardado HTTP de áreas confirma versión');
expect(str_contains(areaRequest($url)['body'],'99.25'),'recarga HTTP recupera área guardada');
expect(areaRequest($path,$post)['status']===409,'áreas HTTP evita sobrescritura concurrente');
$foreign=(new \App\Models\AppraisalRepository($app))->create(2);
expect(areaRequest('/avaluos/'.$foreign.'/metodologia-valuatoria/comparables/areas?component='.$key)['status']===404,'lectura ajena de áreas rechazada');
expect(areaRequest('/avaluos/'.$foreign.'/metodologia-valuatoria/comparables/areas',$post)['status']===404,'escritura ajena de áreas rechazada');
$auth=\App\Core\Database::connect(array_replace($config,['database'=>'ga_test_auth']));$auth->exec("UPDATE wp_jet_cct_funcionarios SET activo='No' WHERE _ID=1");
try {expect(areaRequest($path,$post)['status']===401,'áreas revalida actividad del funcionario');} finally {$auth->exec("UPDATE wp_jet_cct_funcionarios SET activo='Si' WHERE _ID=1");}
file_put_contents(__DIR__.'/.runtime/renta-areas-ui.json',json_encode(compact('id','key','sample','url')));unlink($cookie);report();
