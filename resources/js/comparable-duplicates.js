import { comparableUrlKey } from './comparable-review.js';

const text = value => String(value ?? '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().replace(/[^a-z0-9]/g, '');
const number = value => {
    let v = String(value ?? '').replace(/[^\d.,]/g, '');
    if (v.includes(',')) v = v.replaceAll('.', '').replace(',', '.');
    else if (/^\d{1,3}(\.\d{3})+$/.test(v)) v = v.replaceAll('.', '');
    return Number(v) || null;
};
const same = (a, b, key) => text(a[key]).length > 0 && text(a[key]) === text(b[key]);
const equalNumber = (a, b, key) => number(a[key]) !== null && number(a[key]) === number(b[key]);

export function duplicateEvidence(candidate, existing) {
    const url = comparableUrlKey(candidate.source_url);
    if (url && url === comparableUrlKey(existing.source_url)) return { exact: true, reasons: ['mismo enlace'] };
    const reasons = [];
    if (same(candidate, existing, 'address_hint') && text(candidate.address_hint).length >= 8
        && /\d/.test(candidate.address_hint)) reasons.push('misma dirección anunciada');
    if (same(candidate, existing, 'project_name')) reasons.push('mismo edificio/proyecto');
    if (equalNumber(candidate, existing, 'area_m2')) reasons.push('misma área');
    if (equalNumber(candidate, existing, 'price_amount')) reasons.push('mismo precio');
    if (same(candidate, existing, 'neighborhood')) reasons.push('mismo sector');
    const address = reasons.includes('misma dirección anunciada');
    const area = reasons.includes('misma área');
    const price = reasons.includes('mismo precio');
    const context = reasons.includes('mismo edificio/proyecto') || reasons.includes('mismo sector');
    // These are review signals, never an identity decision or an appraisal rule.
    return (address && area) || (area && price && context) ? { exact: false, reasons } : null;
}

export function checkComparableDuplicates(data, rows, confirmDistinct = message => window.confirm(message)) {
    const matches = rows.map((row, index) => ({ row, index, match: duplicateEvidence(data, row) })).filter(item => item.match);
    if (matches.some(item => item.match.exact)) return { blocked: true, exact: true };
    if (!matches.length) return { blocked: false };
    const summary = matches.map(({ row, index, match }) => `Muestra ${index + 1} (${row.source_name || 'sin fuente'}): ${match.reasons.join(', ')}.`).join('\n');
    const distinct = confirmDistinct(`Posible inmueble repetido, aunque tenga otro código o portal:\n${summary}\n\nNo basta esta coincidencia para identificarlo. Cancelar: no agregar y revisar la tabla. Aceptar: confirmo que es un inmueble distinto y deseo agregarlo.`);
    if (distinct) data.comparability_notes = `${data.comparability_notes || ''}\nCoincidencia revisada por el usuario: declaró inmueble distinto. ${summary}`.trim();
    return { blocked: !distinct, exact: false, summary };
}
