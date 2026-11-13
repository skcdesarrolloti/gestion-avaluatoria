<?php
declare(strict_types=1);
function incomeValuationFixture(): array
{
    $s=['projection'=>['date'=>'2026-12-31','months'=>'12','income_source'=>'market','market_rent'=>'1000','income_support'=>'Prueba: oficina y anexos incluidos; canon sin PH ni IVA.',
        'growth'=>'0','vacancy'=>'0','arrears'=>'0','loss_support'=>'Historial documentado sin pérdidas en este escenario.','projection_support'=>'Escenario constante por doce meses.',
        'cost_review'=>'confirmed','cost_support'=>'Costo operativo completo del propietario para esta prueba.'],
        'costs'=>[['name'=>'Gasto operativo','payer'=>'owner','amount'=>'200','basis'=>'monthly','growth'=>'0','source'=>'Presupuesto verificable de prueba']], 'events'=>[],
        'rates'=>[
            ['id'=>str_repeat('a',16),'name'=>'Tasa de mercado','purpose'=>'direct','method'=>'market','basis'=>'net','income'=>'9600','price'=>'120000','date'=>'2026-12-31','source'=>'Contrato y venta del mismo inmueble, netos.'],
            ['id'=>str_repeat('b',16),'name'=>'Descuento sustentado','purpose'=>'discount','method'=>'build','basis'=>'cash','reference'=>'6','premium'=>'4','date'=>'2026-12-31','source'=>'Referencia COP y prima documentadas de prueba.'],
            ['id'=>str_repeat('c',16),'name'=>'Salida observada','purpose'=>'exit','method'=>'manual','basis'=>'net','rate'=>'8','date'=>'2026-12-31','source'=>'Salida sustentada para la prueba.']],
        'adoption'=>['direct_id'=>str_repeat('a',16),'discount_id'=>str_repeat('b',16),'exit_id'=>str_repeat('c',16),'direct_reason'=>'Comparables verificados.',
            'discount_reason'=>'Riesgos del activo en COP nominal.', 'exit_reason'=>'Continuidad estabilizada y costos completos.','terminal_mode'=>'capitalized','terminal_growth'=>'0','selling_cost'=>'0','conclusion'=>'Caso de prueba.']];
    foreach ($s as $group=>$values) $s[$group]=\App\Services\IncomeValuationInput::section($group,$values);
    return $s;
}
