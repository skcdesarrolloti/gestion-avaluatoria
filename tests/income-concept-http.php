<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
require __DIR__.'/support.php';
$port=getenv('GA_TEST_PORT');
if (getenv('GA_TEST_HTTP')!=='true' || !$port || !ctype_digit($port) || $port==='3306') throw new RuntimeException('Usa servidor local de fixtures y GA_TEST_PORT desechable.');
$app=\App\Core\Database::connect(['host'=>'127.0.0.1','port'=>$port,'database'=>'ga_test_app','username'=>'root','password'=>'']);
$ar=new \App\Models\AppraisalRepository($app);
$id=$ar->create(1);$ar->ensureUnits($id,1,1,0);
$query=$app->prepare('UPDATE appraisals SET igac_property_units_count=1 WHERE id=?');$query->execute([$id]);
$unitId=$ar->units($id,1)[0]['id'];
$wf=new \App\Models\MethodologyWorkflowRepository($app);
$wf->save($id,1,0,$unitId,['method'=>'mercado','additional_methods'=>['renta']]);
$incomeKey=$unitId.':metodo:renta';
$path='/avaluos/'.$id;$flowPath=$path.'/metodologia-valuatoria';
$cookie=tempnam(sys_get_temp_dir(),'ga_renta_http_');
function request(string $path,?string $body=null,array $headers=[]): array
{
    $curl=curl_init('http://127.0.0.1:8088'.$path);
    curl_setopt_array($curl,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_HEADER=>true,CURLOPT_COOKIEJAR=>$GLOBALS['cookie'],
        CURLOPT_COOKIEFILE=>$GLOBALS['cookie'],CURLOPT_TIMEOUT=>10,CURLOPT_HTTPHEADER=>$headers]);
    if ($body!==null) { curl_setopt($curl,CURLOPT_POST,true);curl_setopt($curl,CURLOPT_POSTFIELDS,$body); }
    $result=curl_exec($curl);
    if ($result===false) throw new RuntimeException(curl_error($curl));
    $response=['status'=>curl_getinfo($curl,CURLINFO_HTTP_CODE),'headers'=>substr($result,0,curl_getinfo($curl,CURLINFO_HEADER_SIZE)),
        'body'=>substr($result,curl_getinfo($curl,CURLINFO_HEADER_SIZE))];curl_close($curl);return $response;
}
$login=request('/login');preg_match('/name="csrf-token" content="([a-f0-9]+)"/',$login['body'],$matches);
$token=$matches[1];$jsonHeaders=['Accept: application/json'];
expect(request('/login',http_build_query(['_token'=>$token,'username'=>'ga_test','password'=>'Only-test-2026!']))['status']===303,'HTTP renta usa login real contra funcionarios de prueba');
$home=request('/');preg_match('/name="csrf-token" content="([a-f0-9]+)"/',$home['body'],$matches);$token=$matches[1];
$incomePath=$flowPath.'/renta/concepto';
$incomePost=['_token'=>$token,'version'=>1,'component'=>$incomeKey,
    'income_concept'=>['occupancy'=>'partial','scope'=>'part','support'=>'verbal','scope_detail'=>'Zona <reservada>',
        'rental_scope'=>'different','lease_term'=>'Vigencia según cláusula <privada>','cost_ph'=>'tenant','history_occupancy'=>'none']];
expect(request($incomePath,http_build_query(array_replace($incomePost,['_token'=>'invalid'])),$jsonHeaders)['status']===419,'HTTP renta requiere CSRF');
expect(request($incomePath,http_build_query(array_replace($incomePost,['component'=>$unitId])),$jsonHeaders)['status']===422,'HTTP renta rechaza recorrido Mercado');
expect(request($incomePath,http_build_query($incomePost),$jsonHeaders)['status']===200,'HTTP renta guarda borrador');
expect(request($incomePath,http_build_query($incomePost),$jsonHeaders)['status']===409,'HTTP renta rechaza versión antigua');
$incomePage=$flowPath.'?stage=3&component='.$incomeKey;
$incomeHtml=request($incomePage)['body'];
expect(str_contains($incomeHtml,'Zona &lt;reservada&gt;') && str_contains($incomeHtml,'Parcialmente arrendado'),'HTTP recarga conserva respuestas y escapa notas');
expect(str_contains($incomeHtml,'Vigencia según cláusula &lt;privada&gt;') && str_contains($incomeHtml,'Arrendatario'),
    'HTTP recarga contrato y responsabilidades nuevas junto a respuestas históricas');
$invalidIncome=array_replace($incomePost,['version'=>2,'income_concept'=>['occupancy'=>'invalid','scope_detail'=>'Borrador pendiente']]);
$incomeResponse=request($incomePath,http_build_query($invalidIncome),$jsonHeaders);
expect($incomeResponse['status']===422 && isset(json_decode($incomeResponse['body'],true)['errors']['income_concept[occupancy]']),'HTTP validación devuelve errores por campo');
expect(request($incomePath,http_build_query($invalidIncome))['status']===303,'HTTP sin JavaScript redirige conservando borrador inválido');
$incomeHtml=request($incomePage)['body'];
expect(str_contains($incomeHtml,'Borrador pendiente') && str_contains($incomeHtml,'value="invalid" selected') && str_contains($incomeHtml,'aria-invalid="true"'),'HTTP fallback recupera valores y error junto al campo');
$incomeAcademy=request($flowPath.'?stage=1&component='.$incomeKey)['body'];
expect(str_contains($incomeAcademy,'renta-orientacion-v1.pdf') && str_contains($incomeAcademy,'Antes de empezar con Renta'),'HTTP academia incluye resumen');
$incomePdf=request('/assets/normativa/renta-orientacion-v1.pdf');
expect($incomePdf['status']===200 && str_starts_with($incomePdf['body'],'%PDF-'),'HTTP resumen PDF disponible en aplicativo');
$foreignId=$ar->create(2);
expect(request('/avaluos/'.$foreignId.'/metodologia-valuatoria/renta/concepto',http_build_query($incomePost),$jsonHeaders)['status']===404,'HTTP renta rechaza expediente de otro propietario');
$auth=\App\Core\Database::connect(['host'=>'127.0.0.1','port'=>$port,'database'=>'ga_test_auth','username'=>'root','password'=>'']);
$auth->exec("UPDATE wp_jet_cct_funcionarios SET activo='No' WHERE _ID=1");
try { expect(request($incomePath,http_build_query($incomePost),$jsonHeaders)['status']===401,'HTTP renta revalida actividad antes de guardar'); }
finally { $auth->exec("UPDATE wp_jet_cct_funcionarios SET activo='Si' WHERE _ID=1"); }
file_put_contents(__DIR__.'/.runtime/renta-ui-context.json',json_encode(['id'=>$id,'key'=>$incomeKey]));
unlink($cookie);report();
