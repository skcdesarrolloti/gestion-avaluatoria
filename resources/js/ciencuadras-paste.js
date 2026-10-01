import { portalResultsPaste } from './portal-results-paste.js';

const normalize = value => String(value ?? '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().replace(/\s+/g, ' ').trim();
export function ciencuadrasUrl(value) {
    try {
        const url = new URL(value, 'https://www.ciencuadras.com');
        if (url.protocol !== 'https:' || !['www.ciencuadras.com', 'ciencuadras.com'].includes(url.hostname)
            || url.username || url.password || url.port || !/^\/inmueble\/[a-z0-9-]+-\d+\/?$/i.test(url.pathname)) return '';
        return `https://www.ciencuadras.com${url.pathname.replace(/\/$/, '')}`;
    } catch { return ''; }
}

export function parseCiencuadrasCards(cards, city) {
    const seen = new Set();
    const expected = normalize(city).replace(' de indias', '');
    return cards.flatMap(card => {
        const url = ciencuadrasUrl(card.url);
        const heading = normalize(card.heading);
        const location = heading.replace(/^oficina en (?:arriendo o )?venta\s*/, '').split(',').map(part => part.trim());
        const price = card.text.match(/valor de compra\s*:\s*\$\s*([\d.,]+)/i)?.[1];
        const area = card.text.match(/(\d+(?:[.,]\d+)?)\s*m[2²]/i)?.[1];
        if (!url || seen.has(url) || !/^oficina en (?:arriendo o )?venta/.test(heading)
            || !expected || location[0] !== expected || !price || !area) return [];
        seen.add(url);
        return [{ source_type: 'portal', source_name: 'Ciencuadras', source_url: url,
            operation: 'Venta', property_type: 'Oficina', price_amount: price, price_unit: 'precio_total',
            area_m2: area.replace('.', ','), neighborhood: location.at(-1), ph_regime: 'por_verificar',
            comparability_notes: `Resumen copiado de Ciencuadras; ubicación publicada: ${location.join(', ')}. Área y PH por verificar.` }];
    });
}

export function copiedCiencuadrasRows(html, city) {
    // A detached template is inert: never attach external markup or images to the page.
    const template = document.createElement('template');
    template.innerHTML = html;
    const cards = [...template.content.querySelectorAll('a[href]')].flatMap(anchor => {
        const article = anchor.querySelector('article');
        const heading = anchor.querySelector('h3');
        if (!article || !heading) return [];
        return [{ url: anchor.getAttribute('href'), heading: heading.textContent, text: article.textContent }];
    });
    return parseCiencuadrasCards(cards, city);
}

export function ciencuadrasPaste() {
    return portalResultsPaste({ label: 'Ciencuadras', readRows: copiedCiencuadrasRows, validUrl: ciencuadrasUrl, allowTsv: true });
}
