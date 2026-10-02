export const mapFields = ['latitude', 'longitude', 'location_precision', 'map_notes', 'location_source', 'evidence_detail', 'verification_detail'];
const phFields = ['private_built_m2', 'private_free_m2', 'ph_units_detail'];
const nphFields = ['land_m2', 'built_m2', 'annexes_detail', 'crops_detail'];
export function compositionVisible(key, row) {
    if (phFields.includes(key) || (key.startsWith('ph_') && key !== 'ph_special' && key !== 'ph_regime')) return row.ph_regime === 'si' && row.ph_special !== 'condominio';
    if (nphFields.includes(key)) return row.ph_regime === 'no' || (row.ph_regime === 'si' && row.ph_special === 'condominio');
    return key !== 'ph_special' || row.ph_regime === 'si';
}
export function locationPoints(rows, subject = null) {
    if (subject) rows = [...rows, {...subject, subject: true, source_name: 'Bien sujeto'}];
    const points = rows.flatMap((row, index) => {
        const latText = String(row.latitude ?? '').trim().replace(',', '.');
        const lngText = String(row.longitude ?? '').trim().replace(',', '.');
        const lat = Number(latText), lng = Number(lngText);
        if (!latText || !lngText || !Number.isFinite(lat) || !Number.isFinite(lng) || Math.abs(lat) > 90 || Math.abs(lng) > 180 || row.active === 'no' || row.status === 'descartada') return [];
        return [{n:row.subject ? 'S' : index + 1, lat, lng, label:row.project_name || row.source_name || 'Muestra', precision:row.location_precision || 'Sin precisión', url:'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(lat + ',' + lng)}];
    });
    const lats = points.map(p => p.lat), lngs = points.map(p => p.lng);
    const minLat = Math.min(...lats), maxLat = Math.max(...lats), minLng = Math.min(...lngs), maxLng = Math.max(...lngs);
    return points.map(p => ({...p, x:maxLng === minLng ? 300 : 40 + (p.lng - minLng) / (maxLng - minLng) * 520,
        y:maxLat === minLat ? 180 : 320 - (p.lat - minLat) / (maxLat - minLat) * 280}));
}
