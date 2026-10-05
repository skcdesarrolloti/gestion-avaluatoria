import { amount } from './comparable-negotiation.js';
import { sourceFacts } from './comparable-source-facts.js';
// Same URL: fill only blanks; retain conflicting values as a source observation.
export function sourceUpdate(existing, incoming) {
    const changes = {};
    const protectedFields = new Set(['id','property_group','intake_state','active','status','component_key','location_verification','latitude','longitude','published_attributes']);
    for (const [key,value] of Object.entries(incoming)) {
        if (!protectedFields.has(key) && String(value ?? '').trim() && !String(existing[key] ?? '').trim()) changes[key]=value;
    }
    const different = [];
    if (incoming.published_text && existing.published_text && !existing.published_text.includes(incoming.published_text)) {
        changes.published_text = `${existing.published_text}\n\nLectura adicional de la ficha:\n${incoming.published_text}`.slice(0,15900);
    }
    const originalFacts=sourceFacts(existing.published_attributes), nextFacts=sourceFacts(incoming.published_attributes);
    const merged={...originalFacts};
    for (const [label,value] of Object.entries(nextFacts)) {
        if (!merged[label] && Object.keys(merged).length < 80) merged[label]=value;
        else if (merged[label] !== value) different.push(`${label}: registrado ${merged[label]}; nueva lectura ${value}`);
    }
    if (JSON.stringify(originalFacts)!==JSON.stringify(merged)) changes.published_attributes=JSON.stringify(merged);
    const labels={price_amount:'Precio publicado',area_m2:'Área publicada',parking_spaces:'Parqueaderos',ph_deposit_count:'Depósitos'};
    if (incoming.evidence_detail && existing.evidence_detail && incoming.evidence_detail !== existing.evidence_detail
        && incoming.evidence_detail !== existing.latest_source_excerpt) changes.latest_source_excerpt=incoming.evidence_detail;
    for (const key of ['price_amount','area_m2','parking_spaces','ph_deposit_count','bathrooms','bedrooms','floor_level','age_years','elevator','view_quality','finish_quality','research_generator']) {
        const before=String(existing[key] ?? '').trim(), after=String(incoming[key] ?? '').trim();
        if (!before || !after) continue;
        const numeric = value => key === 'price_amount' ? amount(value) : Number(value.replace(',','.'));
        const numericField=['price_amount','area_m2','parking_spaces','ph_deposit_count','bathrooms','bedrooms','floor_level','age_years'].includes(key);
        const comparable = value => value.normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().trim();
        if (numericField ? numeric(before) !== numeric(after) : comparable(before) !== comparable(after)) different.push(`${labels[key] || key}: registrado ${before}; nueva lectura ${after}`);
    }
    if (different.length) {
        const note=different.join('; ');
        if (!String(existing.source_updates || '').includes(note)) changes.source_updates=
            `${existing.source_updates || ''}\n${new Intl.DateTimeFormat('es-CO',{timeZone:'America/Bogota',dateStyle:'short',timeStyle:'short'}).format(new Date())}: ${note}. Confrontar aviso original.`.trim().slice(-1600);
    }
    if (Object.keys(changes).length) changes.intake_state='review';
    return changes;
}
