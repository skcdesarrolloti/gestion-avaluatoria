<?php
declare(strict_types=1);
(static function () use ($app): void {
    $appraisals=new App\Models\AppraisalRepository($app); $aid=$appraisals->create(1);
    $repo=new App\Models\AppraisalComparableRepository($app); $a=bin2hex(random_bytes(16)); $b=bin2hex(random_bytes(16));
    $rows=[['id'=>$a,'source_name'=>'Fuente A','price_amount'=>'100','property_group'=>$a,'intake_state'=>'selected_pending','component_key'=>'','research_primary'=>'si','capture_confirmation'=>'confirmed'],
        ['id'=>$b,'source_name'=>'Fuente B','price_amount'=>'110','property_group'=>$a,'intake_state'=>'selected_pending','intake_note'=>'Precio por corroborar','research_primary'=>'no','capture_confirmation'=>'confirmed']];
    $repo->saveAll($aid,1,$rows,0); $saved=$repo->forAppraisal($aid,1);
    expect($saved[0]['research_primary']==='si' && $saved[1]['research_primary']==='no' && $saved[0]['capture_confirmation']==='confirmed','BD recupera ficha principal y respaldo tras consolidar sin eliminar anuncios');
    expect(count(App\Services\ComparableIntake::groups($saved,true))===1 && array_column($saved,'price_amount')===['100.00','110.00'], 'BD vincula dos anuncios sin alterar sus importes e identidad');
    $saved[0]+= ['latitude'=>'10.4','longitude'=>'-75.5'];
    $saved[0]=array_replace($saved[0],['latitude'=>'10.4','longitude'=>'-75.5','location_verification'=>'approximate','location_source'=>'Visita ficticia','verification_detail'=>'Analista, fecha y soporte ficticios','published_location'=>'10.3, -75.4 · referencia de barrio']);
    $repo->saveAll($aid,1,$saved,1); $saved=$repo->forAppraisal($aid,1);
    expect($saved[0]['location_verification']==='approximate' && $saved[0]['published_location']==='10.3, -75.4 · referencia de barrio' && $saved[1]['intake_note']==='Precio por corroborar', 'BD conserva punto manual separado de referencia publicada y pendientes de otra fuente');
    expectStatus(409,fn()=> $repo->saveAll($aid,1,$rows,1),'selección obsoleta no sobrescribe ubicación verificada');
    $invalid=$saved; $invalid[0]['property_group']='incorrecto';
    expectStatus(422,fn()=> $repo->saveAll($aid,1,$invalid,2),'identidad inválida revierte guardado completo');
    expect(count($repo->forAppraisal($aid,1))===2 && $repo->forAppraisal($aid,1)[0]['location_verification']==='approximate','rollback conserva anuncios y verificación');
})();
