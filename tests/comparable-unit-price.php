<?php
use App\Services\ComparableUnitPrice;
(function (): void {
    $row=['ph_regime'=>'si','private_built_m2'=>'100','private_free_m2'=>'20','area_m2'=>'124',
        'areas_source'=>'Cuadro de áreas verificado','operation'=>'Venta','price_unit'=>'precio_total',
        'price_amount'=>'500000000','negotiation_discount'=>'25000000','ph_parking_presence'=>'si'];
    $v=ComparableUnitPrice::values($row);
    expect($v['offer_per_m2']==='5000000.00' && $v['negotiated_per_m2']==='4750000.00','PH divide precio por área privada construida sin sumar libres o anexos');
    expect(ComparableUnitPrice::values(array_replace($row,['private_built_m2'=>'12.345']))['unit_area_m2']==='12.3450','área privada con tres decimales no se convierte en miles');
    expect(str_contains($v['unit_price_status'],'no es valor depurado'),'cociente PH no se presenta como depurado ni adoptado');
    foreach (['areas_source'=>'','private_built_m2'=>'0','ph_regime'=>'por_verificar'] as $key=>$value)
        expect(ComparableUnitPrice::values(array_replace($row,[$key=>$value]))['negotiated_per_m2']==='', 'unitario no presume dato faltante: '.$key);
    expect(ComparableUnitPrice::values(array_replace($row,['negotiation_discount'=>'']))['negotiated_per_m2']==='', 'unitario negociado sin descuento confirmado queda pendiente');
    expect(ComparableUnitPrice::values(array_replace($row,['price_unit'=>'valor_m2','price_amount'=>'5000000','negotiation_discount'=>'250000']))['negotiated_per_m2']==='4750000.00','precio ya unitario no se divide dos veces');
    expect(ComparableUnitPrice::values(array_replace($row,['operation'=>'Arriendo','price_unit'=>'canon_mensual']))['unit_price_currency']==='COP/m²/mes','canon conserva dimensión mensual');
    expect(ComparableUnitPrice::values(array_replace($row,['price_unit'=>'canon_mensual']))['offer_per_m2']==='', 'venta con unidad de arriendo no genera cociente');
    $nph=array_replace($row,['ph_regime'=>'no','area_basis'=>'Área construida publicada']);
    expect(ComparableUnitPrice::values($nph)['unit_area_m2']==='124.0000','NPH usa base publicada identificada sin heredar área privada');
    expect(ComparableUnitPrice::values(array_replace($nph,['area_basis'=>'']))['offer_per_m2']==='', 'NPH sin base identificada queda pendiente');
    expect(!isset(\App\Services\ComparableCaptureDetail::normalize($row+['offer_per_m2'=>'1'])['offer_per_m2']), 'valor unitario adulterado no se persiste');
})();
