<?php
declare(strict_types=1);
require_once __DIR__.'/income-valuation-fixture.php';
(static function(): void {
    $s=incomeValuationFixture();$p=&$s['projection'];
    $p=array_replace($p,['income_source'=>'scenario','scenario_rent'=>'2941590.06','date'=>'2026-09-30','months'=>'60','growth'=>'7.1','next_increase'=>'2027-01-01','cost_review'=>'','cost_support'=>'Administración pendiente de validar.']);
    $s['rates']=[];$s['adoption']=[];$s['costs']=[['name'=>'Administración PH','payer'=>'','amount'=>'','source'=>'Pendiente']];
    $context=['leases'=>[],'hash'=>'test','value_date'=>'2026-09-30'];
    $got=\App\Services\IncomeFlowPreview::calculate($s,$context);
    expect(count($got['months'])===60 && $got['months'][0]['month']==='2026-10' && $got['months'][59]['month']==='2031-09','preliminar genera sesenta cierres sin exigir costos ni tasas');
    expect(abs($got['months'][0]['potential']-2941590.06)<1e-6 && abs($got['months'][3]['potential']-2941590.06*1.071)<1e-6,'preliminar aplica crecimiento únicamente desde enero');
    $sum=2941590.06*3;
    for ($y=1;$y<=5;$y++) $sum+=2941590.06*pow(1.071,$y)*($y===5?9:12);
    expect(abs($sum-$got['total_gross'])<1e-5 && abs($got['annual_gross']-2941590.06*12)<1e-6,'suma independiente del horizonte no se confunde con renta anual de directa');
    expect($got['months'][0]['costs']===null && $got['months'][0]['cash']===null && $got['annual_net']===null,'PH no validada no se transforma en gasto cero ni flujo neto');
    expect($got['example']===true && $got['months'][0]['pv']===null,'escenario permanece de prueba y sin valor presente inventado');
    $full=\App\Services\IncomeValuationCalculator::calculate($s,$context);
    expect($full['direct']['value']===null && $full['dcf']['value']===null,'preliminar no debilita requisitos para valorar por ambos métodos');
    $p['vacancy']='';$unknown=\App\Services\IncomeFlowPreview::calculate($s,$context);
    expect($unknown['months'][0]['effective']===null,'pérdidas sin revisar tampoco se asumen cero');
    $s=incomeValuationFixture();$s['events']=[['month'=>'2027-03','rent'=>'0','extra'=>'100','capex'=>'50','source'=>'Prueba de evento']];
    $preview=\App\Services\IncomeFlowPreview::calculate($s,[]);$final=\App\Services\IncomeValuationCalculator::calculate($s,[]);
    foreach ($preview['months'] as $i=>$r) expect(abs($r['cash']-$final['dcf']['months'][$i]['cash'])<1e-8,'preliminar y FCD usan mismo calendario y gastos: '.$r['month']);
    $s['events'][]=['month'=>'2030-01','rent'=>'0','source'=>'Fuera'];
    expect(\App\Services\IncomeFlowPreview::calculate($s,[])['months']===[],'preliminar rechaza eventos fuera del horizonte sin omitirlos');
    $s=incomeValuationFixture();$s['projection']['date']='2026-01-15';$s['projection']['months']='1';$s['projection']['market_rent']='3100';
    $r=\App\Services\IncomeFlowPreview::calculate($s,[])['months'][0];expect(abs($r['potential']-1600)<1e-8,'preliminar conserva fracción del primer mes');
})();
