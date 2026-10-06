import { sourceFacts } from './comparable-source-facts.js';

const text = value => value === null || value === undefined ? '' : String(value).trim();
const normal = value => text(value).normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().replace(/\s+/g,' ');
const fields = [
    ['listing_code','Código',['codigo','codigo del anuncio']],
    ['price_amount','Precio · COP',['precio','precio publicado','precio de venta']],
    ['area_m2','Área publicada · m²',['area','area publicada']],
    ['bathrooms','Baños',['banos','bano']],
    ['parking_spaces','Parqueaderos',['parqueaderos','garajes','celdas de parqueo']],
    ['contact_name','Anunciante / inmobiliaria',['anunciante','inmobiliaria']],
    ['property_type','Tipo de inmueble',['tipo de inmueble']],
    ['neighborhood','Barrio',['barrio']],
    ['bedrooms','Habitaciones',['habitaciones','alcobas']],
    ['floor_level','Piso',['piso','piso n°','piso no','piso de ubicacion']],
    ['admin_fee','Administración · COP',['administracion']],
];
const aliases = new Map(fields.flatMap(([key,,names]) => names.map(label => [label,key])));
const labels = new Map(fields.map(([key,label]) => [key,label]));

// Each row keeps one advertisement, without merging other sources or the subject.
export function portalTable(advertisements) {
    const columns = new Map();
    const rows = advertisements.map(ad => {
        const values = {};
        Object.entries(sourceFacts(ad.published_attributes)).forEach(([label,value]) => {
            if (!text(value) || typeof value === 'object') return;
            const key = aliases.get(normal(label)) || 'published:'+normal(label);
            columns.set(key,{key,label:labels.get(key) || label});
            if (!Object.hasOwn(values,key)) values[key]=text(value);
        });
        fields.forEach(([key,label]) => {
            if (!text(ad[key])) return;
            columns.set(key,{key,label});
            if (!Object.hasOwn(values,key)) values[key]=text(ad[key]);
        });
        return {key:ad.id,title:ad.listing_title || ad.project_name || ad.property_type || 'Inmueble',
            source_url:ad.source_url,values};
    });
    const order = fields.map(([key]) => key);
    return {columns:[...columns.values()].sort((a,b) => {
        const rank = key => order.includes(key) ? order.indexOf(key) : order.length;
        return rank(a.key)-rank(b.key);
    }),rows};
}
