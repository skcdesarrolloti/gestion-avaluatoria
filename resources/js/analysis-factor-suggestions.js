const norm=value=>String(value??'').normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().trim();
export function factorSuggestion(factor,total,threshold=50) {
    if (!factor.compatible) return 'No corresponde a Oficina: revisar dato original';
    if (total && factor.count/total*100<threshold) return 'Pocos datos: no sugerido';
    if (factor.distinct<2) return 'Sin variación: no sugerido';
    return 'Sugerido por cobertura y variación';
}
export function factorProfile(factor,table,rows) {
    const values=table.rows.map(r=>r.values[factor.key]).filter(v=>v!==undefined && !/^(?:\s*|no publicado|pendiente|por confirmar)$/i.test(String(v).trim()));
    const bedrooms=factor.key==='bedrooms' || /^(habitaciones?|alcobas?|dormitorios?|bedrooms?)(?:\s|$)/.test(norm(factor.label));
    return {...factor,compatible:!(rows.length && rows.every(r=>norm(r.property_type)==='oficina') && bedrooms),
        distinct:new Set(values.map(v=>/^\d+(?:[.,]\d+)?$/.test(String(v)) ? String(Number(String(v).replace(',','.'))) : norm(v))).size};
}
