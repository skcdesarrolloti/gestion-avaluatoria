import { portalResultsPaste } from './portal-results-paste.js';
import { publishedDetails } from './comparable-published-details.js';

const normalize = value => String(value ?? '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().replace(/\s+/g, ' ').trim();
export function ciencuadrasUrl(value) {
    try {
        const url = new URL(value, 'https://www.ciencuadras.com');
        if (url.protocol !== 'https:' || !['www.ciencuadras.com', 'ciencuadras.com'].includes(url.hostname)
            || url.username || url.password || url.port || !/^\/inmueble\/[a-z0-9-]+-\d+\/?$/i.test(url.pathname)) return '';
        return `https://www.ciencuadras.com${url.pathname.replace(/\/$/, '')}`;
    } catch { return ''; }
}

export function parseCiencuadrasCards(cards, city, operation = 'venta') {
    const seen = new Set();
    const expected = normalize(city).replace(' de indias', '');
    return cards.flatMap(card => {
        const url = ciencuadrasUrl(card.url);
        const heading = normalize(card.heading);
        const location = heading.replace(/^oficina en (?:arriendo o venta|venta|arriendo)\s*/, '').split(',').map(part => part.trim());
        const price = operation==='arriendo' ? card.text.match(/(?:valor de arriendo|administraci[oó]n incluida|arriendo(?: con administraci[oó]n)?)\s*:\s*\$\s*([\d.,]+)/i)?.[1] : card.text.match(/valor de compra\s*:\s*\$\s*([\d.,]+)/i)?.[1];
        const area = card.text.match(/(\d+(?:[.,]\d+)?)\s*m[2²]/i)?.[1];
        if (!url || seen.has(url) || !(operation==='arriendo'?/^oficina en arriendo(?: o venta)?/:/^oficina en (?:arriendo o )?venta/).test(heading)
            || !expected || location[0] !== expected || !price || !area) return [];
        seen.add(url);
        return [{ ...publishedDetails(card.text), source_type: 'portal', source_name: 'Ciencuadras', source_url: url,
            operation: operation==='arriendo'?'Arriendo':'Venta', property_type: 'Oficina', price_amount: price, price_unit: operation==='arriendo'?'canon_mensual':'precio_total',
            area_m2: area.replace('.', ','), neighborhood: location.at(-1), ph_regime: 'por_verificar',
            comparability_notes: `Resumen copiado de Ciencuadras; ubicación publicada: ${location.join(', ')}. Área y PH por verificar. ${operation==='arriendo' && /administraci[oó]n/i.test(card.text)?'Importe publicado con administración; verificar el desglose, no se descuenta automáticamente.':''}` }];
    });
}

export function copiedCiencuadrasRows(html, city, operation = 'venta') {
    // A detached template is inert: never attach external markup or images to the page.
    const template = document.createElement('template');
    template.innerHTML = html;
    const cards = [...template.content.querySelectorAll('a[href]')].flatMap(anchor => {
        const article = anchor.querySelector('article');
        const heading = anchor.querySelector('h3');
        if (!article || !heading) return [];
        const content=article.cloneNode(true);
        content.querySelectorAll('p,span,div,h3').forEach(el=>el.appendChild(document.createTextNode(' ')));
        return [{ url: anchor.getAttribute('href'), heading: heading.textContent, text: content.textContent }];
    });
    return parseCiencuadrasCards(cards, city, operation);
}

export function ciencuadrasPaste() {
    return portalResultsPaste({ label: 'Ciencuadras', readRows: (html,city,text,operation) => copiedCiencuadrasRows(html,city,operation), validUrl: ciencuadrasUrl, allowTsv: true });
}
