import {amount, negotiation} from './comparable-negotiation.js';
export const unitFields=['unit_area_m2','unit_area_label','offer_per_m2','negotiated_per_m2','unit_price_currency','unit_price_status'];
export const unitNumeric=['unit_area_m2','offer_per_m2','negotiated_per_m2'];
export function unitPrice(row) {
    const out=Object.fromEntries(unitFields.map(key=>[key,'']));
    const ph=row.ph_regime==='si' && row.ph_special!=='condominio';
    out.unit_area_label=ph?'Área privada construida; no suma garaje, depósito ni área libre':'Área publicada; confrontar terreno/construcción en M4';
    out.unit_price_status='Pendiente: verifica régimen, área, base y soporte antes de calcular.';
    if (!['si','no'].includes(row.ph_regime) || !String(row.areas_source ?? '').trim() || (!ph && !String(row.area_basis ?? '').trim())) return out;
    const area=ph?Number(String(row.private_built_m2 ?? '').replace(',','.')):amount(row.area_m2);
    if (area===null || !Number.isFinite(area) || area<=0) return out;
    out.unit_area_m2=area.toFixed(4);
    const valid=row.operation==='Venta' && ['precio_total','valor_m2'].includes(row.price_unit)
        || row.operation==='Arriendo' && ['canon_mensual','valor_m2'].includes(row.price_unit);
    if (!valid) {out.unit_price_status='Pendiente: confirma operación y unidad del precio.';return out;}
    out.unit_price_currency=row.operation==='Arriendo'?'COP/m²/mes':'COP/m²';
    const divisor=row.price_unit==='valor_m2'?1:area, offer=amount(row.price_amount), negotiated=negotiation(row).value;
    if (offer!==null) out.offer_per_m2=(offer/divisor).toFixed(2);
    if (negotiated!=='') out.negotiated_per_m2=(Number(negotiated)/divisor).toFixed(2);
    out.unit_price_status='Preliminar M3; no es valor depurado ni adoptado. '+(ph
        ? 'M4: depurar garajes, depósitos, áreas libres y otras unidades incluidos; lo desconocido queda pendiente.'
        : 'M4: justificar base integral y composición terreno/construcción/anexos.');
    return out;
}
