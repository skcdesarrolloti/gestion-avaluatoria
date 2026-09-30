// Operational completeness only: this is not a normative approval.
export const coreFields = {
    source_name: 'fuente', source_url: 'enlace', consulted_at: 'fecha de consulta',
    operation: 'operación', price_amount: 'precio/canon', price_unit: 'unidad del precio',
    area_m2: 'área', neighborhood: 'sector', contact_phone: 'contacto',
    comparability_notes: 'observación técnica',
};
export const meaningfulFields = ['source_name', 'source_url', 'price_amount', 'area_m2',
    'neighborhood', 'project_name', 'comparability_notes', 'analysis_factor', 'latitude', 'longitude'];
export const hasComparableData = row => meaningfulFields.some(key => String(row[key] ?? '').trim());
export const missingComparableFields = row => Object.entries(coreFields)
    .filter(([key]) => !String(row[key] ?? '').trim()).map(([, label]) => label);
export function comparableUrlKey(value) {
    try {
        const url = new URL(value);
        if (!['http:', 'https:'].includes(url.protocol)) return '';
        url.hash = '';
        for (const key of [...url.searchParams.keys()]) {
            if (/^utm_|^(fbclid|gclid)$/i.test(key)) url.searchParams.delete(key);
        }
        url.searchParams.sort();
        return url.hostname.replace(/^www\./, '') + url.pathname.replace(/\/$/, '') + url.search;
    } catch { return ''; }
}
