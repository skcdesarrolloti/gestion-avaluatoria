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

function copiedCiencuadrasCards(html) {
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
    return cards;
}

export function copiedCiencuadrasRows(html, city, operation = 'venta') {
    return parseCiencuadrasCards(copiedCiencuadrasCards(html), city, operation);
}

export function ciencuadrasEmptyMessage(html, city) {
    const expected=normalize(city).replace(' de indias','');
    const places=[...new Set((html ? copiedCiencuadrasCards(html) : [])
        .filter(card=>ciencuadrasUrl(card.url) && /^oficina en (?:venta|arriendo)/i.test(card.heading))
        .map(card=>card.heading.replace(/^oficina en (?:arriendo o venta|venta|arriendo)\s*/i,'').split(',')[0].trim()).filter(Boolean))];
    if (expected && places.length && places.every(place=>normalize(place).replace(' de indias','')!==expected)) {
        return `Los anuncios copiados corresponden a otras ciudades (${places.slice(0,5).join(', ')}). Este expediente es de ${city}. No hay avisos preparados para subir. En Ciencuadras, escribe el barrio en «Ciudad, barrio, sector o sitio» y pulsa Enter. Espera que los anuncios correspondan a ${city}, copia los resultados con Ctrl+A → Ctrl+C y vuelve a pegarlos aquí con Ctrl+V.`;
    }
    return 'No hay avisos preparados para subir: no se reconocieron tarjetas completas de la ciudad y operación de esta investigación. Comprueba Arriendo o Venta, Oficina y la ubicación en Ciencuadras. Después de escribir el barrio, pulsa Enter y espera los resultados antes de copiarlos. Pega con Ctrl+V, sin Mayús; cuando se reconozcan los avisos aparecerá «Subir sin repetidos de este portal».';
}

export function ciencuadrasPaste() {
    return portalResultsPaste({ label: 'Ciencuadras', readRows: (html,city,text,operation) => copiedCiencuadrasRows(html,city,operation), validUrl: ciencuadrasUrl, allowTsv: true, emptyMessage: ciencuadrasEmptyMessage });
}
