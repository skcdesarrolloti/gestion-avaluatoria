<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';require __DIR__.'/support.php';
$port=getenv('GA_TEST_PORT');
if (getenv('GA_TEST_HTTP')!=='true' || !$port || !ctype_digit($port) || $port==='3306') throw new RuntimeException('Solo servidor de fixtures y puerto desechable.');
$db=\App\Core\Database::connect(['host'=>'127.0.0.1','port'=>$port,'database'=>'ga_test_app','username'=>'root','password'=>'']);
$repo=new \App\Models\AppraisalRepository($db);$id=$repo->create(1);$repo->ensureUnits($id,1,1,1);
$db->prepare('UPDATE appraisals SET titulo=?,tipo_inmueble=?,igac_property_units_count=1,igac_annex_units_count=1,rent_amount=3500000,income_notes=? WHERE id=?')->execute(['Prueba ficha compartida','oficina','Antecedente original',$id]);
$units=$repo->units($id,1);$office=$units[0]['id'];$garage=$units[1]['id'];
$db->prepare('UPDATE appraisal_units SET label=?,property_type=?,construction_type=? WHERE id=?')->execute(['Oficina de prueba','oficina','oficina',$office]);
$db->prepare('UPDATE appraisal_units SET label=?,construction_type=? WHERE id=?')->execute(['Garaje de prueba','parqueo',$garage]);
$wf=new \App\Models\MethodologyWorkflowRepository($db);$wf->save($id,1,0,$office,['method'=>'mercado','additional_methods'=>['renta']]);
$key=$office.':metodo:renta';$wf->save($id,1,1,$garage,['method'=>'renta']);
$wf->save($id,1,2,$key,['income_concept'=>['lease_term'=>'Texto histórico <contrato>','cost_ph'=>'tenant']]);
$cookie=tempnam(sys_get_temp_dir(),'ga_lease_');
function leaseRequest(string $path,?array $post=null,bool $json=false): array {
    $c=curl_init('http://127.0.0.1:8088'.$path);curl_setopt_array($c,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_HEADER=>true,CURLOPT_COOKIEJAR=>$GLOBALS['cookie'],CURLOPT_COOKIEFILE=>$GLOBALS['cookie'],CURLOPT_TIMEOUT=>15]);
    if ($post!==null) curl_setopt_array($c,[CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>http_build_query($post)]);
    if ($json) curl_setopt($c,CURLOPT_HTTPHEADER,['Accept: application/json']);
    $raw=curl_exec($c);if ($raw===false) throw new RuntimeException(curl_error($c));
    $response=['status'=>curl_getinfo($c,CURLINFO_HTTP_CODE),'body'=>substr($raw,curl_getinfo($c,CURLINFO_HEADER_SIZE))];curl_close($c);return $response;
}
$login=leaseRequest('/login');preg_match('/name="csrf-token" content="([a-f0-9]+)"/',$login['body'],$match);
expect(leaseRequest('/login',['_token'=>$match[1],'username'=>'ga_test','password'=>'Only-test-2026!'])['status']===303,'ficha usa autenticación real de prueba');
$home=leaseRequest('/');preg_match('/name="csrf-token" content="([a-f0-9]+)"/',$home['body'],$match);$token=$match[1];
$base='/avaluos/'.$id;$route=$base.'/metodologia-valuatoria';$contract=str_repeat('b',32);
$post=['_token'=>$token,'version'=>0,'component'=>$key,'lease_id'=>$contract,'lease_units'=>[$office,$garage],
    'lease'=>['reference'=>'Contrato <compartido>','economic_source'=>'assignment','start_date'=>'2026-01-01','end_date'=>'2028-01-01','term'=>'2 años','adjustment_rule'=>'IPC según cláusula 5']];
