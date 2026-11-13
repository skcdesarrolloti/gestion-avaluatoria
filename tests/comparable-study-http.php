<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';require __DIR__.'/support.php';
$testPort=getenv('GA_TEST_PORT');
if (getenv('GA_TEST_HTTP')!=='true' || !$testPort || !ctype_digit($testPort) || $testPort==='3306') throw new RuntimeException('Requiere fixture HTTP local y GA_TEST_PORT desechable.');
$db=\App\Core\Database::connect(['host'=>'127.0.0.1','port'=>$testPort,'database'=>'ga_test_app','username'=>'root','password'=>'']);
$ar=new \App\Models\AppraisalRepository($db);$samples=new \App\Models\AppraisalComparableRepository($db);
$workflow=new \App\Models\MethodologyWorkflowRepository($db);
$id=$ar->create(1);$ar->ensureUnits($id,1,1,0);$db->prepare('UPDATE appraisals SET igac_property_units_count=1 WHERE id=?')->execute([$id]);
$key=$ar->units($id,1)[0]['id'];$workflow->save($id,1,0,$key,['method'=>'mercado']);
$sale=bin2hex(random_bytes(16));$rent=bin2hex(random_bytes(16));$unknown=bin2hex(random_bytes(16));
$samples->saveAll($id,1,[['id'=>$sale,'component_key'=>$key,'operation'=>'Venta','source_name'=>'VENTA_PRIVADA_TEST','price_amount'=>'450000000'],
    ['id'=>$rent,'component_key'=>$key,'operation'=>'Arriendo','source_name'=>'ARRIENDO_PRIVADO_TEST','price_amount'=>'2500000'],
    ['id'=>$unknown,'component_key'=>$key,'source_name'=>'SIN_OPERACION_TEST']],0);
