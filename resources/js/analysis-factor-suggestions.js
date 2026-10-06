const norm=value=>String(value??'').normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().trim();
export function factorSuggestion(factor,total,threshold=50) {
    if (!factor.compatible) return 'No corresponde a Oficina: revisar dato original';
    if (total && factor.count/total*100<threshold) return 'Pocos datos: descartado';
    if (factor.distinct<2) return 'Sin variación: descartado';
    return 'Sugerido por cobertura y variación';
}
export function factorProfile(factor,table,rows,type='') {
    const values=table.rows.map(r=>r.values[factor.key]).filter(v=>v!==undefined && !/^(?:\s*|no publicado|pendiente|por confirmar)$/i.test(String(v).trim()));
    const excluded=factor.key==='bedrooms' || /^(habitaciones?|alcobas?|dormitorios?|bedrooms?|estrato)(?:\s|$)/.test(norm(factor.label));
    return {...factor,compatible:!((norm(type)==='oficina' || rows.length && rows.every(r=>norm(r.property_type)==='oficina')) && excluded),
        distinct:new Set(values.map(v=>/^\d+(?:[.,]\d+)?$/.test(String(v)) ? String(Number(String(v).replace(',','.'))) : norm(v))).size};
}
