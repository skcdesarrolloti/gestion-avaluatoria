import { sourceFacts } from './comparable-source-facts.js';
export function previewFacts(row) {
    const labels={price_amount:'Precio publicado · COP',area_m2:'Área publicada · m²',area_basis:'Base del área',operation:'Operación',property_type:'Tipo',
        neighborhood:'Barrio',address_hint:'Dirección publicada',listing_code:'Código',contact_phone:'Contacto',bedrooms:'Habitaciones',bathrooms:'Baños',
        parking_spaces:'Parqueaderos',ph_deposit_count:'Depósitos',floor_level:'Piso',age_years:'Edad',stratum:'Estrato',view_quality:'Vista',
        finish_quality:'Acabados',elevator:'Ascensor',research_generator:'Planta eléctrica',power_plant:'Planta eléctrica',amenities:'Amenidades',security_features:'Seguridad',market_services:'Servicios',
        research_access:'Acceso',research_levels:'Niveles',research_height:'Altura libre',research_service:'Alcoba de servicio',admin_fee:'Administración',private_built_m2:'Área privada construida · m²',private_free_m2:'Área privada libre · m²',built_m2:'Área construida · m²',land_m2:'Terreno · m²'};
    return [...Object.entries(labels).filter(([key]) => String(row[key] ?? '').trim()!=='' && !(key==='stratum' && /^(oficina|consultorio)$/i.test(row.property_type || ''))).map(([key,label]) => ({label,value:String(row[key])})),
        ...Object.entries(sourceFacts(row.published_attributes)).map(([label,value]) => ({label:'Publicado: '+label,value:String(value)}))];
}
