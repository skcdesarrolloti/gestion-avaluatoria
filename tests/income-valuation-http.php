<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';require __DIR__.'/support.php';
$port=getenv('GA_TEST_PORT');$http=getenv('GA_TEST_HTTP_PORT') ?: '8090';
if (getenv('GA_TEST_HTTP')!=='true' || !$port || !ctype_digit($port) || $port==='3306' || !ctype_digit($http)) throw new RuntimeException('Solo servidor local y bases desechables.');
$config=['host'=>'127.0.0.1','port'=>$port,'database'=>'ga_test_app','username'=>'root','password'=>''];$app=\App\Core\Database::connect($config);
require __DIR__.'/income-valuation-persistence.php';extract($GLOBALS['incomeValuationFixture']);$cookie=tempnam(sys_get_temp_dir(),'ga_income_');
function incomeRequest(string $path,?array $post=null,bool $json=false): array {
    $c=curl_init('http://127.0.0.1:'.$GLOBALS['http'].$path);curl_setopt_array($c,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_HEADER=>true,CURLOPT_COOKIEJAR=>$GLOBALS['cookie'],CURLOPT_COOKIEFILE=>$GLOBALS['cookie'],CURLOPT_TIMEOUT=>20]);
    if ($post!==null) curl_setopt_array($c,[CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>http_build_query($post)]);
    if ($json) curl_setopt($c,CURLOPT_HTTPHEADER,['Accept: application/json']);
    $raw=curl_exec($c);if ($raw===false) throw new RuntimeException(curl_error($c));
    $r=['status'=>curl_getinfo($c,CURLINFO_HTTP_CODE),'body'=>substr($raw,curl_getinfo($c,CURLINFO_HEADER_SIZE))];curl_close($c);return $r;
}
$login=incomeRequest('/login');preg_match('/name="csrf-token" content="([a-f0-9]+)"/',$login['body'],$match);
expect(incomeRequest('/login',['_token'=>$match[1],'username'=>'ga_test','password'=>'Only-test-2026!'])['status']===303,'valoración prueba login real');
$home=incomeRequest('/');preg_match('/name="csrf-token" content="([a-f0-9]+)"/',$home['body'],$match);
$route='/avaluos/'.$id.'/metodologia-valuatoria';$page=$route.'?stage=3&method=renta&component='.$key.'&renta_paso=valuation';$endpoint=$route.'/renta/valoracion';
$html=incomeRequest($page);file_put_contents(__DIR__.'/.runtime/income-valuation-http-page.html',$html['body']);
expect($html['status']===200 && str_contains($html['body'],'Comparar tasas y calcular los dos métodos'),'pantalla nueva responde con acción explícita');
$post=['_token'=>$match[1],'component'=>$key,'study_scope'=>'renta:arriendo','version'=>$version,'context_hash'=>$context_hash,'payload'=>json_encode($data)];
expect(incomeRequest($endpoint,array_replace($post,['payload'=>'','data'=>$data]),true)['status']===422,'fallback truncado no reemplaza el escenario');
expect(incomeRequest($endpoint,array_replace($post,['_token'=>'bad']),true)['status']===419,'valoración exige CSRF');
$bad=$data;$bad['projection']['market_rent']='3.500.000';$bad['projection']['income_support']='Soporte <script>alert(1)</script>';
$invalid=array_replace($post,['payload'=>json_encode($bad)]);$res=incomeRequest($endpoint,$invalid,true);
expect($res['status']===422 && isset(json_decode($res['body'],true)['errors']['data[projection][market_rent]']),'error de importe apunta al campo correcto');
expect(incomeRequest($endpoint,$invalid)['status']===303,'fallback mantiene borrador inválido');
$fallback=incomeRequest($page)['body'];expect(str_contains($fallback,'3.500.000') && str_contains($fallback,'Soporte &lt;script&gt;alert(1)&lt;/script&gt;'),'fallback conserva importe y escapa soporte');
$post['operation']='calculate';expect(incomeRequest($endpoint,$post,true)['status']===200,'calcula por HTTP con controles del servidor');
expect(incomeRequest($endpoint,$post,true)['status']===409,'rechaza recálculo con versión obsoleta');
$memory=incomeRequest($route.'/renta/memoria?component='.$key);$decoded=json_decode($memory['body'],true);
expect($memory['status']===200 && $decoded['current']===true && count($decoded['valuation']['calculation']['result']['dcf']['months'])===12,'memoria exporta entrada y doce meses vigentes');
$ar=new \App\Models\AppraisalRepository($app);$foreign=$ar->create(2);
expect(incomeRequest('/avaluos/'.$foreign.'/metodologia-valuatoria/renta/valoracion',$post,true)['status']===404,'no guarda escenarios ajenos');
expect(incomeRequest('/avaluos/'.$foreign.'/metodologia-valuatoria/renta/memoria?component='.$key,null,true)['status']===404,'no exporta memoria ajena');
$auth=\App\Core\Database::connect(array_replace($config,['database'=>'ga_test_auth']));$auth->exec("UPDATE wp_jet_cct_funcionarios SET activo='No' WHERE _ID=1");
try {expect(incomeRequest($endpoint,$post,true)['status']===401,'revalida activo antes del cálculo');}
finally {$auth->exec("UPDATE wp_jet_cct_funcionarios SET activo='Si' WHERE _ID=1");}
file_put_contents(__DIR__.'/.runtime/income-valuation-ui.json',json_encode(compact('id','key','page')));unlink($cookie);report();
