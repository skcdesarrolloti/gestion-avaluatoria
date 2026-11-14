<?php
declare(strict_types=1);
(static function() use ($app): void {
    $repo=new \App\Models\AppraisalRepository($app);$id=$repo->create(1);$repo->ensureUnits($id,1,1,1);
    $app->prepare('UPDATE appraisals SET igac_property_units_count=1,igac_annex_units_count=1 WHERE id=?')->execute([$id]);
    $units=$repo->units($id,1);$key=$units[0]['id'];$annex=$units[1]['id'];
    (new \App\Models\MethodologyWorkflowRepository($app))->save($id,1,0,$key,['method'=>'renta','income_selection'=>['excluded'=>['sale'=>['reason'=>'Venta conservada']]]]);
    $lease=str_repeat('d',32);$adj=str_repeat('a',16);
    $repo->saveLease($id,1,0,$lease,[$key,$annex],['reference'=>'Historia compartida','history_rule'=>'annual','initial_rent_amount'=>'1000','initial_rent_period'=>'mensual',
        'rent_adjustments'=>[$adj=>['date'=>'2023-01-01','rate'=>'10','source'=>'Cláusula e IPC de prueba']],
        'monthly_history'=>['2022-01'=>['occupancy'=>'vacant','rent_paid'=>'0','cost_tax'=>'50','review'=>'verified','support'=>'Visita y recibo']]]);
    $repo->saveLease($id,1,1,$lease,[$key,$annex],['monthly_history'=>['2023-02'=>['rent_billed'=>'1100','vat_rate'=>'19','admin_received'=>'80']]]);
    $repo->saveLease($id,1,2,$lease,[$key,$annex],['notes'=>'Cliente anterior']);
    $read=\App\Services\LeaseRegister::read($repo->find($id,1))[$lease];
    expect($read['rent_adjustments'][$adj]['rate']==='10' && count($read['monthly_history'])===2,'historial e incrementos sobreviven a formularios parciales y otros años');
    expect($read['monthly_history']['2022-01']['occupancy']==='vacant' && $read['unit_ids']===[$key,$annex],'vacancia soportada y pagos siguen en un contrato compartido');
    $origin=new \App\Models\IncomeOriginRepository($app);$record=$repo->find($id,1);
    $post=['version'=>$record['version'],'occupation_version'=>0,'origin'=>['value_date'=>'2026-09-30','label'=>'Oficina de prueba','property_type'=>'oficina','area_private_m2'=>'80,50','surface_source'=>'Escritura'],
        'occupation'=>['occupancy'=>'rented','date'=>'2026-09-30','source'=>'Visita y contrato']];
    $before=$record['lease_register'];$result=$origin->save($id,1,$key,$post);$record=$repo->find($id,1);
    $readUnit=array_column($repo->units($id,1),null,'id')[$key];
    expect($record['value_date']==='2026-09-30' && $readUnit['area_private_m2']==='80.50','edición desde Renta actualiza fecha y área en sus columnas originales');
    expect($record['lease_register']===$before && \App\Services\MethodologyWorkflow::saved($record)[$key]['income_selection']['excluded']['sale']['reason']==='Venta conservada','corregir origen conserva contratos y constancia de venta');
    expect($result['income_context']['value_date']==='2026-09-30' && $result['occupation_version']===1,'origen devuelve contexto actualizado y versiones confirmadas');
    expectStatus(409,fn()=>$origin->save($id,1,$key,$post),'edición de origen obsoleta no sobrescribe');
    expectStatus(404,fn()=>$origin->save($id,2,$key,$post),'edición de origen rechaza propietario ajeno');
    $post['version']=$result['version'];$post['occupation_version']=0;
    expectStatus(409,fn()=>$origin->save($id,1,$key,$post),'ocupación concurrente revierte toda la edición de origen');
    expect($repo->find($id,1)['version']===$result['version'],'conflicto no incrementa versión ni altera fecha');
    $post['occupation_version']=1;$post['origin']['area_private_m2']='-5';
    expectStatus(422,fn()=>$origin->save($id,1,$key,$post),'áreas inválidas rechazadas en servidor');
    $repo->saveLease($id,1,3,$lease,[$key,$annex],['history_rule'=>'ipc','history_calendar'=>'january','history_ipc_points'=>'2',
        'admin_adjustments'=>[$adj=>['date'=>'2025-01-01','amount'=>'300000','source'=>'Acta']],
        'vacancy_periods'=>[$adj=>['start'=>'2024-01-01','end'=>'2024-02-29','source'=>'Entregas']]]);
    $repo->saveLease($id,1,4,$lease,[$key,$annex],['admin_adjustments'=>[$adj=>['amount'=>'320000']],'vacancy_periods'=>[$adj=>['source'=>'Actas verificadas']]]);
    $read=\App\Services\LeaseRegister::read($repo->find($id,1))[$lease];
    expect($read['admin_adjustments'][$adj]['source']==='Acta' && $read['vacancy_periods'][$adj]['end']==='2024-02-29','excepciones y vacancias preservan campos omitidos al guardar');
    expect(count($read['monthly_history'])===2 && $read['rent_adjustments'][$adj]['rate']==='10' && $read['unit_ids']===[$key,$annex],'referencia automática conserva pagos, cambios y unidades compartidas');
})();
