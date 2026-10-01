import { portalResultsPaste } from './portal-results-paste.js';
import { properatiClipboardCards } from './properati-clipboard.js';

const normalize = value => String(value ?? '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().replace(/\s+/g, ' ').trim();
export function properatiUrl(value) {
    try {
        const url = new URL(value, 'https://www.properati.com.co');
        if (url.protocol !== 'https:' || !['www.properati.com.co', 'properati.com.co'].includes(url.hostname)
            || url.username || url.password || url.port || !/^\/detalle\/[a-f0-9]+(?:-[a-f0-9]+)+\/?$/i.test(url.pathname)) return '';
        return `https://www.properati.com.co${url.pathname.replace(/\/$/, '')}`;
    } catch { return ''; }
}

export function parseProperatiCards(cards, city) {
    const expected = normalize(city).replace(' de indias', '');
    const seen = new Set();
    return cards.flatMap(card => {
        const url = properatiUrl(card.url);
        const places = String(card.location || '').split(',').map(part => part.trim());
        const cityIndex = places.findIndex(part => normalize(part).replace(' de indias', '') === expected);
        const price = String(card.price || '').trim().match(/^\$\s*([\d.]+(?:,\d{1,2})?)$/)?.[1];
        const area = String(card.area || '').trim().match(/^(\d+(?:[.,]\d+)?)\s*m[2²]$/)?.[1];
        if (!url || seen.has(url) || !/^oficina en venta en\s/.test(normalize(card.heading))
            || !expected || cityIndex < 0 || !price || !area) return [];
        seen.add(url);
        return [{ source_type: 'portal', source_name: 'Properati', source_url: url,
            operation: 'Venta', property_type: 'Oficina', price_amount: price, price_unit: 'precio_total',
            area_m2: area.replace('.', ','), neighborhood: cityIndex > 0 ? places[0] : '', ph_regime: 'por_verificar',
            contact_name: String(card.agency || '').trim(),
            comparability_notes: `Fuente: Properati · Red Proppit. Ubicación publicada: ${places.join(', ')}. Resumen copiado; área y PH por verificar. Puede estar publicado en otros portales de la red.` }];
    });
}

export function copiedProperatiRows(html, city) {
    const template = document.createElement('template');
    template.innerHTML = html; // Detached, inert; external markup is never displayed or executed.
    const cards = [...template.content.querySelectorAll('article')].map(article => {
        const text = key => article.querySelector(`[data-test="${key}"]`)?.textContent || '';
        return { url: article.querySelector('a[data-test="snippet__title"]')?.getAttribute('href'),
            heading: text('snippet__title'), location: text('snippet__location'), price: text('snippet__price'),
            area: text('area-value'), agency: text('agency-name') };
    });
    return parseProperatiCards([...cards, ...properatiClipboardCards(template.content, properatiUrl)], city);
}

export function properatiPaste() {
    return portalResultsPaste({ label: 'Properati', readRows: copiedProperatiRows, validUrl: properatiUrl });
}
