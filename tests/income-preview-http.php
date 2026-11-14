<?php
declare(strict_types=1);
require __DIR__.'/lease-ledger-http.php';
$login=incomeRequest('/login');preg_match('/name="csrf-token" content="([a-f0-9]+)"/',$login['body'],$m);
incomeRequest('/login',['_token'=>$m[1],'username'=>'ga_test','password'=>'Only-test-2026!']);
$home=incomeRequest('/');preg_match('/name="csrf-token" content="([a-f0-9]+)"/',$home['body'],$m);
$record=$ar->find($id,1);$workflow=\App\Services\MethodologyWorkflow::saved($record)[$key];
$before=$workflow['income_valuation']['calculation'];
$data['projection']=array_replace($data['projection'],['income_source'=>'scenario','scenario_rent'=>'2941590.06','months'=>'60','growth'=>'7.1',
    'next_increase'=>'2027-01-01','date'=>'2026-09-30','cost_review'=>'','income_support'=>'Prueba: canon del historial, no renta verificada.',
    'projection_support'=>'60 meses; incremento hipotético 7,10 % anual cada enero.','vacancy'=>'0','arrears'=>'0','loss_support'=>'Prueba sin vacancias ni mora simuladas.']);
$data['costs']=[];$data['rates']=[];$data['adoption']=[];
$post=['_token'=>$m[1],'component'=>$key,'study_scope'=>'renta:arriendo','version'=>$record['methodology_version'],
    'context_hash'=>$o['income_context']['hash'],'payload'=>json_encode($data),'operation'=>'preview'];
expect(incomeRequest($endpoint,array_replace($post,['_token'=>'bad']),true)['status']===419,'flujo preliminar exige CSRF');
$r=incomeRequest($endpoint,$post,true);
expect($r['status']===200 && isset(json_decode($r['body'],true)['version']),'flujo preliminar guarda con versión confirmada');
expect(incomeRequest($endpoint,$post,true)['status']===409,'flujo preliminar rechaza versión obsoleta por HTTP');
$read=\App\Services\MethodologyWorkflow::saved($ar->find($id,1))[$key]['income_valuation'];
expect($read['calculation']===$before,'vista preliminar no reemplaza resultado final anterior');
expect(count($read['flow_preview']['result']['months'])===60 && $read['flow_preview']['result']['months'][0]['cash']===null,'HTTP conserva sesenta filas con flujo neto pendiente');
$html=incomeRequest($page)['body'];
expect(str_contains($html,'data-income-flow-preview') && str_contains($html,'2031-09') && str_contains($html,'2.941.590,06'),'recarga recupera tabla y canon de prueba');
expect(!str_contains($html,'Fatal error') && !str_contains($html,'Warning:'),'vista preliminar completa sin avisos PHP');
unlink($cookie);report();
