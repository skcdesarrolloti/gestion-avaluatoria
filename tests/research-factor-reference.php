<?php
declare(strict_types=1);
(static function(): void {
    $catalog=\App\Services\ResearchFactorCatalog::all();
    $canonical=['factors'=>['view'=>[
        'kind'=>$catalog['view']['kind'],'categories'=>$catalog['view']['categories'],
        'definition'=>$catalog['view']['why'],'decision'=>'investigate',
    ]]];
    \App\Services\ResearchFactorReference::validateFixed($canonical,[]);
    expect(str_contains(\App\Services\ResearchFactorReference::scale($catalog['view']),'0 = Sin vista') && str_contains(\App\Services\ResearchFactorReference::scale($catalog['view']),'3 = Exterior: paisajística'),'Vista muestra jerarquía de cuatro niveles aprobada');
    $legacy=$canonical;
    $legacy['factors']['view']['kind']='ordinal';
    $legacy['factors']['view']['categories']="Sin vista\nInterior\nPanorámica";
    $legacy['factors']['view']['definition']='Escala anterior documentada';
    $changed=$legacy;
    $changed['factors']['view']['decision']='model';
    \App\Services\ResearchFactorReference::validateFixed($changed,$legacy);
    expect(true,'permite elegir candidatos conservando escala histórica');
    $changed['factors']['view']['categories']="Interior\nSin vista\nPanorámica";
    expectStatus(422,fn()=>\App\Services\ResearchFactorReference::validateFixed($changed,$legacy),'impide recodificar una escala desde el expediente');
    expectStatus(422,fn()=>\App\Services\ResearchFactorReference::validateFixed($legacy,[]),'nuevo expediente usa clasificación del catálogo');
    expect(\App\Services\ResearchFactorReference::portals('oficina','bathrooms')!==[],'oficina referencia campos documentados de baños');
    expect(\App\Services\ResearchFactorReference::portals('tipo-no-investigado','bathrooms')===[],'no extrapola disponibilidad desde otro tipo');
})();
