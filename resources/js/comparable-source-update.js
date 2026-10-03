import { amount } from './comparable-negotiation.js';
// Same URL: fill only blanks; retain conflicting values as a source observation.
export function sourceUpdate(existing, incoming) {
    const changes = {};
    const protectedFields = new Set(['id','property_group','intake_state','active','status','component_key','location_verification','latitude','longitude']);
    for (const [key,value] of Object.entries(incoming)) {
        if (!protectedFields.has(key) && String(value ?? '').trim() && !String(existing[key] ?? '').trim()) changes[key]=value;
    }
    const different = [];
    const labels={price_amount:'Precio publicado',area_m2:'Área publicada',parking_spaces:'Parqueaderos',ph_deposit_count:'Depósitos'};
    if (incoming.evidence_detail && existing.evidence_detail && incoming.evidence_detail !== existing.evidence_detail
        && incoming.evidence_detail !== existing.latest_source_excerpt) changes.latest_source_excerpt=incoming.evidence_detail;
    for (const key of ['price_amount','area_m2','parking_spaces','ph_deposit_count']) {
        const before=String(existing[key] ?? '').trim(), after=String(incoming[key] ?? '').trim();
        if (!before || !after) continue;
        const numeric = value => key === 'price_amount' ? amount(value) : Number(value.replace(',','.'));
        if (numeric(before) !== numeric(after)) different.push(`${labels[key]}: registrado ${before}; nueva lectura ${after}`);
    }
    if (different.length) {
        const note=different.join('; ');
        if (!String(existing.source_updates || '').includes(note)) changes.source_updates=
            `${existing.source_updates || ''}\n${new Intl.DateTimeFormat('es-CO',{timeZone:'America/Bogota',dateStyle:'short',timeStyle:'short'}).format(new Date())}: ${note}. Confrontar aviso original.`.trim().slice(-1600);
    }
    if (Object.keys(changes).length) changes.intake_state='review';
    return changes;
}
