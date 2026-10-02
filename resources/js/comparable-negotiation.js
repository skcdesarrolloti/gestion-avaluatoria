export function amount(value) {
    let text = String(value ?? '').trim().replace(/[^\d,.-]/g, '');
    if (!text) return null;
    if (text.lastIndexOf(',') > text.lastIndexOf('.')) text = text.replaceAll('.', '').replace(',', '.');
    else text = /^\d{1,3}(\.\d{3})+$/.test(text) ? text.replaceAll('.', '') : text.replaceAll(',', '');
    const number = Number(text);
    return Number.isFinite(number) && number >= 0 ? number : null;
}
export function negotiation(row) {
    if (!String(row.negotiation_discount ?? '').trim()) return {value: '', error: ''};
    const offer = amount(row.price_amount), discount = amount(row.negotiation_discount);
    if (offer === null || discount === null || discount > offer) return {value: '', error: 'Descuento fuera del rango de la oferta'};
    return {value: (offer - discount).toFixed(2), error: ''};
}
export function capturePending(row, phSubject) {
    const pending = [];
    const require = (key, label) => { if (!String(row[key] ?? '').trim()) pending.push([key, label]); };
    for (const [key,label] of Object.entries({source_name:'fuente',source_url:'URL',consulted_at:'fecha de captura',
        market_data_kind:'tipo de dato',operation:'operación',price_amount:'oferta',price_unit:'unidad del precio',
        area_m2:'área publicada',market_city:'municipio y fuente',neighborhood:'sector',contact_phone:'contacto',comparability_notes:'descripción/comparabilidad',
        market_services:'servicios',market_access:'accesos y transporte',market_planning:'norma urbanística'})) require(key,label);
    for (const [key, label] of Object.entries({negotiation_discount:'descuento', negotiation_kind:'tipo de descuento',
        negotiation_source:'soporte de negociación', evidence_detail:'evidencia', verification_detail:'corroboración',
        area_basis:'base del área', areas_source:'soporte de áreas', location_source:'fuente de ubicación'})) require(key, label);
    if (row.ph_regime === 'por_verificar' || !row.ph_regime) pending.push(['ph_regime', 'régimen jurídico']);
    const isPh = row.ph_regime === 'si' && row.ph_special !== 'condominio';
    if (isPh || (phSubject && row.ph_regime === 'por_verificar')) {
        require('private_built_m2', 'área privada construida'); require('private_free_m2', 'área privada libre / ausencia confirmada');
        require('ph_components_source', 'soporte de componentes');
        for (const type of ['parking', 'deposit']) {
            const name = type === 'parking' ? 'parqueaderos' : 'depósitos';
            require(`ph_${type}_presence`, `presencia de ${name}`);
            if (row[`ph_${type}_presence`] === 'si') {
                require(type === 'parking' ? 'parking_spaces' : 'ph_deposit_count', `cantidad de ${name}`);
                for (const [suffix, label] of [['in_price','inclusión'], ['nature','naturaleza'], ['area_m2','área']])
                    require(`ph_${type}_${suffix}`, `${label} de ${name}`);
            }
        }
    }
    if (negotiation(row).error) pending.push(['negotiation_discount', 'descuento inválido']);
    return pending;
}
export function portalCounts(rows) {
    const names = ['FincaRaíz', 'Metrocuadrado', 'Ciencuadras', 'Properati', 'Mercado Libre', 'Otras fuentes'];
    const counts = names.map(label => ({label, count: 0}));
    for (const row of rows) {
        const source = `${row.source_name ?? ''} ${row.source_url ?? ''}`.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
        const index = [/fincaraiz/, /metrocuadrado/, /ciencuadras/, /properati/, /mercado.?libre/].findIndex(pattern => pattern.test(source));
        counts[index < 0 ? 5 : index].count++;
    }
    return counts;
}