$cookie=tempnam(sys_get_temp_dir(),'ga_study_http_');$xlsx=__DIR__.'/.runtime/study-wrong.xlsx';
function studyRequest(string $path,string|array|null $body=null,array $headers=[]): array {
    $curl=curl_init('http://127.0.0.1:8088'.$path);
    curl_setopt_array($curl,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_COOKIEJAR=>$GLOBALS['cookie'],CURLOPT_COOKIEFILE=>$GLOBALS['cookie'],CURLOPT_TIMEOUT=>20,CURLOPT_HTTPHEADER=>$headers]);
    if ($body!==null) {curl_setopt($curl,CURLOPT_POST,true);curl_setopt($curl,CURLOPT_POSTFIELDS,$body);}
    $text=curl_exec($curl);if ($text===false) throw new RuntimeException(curl_error($curl));
    $status=curl_getinfo($curl,CURLINFO_HTTP_CODE);curl_close($curl);return ['status'=>$status,'body'=>$text];
}
try {
    $login=studyRequest('/login');preg_match('/name="csrf-token" content="([a-f0-9]+)"/',$login['body'],$match);$token=$match[1];
    expect(studyRequest('/login',http_build_query(['_token'=>$token,'username'=>'ga_test','password'=>'Only-test-2026!']))['status']===303,'HTTP estudio inicia sesión real de prueba');
    $home=studyRequest('/');preg_match('/name="csrf-token" content="([a-f0-9]+)"/',$home['body'],$match);$token=$match[1];
    $path='/avaluos/'.$id.'/metodologia-valuatoria';$headers=['Accept: application/json'];
    $switch=['_token'=>$token,'version'=>1,'component'=>$key,'method'=>'renta'];
    expect(studyRequest($path.'/flujo',http_build_query($switch),$headers)['status']===200,'HTTP cambia de Mercado a Renta');
    $capture=studyRequest($path.'?stage=3&component='.$key.'&renta_paso=capture');
    expect($capture['status']===200 && str_contains($capture['body'],'ARRIENDO_PRIVADO_TEST') && !str_contains($capture['body'],'VENTA_PRIVADA_TEST') && !str_contains($capture['body'],'SIN_OPERACION_TEST'),
        'HTTP Renta no envía ventas ni muestras desconocidas al navegador');
    $rows=\App\Services\ComparableStudy::rows($samples->forAppraisal($id,1),$key,$ar->find($id,1));
    $post=['_token'=>$token,'matrix_complete'=>'1','version'=>1,'component_scope'=>$key,'study_scope'=>'renta:arriendo','comparables'=>$rows];
    expect(studyRequest($path.'/comparables/autoguardar',http_build_query(array_replace($post,['_token'=>'invalid'])),$headers)['status']===419,'HTTP aislamiento mantiene CSRF');
    expect(studyRequest($path.'/comparables/autoguardar',http_build_query(array_replace($post,['study_scope'=>'mercado:venta'])),$headers)['status']===409,'HTTP pantalla vieja de ventas no guarda en Renta');
    expect(studyRequest($path.'/comparables/autoguardar',http_build_query($post),$headers)['status']===200,'HTTP guarda solo investigación de arriendo');
    expect(studyRequest($path.'/comparables/autoguardar',http_build_query($post),$headers)['status']===409,'HTTP aislamiento mantiene bloqueo de versión antigua');
    $post['version']=2;$post['comparables'][0]['operation']='Venta';
    expect(studyRequest($path.'/comparables/autoguardar',http_build_query($post),$headers)['status']===422,'HTTP Renta rechaza operación Venta');
    $zip=new ZipArchive();$zip->open($xlsx,ZipArchive::CREATE|ZipArchive::OVERWRITE);
    $meta=['format'=>'sucasa-comparables-2','appraisal'=>$id,'scope'=>$key,'version'=>2,'study_scope'=>'mercado:venta'];
    $zip->addFromString('xl/workbook.xml','<workbook xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="_SuCasa" r:id="rId1"/></sheets></workbook>');
    $zip->addFromString('xl/_rels/workbook.xml.rels','<Relationships><Relationship Id="rId1" Target="worksheets/sheet1.xml"/></Relationships>');
    $zip->addFromString('xl/worksheets/sheet1.xml','<worksheet><sheetData><row><c r="A1" t="inlineStr"><is><t>'.htmlspecialchars(json_encode($meta),ENT_XML1|ENT_QUOTES,'UTF-8').'</t></is></c></row></sheetData></worksheet>');$zip->close();
    $upload=['_token'=>$token,'component_scope'=>$key,'study_scope'=>'renta:arriendo','excel'=>new CURLFile($xlsx,'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','ventas.xlsx')];
    expect(studyRequest($path.'/comparables/excel/revisar',$upload,$headers)['status']===422,'HTTP Excel de ventas no se previsualiza en Renta');
    $switch['version']=2;$switch['method']='mercado';expect(studyRequest($path.'/flujo',http_build_query($switch),$headers)['status']===200,'HTTP vuelve a Mercado');
    $salePage=studyRequest($path.'?stage=3&component='.$key);
    expect(str_contains($salePage['body'],'VENTA_PRIVADA_TEST') && !str_contains($salePage['body'],'ARRIENDO_PRIVADO_TEST'),'HTTP Mercado recupera ventas sin arriendos');
    $workflow->save($id,1,3,$key,['additional_methods'=>['renta']]);
    $assign=['_token'=>$token,'component'=>$key.':metodo:renta','source_scope'=>$key,'study_scope'=>'mercado:venta','version'=>2,'samples'=>[$sale]];
    expect(studyRequest($path.'/asignar-muestras',http_build_query($assign),$headers)['status']===422,'HTTP reasignación no traslada ventas a un recorrido de Renta');
    $otherId=$ar->create(2);
    expect(studyRequest('/avaluos/'.$otherId.'/metodologia-valuatoria/comparables/autoguardar',http_build_query($post),$headers)['status']===404,'HTTP propietario ajeno no accede a otra investigación');
    expect(count($samples->forAppraisal($id,1))===3,'HTTP cambiar métodos y guardar conserva todas las muestras originales');
    file_put_contents(__DIR__.'/.runtime/study-ui-id.txt',$id.'|'.$key);
    report();
} finally {if (is_file($cookie)) unlink($cookie);if (is_file($xlsx)) unlink($xlsx);}
