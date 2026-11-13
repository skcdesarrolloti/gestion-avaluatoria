<?php
declare(strict_types=1);
require __DIR__.'/income-valuation-http.php';
$login=incomeRequest('/login');preg_match('/name="csrf-token" content="([a-f0-9]+)"/',$login['body'],$m);
incomeRequest('/login',['_token'=>$m[1],'username'=>'ga_test','password'=>'Only-test-2026!']);
$home=incomeRequest('/');preg_match('/name="csrf-token" content="([a-f0-9]+)"/',$home['body'],$m);$csrf=$m[1];
$ar=new \App\Models\AppraisalRepository($app);$record=$ar->find($id,1);$leaseId=str_repeat('f',32);
$lease=['reference'=>'Contrato oficina · PRUEBA LOCAL','status'=>'current','scope'=>'Oficina y anexos incluidos','economic_source'=>'own','rent_amount'=>'1273.27','rent_period'=>'mensual',
    'initial_rent_amount'=>'1000','initial_rent_period'=>'mensual','start_date'=>'2022-01-01','end_date'=>'2027-12-31','first_adjustment_date'=>'2023-01-01','history_rule'=>'annual','support'=>'Contrato ficticio para verificar interfaz',
    'rent_adjustments'=>[str_repeat('a',16)=>['date'=>'2023-01-01','rate'=>'10','source'=>'Índice ficticio documentado'],str_repeat('b',16)=>['date'=>'2024-01-01','rate'=>'5','source'=>'Índice ficticio documentado'],
        str_repeat('c',16)=>['date'=>'2025-01-01','rate'=>'6','source'=>'Índice ficticio documentado'],str_repeat('d',16)=>['date'=>'2026-01-01','rate'=>'4','source'=>'Índice ficticio documentado']],
    'monthly_history'=>['2022-01'=>['occupancy'=>'occupied','rent_billed'=>'1000','rent_paid'=>'800','admin_billed'=>'100','admin_paid'=>'100','admin_received'=>'100','vat_rate'=>'19','vat_paid'=>'152','cost_insurance'=>'40','review'=>'verified','support'=>'Factura y recibos ficticios'],
        '2022-02'=>['occupancy'=>'vacant','rent_billed'=>'0','rent_paid'=>'0','admin_billed'=>'100','admin_paid'=>'100','admin_payer'=>'owner','review'=>'verified','support'=>'Acta ficticia de entrega']]];
$lp=['_token'=>$csrf,'version'=>$record['lease_version'],'component'=>$key,'lease_id'=>$leaseId,'lease_units'=>[$key],'lease_payload'=>json_encode($lease),'lease_year'=>'2022','return_step'=>'evidence'];
$r=incomeRequest($route.'/renta/arriendo',$lp,true);$saved=json_decode($r['body'],true);
expect($r['status']===200 && isset($saved['income_context']['leases'][$leaseId]['ledger']),'historial JSON guarda y devuelve contexto compartido confirmado');
expect(incomeRequest($route.'/renta/arriendo',$lp,true)['status']===409,'historial no sobrescribe desde pestaña obsoleta');
$record=$ar->find($id,1);$unit=array_column($ar->units($id,1),null,'id')[$key];
$op=['_token'=>$csrf,'component'=>$key,'version'=>$record['version'],'occupation_version'=>$unit['occupation_version'],'origin'=>['value_date'=>'2026-09-30','label'=>'Oficina · prueba de historial','property_type'=>'oficina','area_private_m2'=>'80.50','surface_source'=>'Plano ficticio'],
    'occupation'=>['occupancy'=>'rented','date'=>'2026-09-30','source'=>'Visita ficticia']];
$originPath=$route.'/renta/origen';
expect(incomeRequest($originPath,array_replace($op,['_token'=>'bad']),true)['status']===419,'edición de origen exige CSRF');
$r=incomeRequest($originPath,$op,true);$o=json_decode($r['body'],true);
expect($r['status']===200 && $o['income_context']['value_date']==='2026-09-30','edición de origen confirma fecha sin navegar al encargo');
expect(incomeRequest($originPath,$op,true)['status']===409,'origen HTTP protege versiones concurrentes');
$foreign=$ar->create(2);expect(incomeRequest('/avaluos/'.$foreign.'/metodologia-valuatoria/renta/origen',$op,true)['status']===404,'origen HTTP rechaza expediente ajeno');
$page=$route.'?stage=3&method=renta&component='.$key.'&renta_paso=evidence&lease='.$leaseId.'&lease_year=2022';
$html=incomeRequest($page);
expect($html['status']===200 && str_contains($html['body'],'Canon reconstruido') && str_contains($html['body'],'Historial y contrato') && str_contains($html['body'],'Costos con soporte'),'historial, proyección y costos recuperables por HTTP');
expect(substr_count($html['body'],'id="lease-form"')===1 && str_contains($html['body'],'popovertarget=') && str_contains($html['body'],'renta/origen'),'sin ficha duplicada y con ayuda normativa contextual y edición de origen');
expect(!str_contains($html['body'],'Fatal error') && !str_contains($html['body'],'Warning:'),'pantalla completa sin avisos PHP');
expect(incomeRequest($originPath,array_replace($op,['origin'=>['label'=>['invalid']]]))['status']===303,'origen conserva fallback de entrada mal formada sin error fatal');
$fallback=incomeRequest($page);
expect($fallback['status']===200 && !str_contains($fallback['body'],'Fatal error') && str_contains($fallback['body'],'Formato o longitud inválida'),'origen muestra error de campo y formulario recuperable');
preg_match('/data-lease-form="([^"]+)"/',$html['body'],$formMatch);
$formState=json_decode(html_entity_decode($formMatch[1],ENT_QUOTES|ENT_HTML5,'UTF-8'),true);
expect(!isset($formState['values']['unit_ids']) && str_contains($html['body'],'name="lease_units[]"'),'datos del contrato y vínculos se envían por sus canales separados');
$formValues=\App\Services\LeaseInput::fields($formState['values']);
expect($formValues['monthly_history']['2022-01']['rent_paid']==='800','borrador renderizado puede guardarse de nuevo sin rechazar metadatos internos');
$data['costs'][0]=array_replace($data['costs'][0],['category'=>'insurance','reference_percent'=>'5','reference_basis'=>'effective','reference_source'=>'Cotización ficticia oficina, Cartagena, 2026']);
$vp=['_token'=>$csrf,'component'=>$key,'study_scope'=>'renta:arriendo','version'=>$ar->find($id,1)['methodology_version'],'context_hash'=>$o['income_context']['hash'],'payload'=>json_encode($data)];
expect(incomeRequest($endpoint,$vp,true)['status']===200,'escenario admite nuevas referencias con contexto actualizado del origen');
$read=\App\Services\MethodologyWorkflow::saved($ar->find($id,1))[$key];
expect($read['income_valuation']['costs'][0]['reference_percent']==='5','porcentaje de referencia y soporte persisten separados del importe adoptado');
file_put_contents(__DIR__.'/.runtime/renta-ledger-ui.json',json_encode(compact('id','key','page','leaseId')));
unlink($cookie);report();
