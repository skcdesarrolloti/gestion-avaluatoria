<?php
declare(strict_types=1);
(static function(): void {
    $parse=static fn($value)=>\App\Services\LeaseInput::fields(['monthly_history'=>$value]);
    $row=$parse(['2022-01'=>['rent_paid'=>'1.000,00','admin_paid'=>'0','review'=>'pending','support'=>'Recibo <enero>']])['monthly_history'];
    expect($row['2022-01']['rent_paid']==='1000.00' && $row['2022-01']['admin_paid']==='0','pagos transcritos conservan precisión y cero explícito');
    foreach (['2022-00','2022-13','2022-1','../../2022','1800-01'] as $month)
        expectStatus(422,fn()=>$parse([$month=>['rent_paid'=>'1']]),'historial rechaza mes inválido: '.$month);
    expectStatus(422,fn()=>$parse(['2022-01'=>['capitalization_rate'=>'1']]),'historial no admite variables de valoración');
    expectStatus(422,fn()=>$parse(['2022-01'=>['admin_paid'=>['1']]]),'historial rechaza estructuras en importes');
    expectStatus(422,fn()=>$parse(['2022-01'=>['rent_paid'=>'-1']]),'pago negativo requiere revisión, no cambio de signo');
    $merge=static fn($a,$b)=>\App\Services\LeaseMonthlyHistory::merge($a,$b);
    $saved=$merge($row,['2023-01'=>['rent_paid'=>'1200','review'=>'pending']]);
    expect($saved['2022-01']===$row['2022-01'] && count($saved)===2,'agregar otro año preserva meses anteriores');
    $changed=$merge($saved,['2022-01'=>['notes'=>'Pago parcial']]);
    expect($changed['2022-01']['rent_paid']==='1000.00' && $changed['2022-01']['notes']==='Pago parcial','parche mensual no borra otros campos');
    expectStatus(422,fn()=>$merge([],['2022-01'=>['review'=>'verified','admin_paid'=>'10']]),'validación manual requiere soporte');
    expectStatus(422,fn()=>$merge([],['2022-01'=>['review'=>'verified','support'=>'Documento']]),'validación manual requiere un importe');
    $verified=$merge([],['2022-01'=>['rent_paid'=>'0','review'=>'verified','support'=>'Recibo']]);
    expect($verified['2022-01']['rent_paid']==='0','cero documentado admite revisión explícita');
    expect($merge($verified,['2022-01'=>['rent_paid'=>'10']])['2022-01']['review']==='pending','edición parcial de mes revisado obliga a nueva revisión');
    expect($merge([],['2022-01'=>['rent_paid'=>'','admin_paid'=>'']])===[],'mes vacío no genera pago cero ni antecedente');
    expect(\App\Services\LeaseMonthlyHistory::year(['2022'],['start_date'=>'2024-01-01'])===2024,'año inválido consulta inicio documentado');
    $base=\App\Services\LeaseInput::fields(['initial_rent_amount'=>'1000','initial_rent_period'=>'mensual','adjustment_index_reference'=>'IPC diciembre anterior']);
    expect(!isset($base['rent_amount']) && $base['initial_rent_amount']==='1000','canon inicial no reemplaza ni proyecta canon vigente');
})();
