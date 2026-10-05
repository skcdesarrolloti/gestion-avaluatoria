import { labelledFacts } from './comparable-source-facts.js';
import { explicitCount, descriptionAttributes } from './comparable-text-attributes.js';
// Only extract explicitly labelled facts from one announcement. Never infer PH or legal rights.
export function publishedDetails(text) {
    const raw = String(text || '').replace(/\s+/g,' ').trim();
    const number = label => raw.match(new RegExp(`(?:${label})\\s*[:：-]?\\s*(\\d+(?:[.,]\\d+)?)`, 'i'))?.[1] || '';
    const count = label => explicitCount(raw,label);
    const parking = count('parqueaderos?|garajes?|celdas? de parqueo');
    const deposits = count('dep[oó]sitos?|cuartos? [uú]tiles?');
    const result = {intake_state:'review', evidence_detail:raw.slice(0,1520) + (raw.length > 1520 ? ' [Texto abreviado; consultar anuncio original.]' : '')};
    result.published_text=String(text || '').trim().slice(0,15920) + (String(text || '').length > 15920 ? '\n[Texto abreviado; consultar ficha original.]' : '');
    const facts={...descriptionAttributes(text),...labelledFacts(text)};
    result.published_attributes=JSON.stringify(facts);
    for (const [key, label] of Object.entries({private_built_m2:'[aá]rea (?:privada construida|construida privada)',private_free_m2:'[aá]rea privada libre',
        built_m2:'[aá]rea construida',land_m2:'[aá]rea (?:del terreno|terreno)',stratum:'estrato',floor_level:'(?:piso|nivel)',
        age_years:'(?:antig[uü]edad|edad)',bathrooms:'ba[nñ]os?',bedrooms:'(?:habitaciones?|alcobas?)'})) {
        const value = ['bathrooms','bedrooms'].includes(key) ? count(label) : number(label); if (value) result[key]=value;
    }
    if (parking) {result.parking_spaces=parking; result.ph_parking_presence=Number(parking) > 0 ? 'si' : 'no';}
    if (deposits) {result.ph_deposit_count=deposits; result.ph_deposit_presence=Number(deposits) > 0 ? 'si' : 'no';}
    const admin = raw.match(/administraci[oó]n\s*[:：-]?\s*\$\s*([\d.,]+)/i)?.[1];
    if (admin) result.admin_fee=admin.replace(/[.,]+$/, '');
    const phone = raw.match(/(?:tel[eé]fono|celular|whatsapp|contacto)\s*[:：-]?\s*(\+?[\d ()-]{7,20})/i)?.[1];
    if (phone) result.contact_phone=phone.trim();
    const code = raw.match(/(?:c[oó]digo|referencia|ref\.)\s*[:：#-]?\s*([\w-]{3,80})/i)?.[1];
    if (code) result.listing_code=code;
    for (const [label,value] of Object.entries(facts)) {
        const key=label.normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase();
        const field={vista:'view_quality',acabados:'finish_quality',ascensor:'elevator',amenidades:'amenities',seguridad:'security_features',
            'planta electrica':'research_generator','servicios publicos':'market_services','tipo de acceso':'research_access',
            'alcoba de servicio':'research_service','niveles':'research_levels','altura libre':'research_height'}[key];
        if (!field) continue;
        if (['research_levels','research_height'].includes(field) && !/^\d+(?:[.,]\d+)?(?:\s*m)?$/.test(value)) continue;
        const limit={elevator:20,view_quality:80,finish_quality:80,amenities:240,security_features:180}[field] || 500;
        if (field==='elevator' && value==='Mencionado en la descripción · verificar alcance') result[field]='Sí';
        else if (field==='elevator' && value==='No · descrito en el anuncio') result[field]='No';
        else result[field]=['research_levels','research_height'].includes(field) ? value.replace(/\s*m$/,'') : value.slice(0,limit);
    }
    if (result.private_built_m2 || result.private_free_m2 || result.built_m2 || result.land_m2) result.areas_source='Área rotulada en el anuncio; pendiente de corroboración.';
    if (parking || deposits) result.ph_components_source='Cantidad publicada en el anuncio; inclusión en precio y naturaleza jurídica pendientes.';
    return result;
}
