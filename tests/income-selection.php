<?php
declare(strict_types=1);
use App\Services\{IncomeSampleSelection as Selection,IncomeSampleStatistics as Stats};
(static function(): void {
    $rows=[];foreach ([100,100,100,300] as $i=>$v) $rows[]=['id'=>str_pad((string)$i,32,'0',STR_PAD_LEFT),'value'=>$v];
    $s=Stats::summarize($rows);
    expect(abs($s['mean']-150)<1e-10 && $s['sd']===100.0 && abs($s['cv']-66.6666666667)<1e-8,'estadísticos muestrales conocidos sin redondeo previo');
    expect($s['q1']===100.0 && $s['q3']===150.0 && $s['median']===100.0,'cuartiles inclusivos de Mercado');
    expect(Stats::summarize([])['cv']===null && Stats::summarize([$rows[0]])['cv']===null,'cero y una muestra no se presentan como CV cero');
    expect(abs(Stats::normalTail(1.9599639845)-.05)<2e-7,'cola bilateral normal con referencia independiente');
    $many=[];for($i=1;$i<=30;$i++) $many[]=['id'=>(string)$i,'value'=>100+($i%2)];
    $many[]=['id'=>'outlier','value'=>1000];$stats=Stats::summarize($many);
    expect(in_array('Chauvenet',$stats['flags']['outlier'],true),'Chauvenet señala extremo bajo hipótesis normal sin eliminarlo');
    $source=['id'=>str_repeat('a',32),'sample_index'=>1,'price_amount'=>'4000000.00','area_m2'=>'40.00','price_unit'=>'canon_mensual','operation'=>'Arriendo'];
    expect(Selection::rows([$source])[0]['value']===100000.0,'divide importe total mensual por área publicada');
    foreach ([['area_m2'=>''],['price_unit'=>'valor_m2'],['operation'=>'Venta'],['source_url'=>'https://example.com/oficina-en-venta/x'],['active'=>'no'],['intake_state'=>'not_selected']] as $change)
        expect(Selection::rows([array_replace($source,$change)])[0]['value']===null,'datos incompatibles quedan fuera del cociente sin ser cero');
    $p=['sample'=>$rows[3]['id'],'category'=>'different','reason'=>'Coworking con servicios','support'=>'Aviso y revisión','operation'=>'exclude'];
    expectStatus(422,fn()=>Selection::input(array_replace($p,['reason'=>''])),'exclusión requiere justificación');
    expectStatus(422,fn()=>Selection::input(array_replace($p,['support'=>''])),'exclusión requiere soporte');
    expectStatus(422,fn()=>Selection::input(array_replace($p,['category'=>'reach_cv'])),'no existe exclusión por alcanzar el CV');
    $actor=['id'=>1,'name'=>'Analista'];$state=Selection::change([],$rows,Selection::input($p),$actor,'2026-11-13T17:00:00Z');
    expect(Selection::report($rows,$state)['current']['cv']===0.0,'exclusión recalcula solo muestra retenida');
    expect($state['events'][0]['before']['n']===4 && $state['events'][0]['after']['n']===3,'constancia conserva antes y después');
    $original=$state['events'][0];$state=Selection::change($state,$rows,array_replace($p,['operation'=>'restore','category'=>'restore','reason'=>'Comparable después de revisión']),$actor,'2026-11-13T17:01:00Z');
    expect(count($state['events'])===2 && $state['events'][0]===$original && !isset($state['excluded'][$p['sample']]),'reincorporar añade evento sin borrar historial');
    $draft=Selection::change($state,$rows,array_replace($p,['operation'=>'draft','reason'=>'']),$actor,'ignored');
    expect($draft['events']===$state['events'] && $draft['excluded']===$state['excluded'],'borrador no altera decisiones ni estadísticas');
    expectStatus(422,fn()=>Selection::change($state,$rows,array_replace($p,['sample'=>str_repeat('f',32)]),$actor,'now'),'no acepta muestra fuera del estudio');
})();
