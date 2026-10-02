<?php
declare(strict_types=1);
namespace App\Services;

final class ComparableUnitPrice
{
    public static function fields(): array
    {
        return [
            'unit_area_m2'=>['Área usada para el cociente (m²)', 'calculated', 'shared'],
            'unit_area_label'=>['Base del cociente por m²', 'calculated', 'shared'],
            'offer_per_m2'=>['Oferta por m² · preliminar', 'calculated', 'shared'],
            'negotiated_per_m2'=>['Negociado por m² · preliminar', 'calculated', 'shared'],
            'unit_price_currency'=>['Unidad del valor por m²', 'calculated', 'shared'],
            'unit_price_status'=>['Estado del valor por m² y componentes', 'calculated', 'shared'],
        ];
    }
    public static function values(array $row): array
    {
        $out=array_fill_keys(array_keys(self::fields()), '');
        $regime=$row['ph_regime'] ?? '';
        $ph=$regime==='si' && ($row['ph_special'] ?? '')!=='condominio';
        $out['unit_area_label']=$ph ? 'Área privada construida; no suma garaje, depósito ni área libre' : 'Área publicada; confrontar terreno/construcción en M4';
        $out['unit_price_status']='Pendiente: verifica régimen, área, base y soporte antes de calcular.';
        if (!in_array($regime,['si','no'],true) || trim((string)($row['areas_source'] ?? ''))==='') return $out;
        if (!$ph && trim((string)($row['area_basis'] ?? ''))==='') return $out;
        $private=str_replace(',', '.', trim((string)($row['private_built_m2'] ?? '')));
        $area=$ph ? (is_numeric($private)?(float)$private:null) : ComparableNegotiation::amount($row['area_m2'] ?? '');
        if ($area===null || $area<=0) return $out;
        $out['unit_area_m2']=number_format($area,4,'.','');
        $unit=$row['price_unit'] ?? '';
        $operation=$row['operation'] ?? '';
        $valid=($operation==='Venta' && in_array($unit,['precio_total','valor_m2'],true))
            || ($operation==='Arriendo' && in_array($unit,['canon_mensual','valor_m2'],true));
        if (!$valid) { $out['unit_price_status']='Pendiente: confirma operación y unidad del precio.'; return $out; }
        $out['unit_price_currency']=$operation==='Arriendo'?'COP/m²/mes':'COP/m²';
        $divisor=$unit==='valor_m2'?1:$area;
        $offer=ComparableNegotiation::amount($row['price_amount'] ?? '');
        $negotiated=ComparableNegotiation::value($row);
        if ($offer!==null) $out['offer_per_m2']=number_format($offer/$divisor,2,'.','');
        if ($negotiated!==null) $out['negotiated_per_m2']=number_format((float)$negotiated/$divisor,2,'.','');
        $out['unit_price_status']='Preliminar M3; no es valor depurado ni adoptado. '.($ph
            ? 'M4: depurar garajes, depósitos, áreas libres y otras unidades incluidos; lo desconocido queda pendiente.'
            : 'M4: justificar base integral y composición terreno/construcción/anexos.');
        return $out;
    }
}
