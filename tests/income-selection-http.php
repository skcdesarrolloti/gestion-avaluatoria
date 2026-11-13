<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';require __DIR__.'/support.php';
$port=getenv('GA_TEST_PORT');
if (getenv('GA_TEST_HTTP')!=='true' || !$port || !ctype_digit($port) || $port==='3306') throw new RuntimeException('Solo servidor de fixtures y puerto desechable.');
$config=['host'=>'127.0.0.1','port'=>$port,'database'=>'ga_test_app','username'=>'root','password'=>''];
$app=\App\Core\Database::connect($config);
require __DIR__.'/income-selection-persistence.php';
['id'=>$id,'key'=>$key,'sample'=>$sample]=$GLOBALS['incomeSelectionFixture'];
$cookie=tempnam(sys_get_temp_dir(),'ga_selection_');
function selectionRequest(string $path,?array $post=null,bool $json=false): array {
    $c=curl_init('http://127.0.0.1:8088'.$path);curl_setopt_array($c,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_HEADER=>true,CURLOPT_COOKIEJAR=>$GLOBALS['cookie'],CURLOPT_COOKIEFILE=>$GLOBALS['cookie'],CURLOPT_TIMEOUT=>15]);
    if ($post!==null) curl_setopt_array($c,[CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>http_build_query($post)]);
    if ($json) curl_setopt($c,CURLOPT_HTTPHEADER,['Accept: application/json']);
    $raw=curl_exec($c);if ($raw===false) throw new RuntimeException(curl_error($c));
    $response=['status'=>curl_getinfo($c,CURLINFO_HTTP_CODE),'body'=>substr($raw,curl_getinfo($c,CURLINFO_HEADER_SIZE))];curl_close($c);return $response;
}
$login=selectionRequest('/login');preg_match('/name="csrf-token" content="([a-f0-9]+)"/',$login['body'],$match);
expect(selectionRequest('/login',['_token'=>$match[1],'username'=>'ga_test','password'=>'Only-test-2026!'])['status']===303,'selección usa login real');
$home=selectionRequest('/');preg_match('/name="csrf-token" content="([a-f0-9]+)"/',$home['body'],$match);
$route='/avaluos/'.$id.'/metodologia-valuatoria';$page=$route.'?stage=3&method=renta&component='.$key.'&renta_paso=analysis';
$html=selectionRequest($page);
file_put_contents(__DIR__.'/.runtime/income-selection-http-page.html',$html['body']);
expect($html['status']===200 && str_contains($html['body'],'Análisis y exclusiones de Renta'),'análisis Renta responde con panel');
expect(str_contains($html['body'],'Modalidad &lt;distinta&gt;') && !str_contains($html['body'],'Modalidad <distinta>'),'constancia escapa motivos');
$post=['_token'=>$match[1],'component'=>$key,'study_scope'=>'renta:arriendo','version'=>7,'samples_version'=>2,
    'sample'=>$sample,'operation'=>'exclude','category'=>'different','reason'=>'Modalidad revisada','support'=>'Soporte HTTP'];
expect(selectionRequest($route.'/renta/seleccion',array_replace($post,['_token'=>'bad']),true)['status']===419,'excluir exige CSRF');
$bad=array_replace($post,['reason'=>'','support'=>'']);$res=selectionRequest($route.'/renta/seleccion',$bad,true);
expect($res['status']===422 && isset(json_decode($res['body'],true)['errors']['support']),'decisión incompleta devuelve errores de campo');
expect(selectionRequest($route.'/renta/seleccion',$bad)['status']===303,'fallback conserva borrador incompleto');
expect(str_contains(selectionRequest($page)['body'],'Completa este campo'),'fallback muestra error sin perder muestra');
expect(selectionRequest($route.'/renta/seleccion',$post,true)['status']===200,'excluir por HTTP persiste');
expect(selectionRequest($route.'/renta/seleccion',$post,true)['status']===409,'decisión HTTP obsoleta no sobrescribe');
$html=selectionRequest($page)['body'];
expect(str_contains($html,'Excluida con constancia') && str_contains($html,'Soporte HTTP'),'recarga recupera decisión y motivo');
$export=selectionRequest($route.'/renta/constancia?component='.$key);$audit=json_decode($export['body'],true);
expect($export['status']===200 && count($audit['audit']['events'])===3 && count($audit['audit']['datasets'])===2,'constancia exporta historial y bases reproducibles');
$repo=new \App\Models\AppraisalRepository($app);$foreign=$repo->create(2);
expect(selectionRequest('/avaluos/'.$foreign.'/metodologia-valuatoria/renta/seleccion',$post,true)['status']===404,'no puede excluir en expediente ajeno');
expect(selectionRequest('/avaluos/'.$foreign.'/metodologia-valuatoria/renta/constancia?component='.$key,null,true)['status']===404,'no puede descargar constancia ajena');
$restore=array_replace($post,['version'=>8,'operation'=>'restore','category'=>'restore','reason'=>'Revisada para prueba']);
expect(selectionRequest($route.'/renta/seleccion',$restore,true)['status']===200,'reincorporar por HTTP agrega constancia');
$auth=\App\Core\Database::connect(array_replace($config,['database'=>'ga_test_auth']));
$auth->exec("UPDATE wp_jet_cct_funcionarios SET activo='No' WHERE _ID=1");
try {expect(selectionRequest($route.'/renta/seleccion',$restore,true)['status']===401,'se revalida actividad antes de decidir');}
finally {$auth->exec("UPDATE wp_jet_cct_funcionarios SET activo='Si' WHERE _ID=1");}
file_put_contents(__DIR__.'/.runtime/income-selection-ui.json',json_encode(compact('id','key','page')));
unlink($cookie);report();
