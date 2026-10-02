import { amount } from './comparable-negotiation.js';
const monetary = ['price_amount','admin_fee','area_m2','latitude','longitude'];
export function excelPlan(entries, rows) {
    const known = new Map(entries.filter(entry=>entry.used).map(entry=>[entry.data.id,entry]));
    const changes = [], seen = new Set();
    for (const row of rows) {
        const entry = known.get(row.id);
        if (!entry || seen.has(row.id)) throw new Error('Muestra inexistente o repetida. No se aplicó nada.');
        seen.add(row.id);
        for (const [key, raw] of Object.entries(row)) {
            if (['id','component_key','negotiated_amount','negotiation_percent'].includes(key)) continue;
            const input = entry.controls.find(input=>input.name.endsWith(`[${key}]`));
            if (!input || input.readOnly) continue;
            let value = String(raw ?? '');
            if (input.tagName === 'SELECT' && ![...input.options].some(option=>option.value === value))
                throw new Error(`Opción inválida en ${key}. Conserva las opciones del Excel exportado.`);
            const numeric = input.type === 'number' || monetary.includes(key);
            if (numeric && value !== '' && (key === 'latitude' || key === 'longitude' ? !Number.isFinite(Number(value)) : amount(value) === null))
                throw new Error(`Número inválido en ${key}. No se aplicó nada.`);
            if (input.type === 'number' && value !== '') value = String(amount(value));
            if (input.type === 'date' && value !== '' && (!/^\d{4}-\d{2}-\d{2}$/.test(value) || !Number.isFinite(Date.parse(value)) || new Date(value).toISOString().slice(0,10) !== value))
                throw new Error(`Fecha inválida en ${key}. Usa AAAA-MM-DD.`);
            const same = numeric && value !== '' && input.value !== ''
                ? (key === 'latitude' || key === 'longitude' ? Number(value) === Number(input.value.replace(',','.')) : amount(value) === amount(input.value))
                : value === input.value;
            if (same) continue;
            changes.push({input, before:input.value, value, label:input.closest('td')?.querySelector('.comparable-field-label')?.textContent || key,
                sample:entry.data.project_name || entry.data.source_name || row.id});
        }
    }
    return changes;
}
export function verifyExcelPlan(changes) {
    if (changes.some(change=>change.input.value !== change.before)) throw new Error('La tabla cambió durante la revisión. Vuelve a revisar el Excel.');
}
export function applyExcelPlan(changes) {
    verifyExcelPlan(changes);
    for (const change of changes) change.input.value = change.value;
}
