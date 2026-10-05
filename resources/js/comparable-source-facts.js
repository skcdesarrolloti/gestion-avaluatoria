const clean = value => String(value ?? '').trim();
const keyOf = value => clean(value).normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().replace(/\s+/g,' ');
export function sourceFacts(value) {
    try {
        const facts = JSON.parse(value || '{}');
        return facts && !Array.isArray(facts) && typeof facts === 'object' ? facts : {};
    } catch { return {}; }
}
export function labelledFacts(text) {
    const facts = {};
    // Preserve original labels and values; unrecognised attributes are evidence, not model codes.
    for (const line of String(text || '').split(/[\n;]+/)) {
        const match = line.trim().match(/^([^:：]{2,80})[:：]\s*(.{1,500})$/);
        if (match && !/https?$/i.test(match[1]) && Object.keys(facts).length < 80) facts[clean(match[1])] = clean(match[2]);
    }
    return facts;
}
export function comparisonRows(card, config = {}) {
    const catalog = config.catalog || {}, subject = config.subjects || {};
    const rows = [
        {label:'Precio publicado · COP', sample:'price_amount'},
        {label:'Área publicada · m²', sample:'area_m2'},
        {label:'Base del área', sample:'area_basis'},
        ...Object.entries(catalog).map(([key, factor]) => ({label:factor.label, sample:factor.sample, subject:subject[key]})),
    ];
    const bags = card.rows.map(row => sourceFacts(row.published_attributes));
    const labels = [...new Map(bags.flatMap(bag => Object.keys(bag)).map(label => [keyOf(label),label])).values()];
    rows.push(...labels.map(label => ({label:`Publicado: ${label}`, raw:label})));
    return rows.map(row => {
        const values = card.rows.map((ad, i) => clean(row.raw ? Object.entries(bags[i]).find(([label]) => keyOf(label)===keyOf(row.raw))?.[1] : ad[row.sample]));
        const present = values.filter(value => value !== '');
        const numeric=['price_amount','area_m2','bathrooms','bedrooms','floor_level','age_years','parking_spaces','ph_deposit_count'].includes(row.sample);
        const normalized = present.map(value => numeric && /^\d+(?:[.,]\d+)?$/.test(value) ? String(Number(value.replace(',','.'))) : keyOf(value));
        const different = new Set(normalized).size > 1;
        return {...row, values, state:different ? 'different' : present.length > 1 ? 'same' : 'pending',
            validation:different ? 'Diferencia · validar' : present.length > 1 ? (present.length < values.length ? 'Coinciden · faltan fuentes' : 'Coinciden') : present.length ? 'Una fuente · completar' : 'Sin dato publicado'};
    });
}
