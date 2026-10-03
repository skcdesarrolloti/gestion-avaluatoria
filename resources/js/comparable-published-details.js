// Only extract explicitly labelled facts from one announcement. Never infer PH or legal rights.
export function publishedDetails(text) {
    const raw = String(text || '').replace(/\s+/g,' ').trim();
    const number = label => raw.match(new RegExp(`(?:${label})\\s*[:：-]?\\s*(\\d+(?:[.,]\\d+)?)`, 'i'))?.[1] || '';
    const count = label => number(label) || raw.match(new RegExp(`\\b(\\d{1,3})\\s+(?:${label})\\b`, 'i'))?.[1] || '';
    const parking = count('parqueaderos?|garajes?|celdas? de parqueo');
    const deposits = count('dep[oó]sitos?|cuartos? [uú]tiles?');
    const result = {intake_state:'review', evidence_detail:raw.slice(0,1520) + (raw.length > 1520 ? ' [Texto abreviado; consultar anuncio original.]' : '')};
    for (const [key, label] of Object.entries({private_built_m2:'[aá]rea (?:privada construida|construida privada)',private_free_m2:'[aá]rea privada libre',
        built_m2:'[aá]rea construida',land_m2:'[aá]rea (?:del terreno|terreno)',stratum:'estrato',floor_level:'(?:piso|nivel)',
        age_years:'(?:antig[uü]edad|edad)',bathrooms:'ba[nñ]os?',bedrooms:'(?:habitaciones?|alcobas?)'})) {
        const value = number(label); if (value) result[key]=value;
    }
    if (parking) {result.parking_spaces=parking; result.ph_parking_presence=Number(parking) > 0 ? 'si' : 'no';}
    if (deposits) {result.ph_deposit_count=deposits; result.ph_deposit_presence=Number(deposits) > 0 ? 'si' : 'no';}
    const admin = raw.match(/administraci[oó]n\s*[:：-]?\s*\$\s*([\d.,]+)/i)?.[1];
    if (admin) result.admin_fee=admin.replace(/[.,]+$/, '');
    const phone = raw.match(/(?:tel[eé]fono|celular|whatsapp|contacto)\s*[:：-]?\s*(\+?[\d ()-]{7,20})/i)?.[1];
    if (phone) result.contact_phone=phone.trim();
    const code = raw.match(/(?:c[oó]digo|referencia|ref\.)\s*[:：#-]?\s*([\w-]{3,80})/i)?.[1];
    if (code) result.listing_code=code;
    if (result.private_built_m2 || result.private_free_m2 || result.built_m2 || result.land_m2) result.areas_source='Área rotulada en el anuncio; pendiente de corroboración.';
    if (parking || deposits) result.ph_components_source='Cantidad publicada en el anuncio; inclusión en precio y naturaleza jurídica pendientes.';
    return result;
}
