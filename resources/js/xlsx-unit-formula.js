export function unitFormula(columns, rowNumber, key, name) {
    if (!['unit_area_m2','offer_per_m2','negotiated_per_m2','unit_price_currency'].includes(key)) return null;
    const ref=k=>{const index=columns.findIndex(column=>column.key===k);return index<0?null:`${name(index)}${rowNumber}`;};
    const option=(k,value)=>JSON.stringify(columns.find(column=>column.key===k)?.options?.[value] ?? value);
    const regime=ref('ph_regime'), special=ref('ph_special'), source=ref('areas_source');
    const built=ref('private_built_m2'), published=ref('area_m2'), basis=ref('area_basis');
    if (key==='unit_area_m2') {
        if (!regime || !source) return '""';
        const ph=`AND(${regime}=${option('ph_regime','si')}${special?`,${special}<>${option('ph_special','condominio')}`:''})`;
        const phArea=built?`IF(AND(ISNUMBER(${built}),${built}>0),${built},"")`:'""';
        const otherArea=published && basis?`IF(AND(ISNUMBER(${published}),${published}>0,${basis}<>""),${published},"")`:'""';
        return `IF(${source}="","",IF(${ph},${phArea},IF(OR(${regime}=${option('ph_regime','no')},AND(${regime}=${option('ph_regime','si')}${special?`,${special}=${option('ph_special','condominio')}`:',FALSE'})),${otherArea},"")))`;
    }
    const area=ref('unit_area_m2'), unit=ref('price_unit'), operation=ref('operation');
    if (!area || !unit || !operation) return '""';
    const valid=`AND(ISNUMBER(${area}),${area}>0,OR(AND(${operation}="Venta",OR(${unit}=${option('price_unit','precio_total')},${unit}=${option('price_unit','valor_m2')})),AND(${operation}="Arriendo",OR(${unit}=${option('price_unit','canon_mensual')},${unit}=${option('price_unit','valor_m2')}))))`;
    if (key==='unit_price_currency') return `IF(${valid},IF(${operation}="Arriendo","COP/m²/mes","COP/m²"),"")`;
    const price=ref(key==='offer_per_m2'?'price_amount':'negotiated_amount');
    if (!price) return '""';
    return `IF(AND(${valid},ISNUMBER(${price})),IF(${unit}=${option('price_unit','valor_m2')},${price},${price}/${area}),"")`;
}
