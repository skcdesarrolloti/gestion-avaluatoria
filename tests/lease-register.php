<?php
declare(strict_types=1);
(static function(): void {
    $data=\App\Services\LeaseInput::fields(['rent_amount'=>'3.500.000,50','start_date'=>'2024-02-29','adjustment_rule'=>'IPC según cláusula <4>']);
    expect($data['rent_amount']==='3500000.50' && $data['adjustment_rule']==='IPC según cláusula <4>','contrato conserva importe y cláusula sin proyección');
    foreach (['-1','1e9','12,3456','12.345,678','texto','9999999999999'] as $invalid)
        expectStatus(422,fn()=>\App\Services\LeaseInput::fields(['rent_amount'=>$invalid]),'importe inválido no se convierte a otro canon: '.$invalid);
    expectStatus(422,fn()=>\App\Services\LeaseInput::fields(['end_date'=>'2025-02-29']),'fecha imposible rechazada');
    expectStatus(422,fn()=>\App\Services\LeaseInput::validate(['start_date'=>'2026-01-01','end_date'=>'2025-01-01']),'vigencia invertida rechazada');
    expectStatus(422,fn()=>\App\Services\LeaseInput::fields(['capitalization_rate'=>'0.07']),'ficha no admite tasas de valoración');
    expect(\App\Services\LeaseInput::fields(['rent_amount'=>''])['rent_amount']==='','importe desconocido no se convierte a cero');
    $record=['rent_amount'=>'3000000','lease_register'=>json_encode(['shared'=>['unit_ids'=>['office','garage'],'economic_source'=>'assignment']])];
    $office=\App\Services\LeaseRegister::forUnit($record,'office');$garage=\App\Services\LeaseRegister::forUnit($record,'garage');
    expect($office===$garage && !isset($office['shared']['rent_amount']),'oficina y anexo consultan un único contrato sin copiar canon');
    expect(\App\Services\LeaseRegister::economic($office['shared'],$record)['rent_amount']==='3000000','referencia al canon del encargo conserva origen');
    $record['rent_amount']='3200000';
    expect(\App\Services\LeaseRegister::economic($garage['shared'],$record)['rent_amount']==='3200000','referencia económica es viva para todas las unidades');
    $catalog=(new \App\Models\IgacTypologyRepository())->optionsByCategory();$total=array_sum(array_map('count',$catalog));
    foreach (\App\Support\AppraisalCatalog::allowedValues('tipo_inmueble') as $type)
        expect(count(\App\Support\IgacTypologySelection::options($type,'','',$catalog,true))===$total,'catálogo completo disponible para '.$type);
    $original=$_POST;$_POST=[];$input=\App\Services\AppraisalAssignmentInput::data();$_POST=$original;
    expect(!array_key_exists('rent_amount',$input) && !array_key_exists('income_notes',$input),'resumen de capítulo 1 no envía vacíos para borrar antecedentes');
    expectStatus(422,fn()=>\App\Services\UnitOccupation::input(['occupancy'=>'inventado']),'ocupación por unidad valida su lista');
})();
