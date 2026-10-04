<?php
declare(strict_types=1);
(static function(PDO $app):void {
    $repo=new \App\Models\ResearchFactorScaleRepository($app);
    $scale=['kind'=>'ordinal','categories'=>"Sin vista\nInterior\nExterior",'definition'=>'Vista comprobada'];
    expect($repo->save(1,'view',0,$scale)===1,'escala permanente guarda versión inicial');
    expect($repo->all(1)['view']['categories']===$scale['categories'] && $repo->all(2)===[],'catálogo se reutiliza por propietario sin afectar a otros analistas');
    expectStatus(409,fn()=>$repo->save(1,'view',0,$scale),'creación concurrente no sobrescribe escala');
    expect($repo->save(1,'view',1,$scale)===2,'escala actualiza con versión correcta');
    expectStatus(409,fn()=>$repo->save(1,'view',1,$scale),'escala antigua no sobrescribe revisión');
})($app);
