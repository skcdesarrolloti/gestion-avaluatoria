<?php
return json_encode(['v'=>1,'basis'=>'adjusted','confidence'=>.95,'step'=>3,'notes'=>(object)[],'conclusion'=>'Pendiente',
    'result'=>['at'=>'2026-10-11T12:00:00.000Z','basis'=>'adjusted','simulated'=>false,'stamp'=>'entradas','reconstructed'=>false,'bootstrap'=>null,
        'rows'=>array_map(fn($i)=>['id'=>str_repeat((string)$i,32),'label'=>'Muestra '.$i,'y'=>100*$i,'area'=>1,'offer'=>100*$i,'discount'=>0,'final'=>100*$i,'reasons'=>[],'areaNote'=>'','regime'=>'PH'],[1,2]),
        'summary'=>['confidence'=>.95,'mean'=>150,'median'=>150,'q1'=>125,'q3'=>175,'modes'=>[],
            'estimators'=>array_map(fn($label)=>['label'=>$label,'value'=>150,'mape'=>37.5],['Media','Mediana','Media acotada (40% total)','Media geométrica'])]]],JSON_THROW_ON_ERROR);
