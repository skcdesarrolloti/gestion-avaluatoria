import { portalResultsPaste } from './portal-results-paste.js';

const normalize = value => String(value ?? '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().replace(/\s+/g, ' ').trim();
export function mercadolibreUrl(value) {
    try {
        const url = new URL(value);
        if (url.protocol !== 'https:' || url.hostname !== 'inmueble.mercadolibre.com.co'
            || url.username || url.password || url.port || !/^\/MCO-\d+-[a-z0-9-]+_JM$/i.test(url.pathname)) return '';
        return `https://inmueble.mercadolibre.com.co${url.pathname}`;
    } catch { return ''; }
}

export function copiedMercadolibreRows(html, city) {
    const template = document.createElement('template');
    template.innerHTML = html; // Detached and inert; never display external HTML.
    const expected = normalize(city).replace(' de indias', '');
    const seen = new Set();
    return [...template.content.querySelectorAll('.poly-card')].flatMap(card => {
        const text = selector => card.querySelector(selector)?.textContent.trim() || '';
        const url = mercadolibreUrl(card.querySelector('a.poly-component__title')?.getAttribute('href'));
        const id = url.match(/MCO-(\d+)-/)?.[1];
        const location = text('.poly-component__location').split(',').map(part => part.trim());
        const cityIndex = location.findIndex(part => normalize(part).replace(' de indias', '') === expected);
        const price = text('.poly-price__current');
        const amount = price.match(/^\$\s*([\d.]+(?:,\d{1,2})?)$/)?.[1];
        const areas = [...card.querySelectorAll('.poly-attributes_list__item')].map(el =>
            el.textContent.trim().match(/^(\d+(?:[.,]\d+)?)\s*m[2²]\s+(cubiertos|construidos|totales)$/i)).filter(Boolean);
        if (!url || seen.has(id) || normalize(text('.poly-component__headline')) !== 'oficina en venta'
            || !expected || cityIndex < 0 || !amount || areas.length !== 1) return [];
        seen.add(id);
        return [{ source_type: 'portal', source_name: 'Mercado Libre Inmuebles', source_url: url,
            operation: 'Venta', property_type: 'Oficina', price_amount: amount, price_unit: 'precio_total',
            area_m2: areas[0][1].replace('.', ','), neighborhood: cityIndex > 0 ? location[cityIndex - 1] : '',
            ph_regime: 'por_verificar', comparability_notes: `Mercado Libre, publicación MCO-${id}. Ubicación publicada: ${location.join(', ')}. Área publicada: ${areas[0][0]}; verificar clase de área y PH. Resumen copiado del portal.` }];
    });
}

export function mercadolibrePaste() {
    return portalResultsPaste({ label: 'Mercado Libre', readRows: copiedMercadolibreRows, validUrl: mercadolibreUrl });
}