expect(leaseRequest($route.'/renta/arriendo',array_replace($post,['_token'=>'bad']),true)['status']===419,'contrato exige CSRF');
expect(leaseRequest($route.'/renta/arriendo',array_replace($post,['component'=>$office]),true)['status']===422,'contrato exige recorrido Renta activo');
expect(leaseRequest($route.'/renta/arriendo',$post,true)['status']===200,'contrato guarda unidades y fechas');
expect(leaseRequest($route.'/renta/arriendo',$post,true)['status']===409,'contrato rechaza versión vieja');
$page=$route.'?stage=3&component='.urlencode($key).'&lease='.$contract;
$html=leaseRequest($page)['body'];
expect(str_contains($html,'Contrato &lt;compartido&gt;') && str_contains($html,'Texto histórico &lt;contrato&gt;'),'ficha y antecedentes se recuperan escapados');
preg_match('/href="([^"]+)"[^>]*>Agregar otro contrato<\/a>/',$html,$newLink);
parse_str(parse_url(html_entity_decode($newLink[1],ENT_QUOTES,'UTF-8'),PHP_URL_QUERY),$newQuery);
expect(preg_match('/^[a-f0-9]{32}$/D',$newQuery['lease'] ?? '')===1 && $newQuery['lease']!==$contract,
    'nuevo contrato tiene enlace estable antes de navegar; recargar no genera otra ficha');
expect(str_contains(leaseRequest($route.'?stage=3&component='.$garage)['body'],'Contrato &lt;compartido&gt;'),'garaje abre el mismo contrato');
$bad=array_replace($post,['version'=>1,'lease'=>['end_date'=>'2025-01-01','notes'=>'Borrador sin perder']]);
$response=leaseRequest($route.'/renta/arriendo',$bad,true);
expect($response['status']===422 && isset(json_decode($response['body'],true)['errors']['lease[end_date]']),'fecha inconsistente indica el campo');
expect(leaseRequest($route.'/renta/arriendo',$bad)['status']===303,'fallback redirige con borrador');
$html=leaseRequest($page)['body'];expect(str_contains($html,'Borrador sin perder') && str_contains($html,'aria-invalid="true"'),'fallback conserva borrador y errores');
$occupationPath=$base.'/bien-sujeto/unidades/'.$office.'/ocupacion';
$occupation=['_token'=>$token,'version'=>0,'occupation'=>['occupancy'=>'rented','date'=>'2026-11-13','source'=>'Visita de prueba']];
expect(leaseRequest($occupationPath,$occupation,true)['status']===200,'ocupación se guarda en capítulo 3');
expect(leaseRequest($occupationPath,$occupation,true)['status']===409,'ocupación rechaza edición simultánea');
expect(str_contains(leaseRequest($page)['body'],'Totalmente arrendado'),'Renta consulta ocupación actual de capítulo 3');
$v=$repo->find($id,1)['version'];
$ante=['_token'=>$token,'version'=>$v,'component'=>$key,'lease_id'=>$contract,'antecedent'=>['rent_amount'=>'3.600.000,50']];
expect(leaseRequest($route.'/renta/antecedente',$ante,true)['status']===200,'canon de origen se edita desde Renta');
expect($repo->find($id,1)['rent_amount']==='3600000.50','canon conserva su columna original');
$foreign=$repo->create(2);
expect(leaseRequest('/avaluos/'.$foreign.'/metodologia-valuatoria/renta/arriendo',$post,true)['status']===404,'contrato ajeno denegado');
$auth=\App\Core\Database::connect(['host'=>'127.0.0.1','port'=>$port,'database'=>'ga_test_auth','username'=>'root','password'=>'']);
$auth->exec("UPDATE wp_jet_cct_funcionarios SET activo='No' WHERE _ID=1");
try { expect(leaseRequest($route.'/renta/arriendo',$post,true)['status']===401,'actividad revalidada al guardar contrato'); }
finally { $auth->exec("UPDATE wp_jet_cct_funcionarios SET activo='Si' WHERE _ID=1"); }
file_put_contents(__DIR__.'/.runtime/lease-ui.json',json_encode(compact('id','office','garage','key','contract')));
unlink($cookie);report();
