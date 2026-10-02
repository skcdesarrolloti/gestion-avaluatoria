<?php
declare(strict_types=1);
(static function (): void {
    $record = ['id'=>str_repeat('a',32)];
    $catalog = ['selects'=>\App\Support\AppraisalCatalog::selectFields()];
    $render = static function (array $unit) use ($record,$catalog): string {
        ob_start(); require BASE_PATH . '/app/Views/appraisals/subject-unit-kind.php'; return ob_get_clean();
    };
    $annex = ['id'=>str_repeat('b',32), 'unit_kind'=>'annex', 'construction_type'=>'deposito', 'property_type'=>''];
    $html = $render($annex);
    expect(str_contains($html,'Depósito / cuarto útil') && !str_contains($html,'<select')
        && str_contains($html,'detail=basicos') && str_contains($html,'unit=' . $annex['id']),
        'depósito muestra clasificación de 3.3 y enlace propio sin lista de inmuebles principales');
    expect(str_contains($render(array_replace($annex,['construction_type'=>'parqueo'])), 'Garaje / parqueadero / celda de parqueo'),
        'garaje muestra clasificación guardada como parqueo');
    expect(str_contains($render(array_replace($annex,['construction_type'=>''])), 'Pendiente de clasificar'),
        'anexo sin tipo pide diligenciar sin heredar oficina');
    $property = $render(array_replace($annex,['unit_kind'=>'property','property_type'=>'oficina']));
    expect(str_contains($property,'<select') && str_contains($property,'value="oficina" selected'),
        'unidad principal conserva selección de su tipo de inmueble');
})();
