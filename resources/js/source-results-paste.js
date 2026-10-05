import { portalResultsPaste } from './portal-results-paste.js';
import { parseComparableBlock, parseComparableText } from './comparable-bulk-import.js';

const sameHost = (value, source) => {
    try { const url=new URL(value), host=url.hostname.replace(/^www\./,''), base=new URL(source).hostname.replace(/^www\./,''); return /^https?:$/.test(url.protocol) && (host===base || host.endsWith('.'+base)); }
    catch { return false; }
};
const listing = value => /(?:inmueble|propiedad|property|apartamento|oficina|casa|local|bodega|lote|venta|arriendo|alquiler)[^?#]*[\d-]/i.test(new URL(value).pathname);

export function readSourceRows(html, text, source, label, agency = false) {
    const found = new Map();
    const accept = (row, url) => {
        if (!sameHost(url,source) || !row.price_amount || !row.area_m2) return;
        found.set(url,{...row, source_url:url, source_name:label, source_type:agency ? 'inmobiliaria' : 'portal'});
    };
    if (html) {
        const template=document.createElement('template'); template.innerHTML=html;
        template.content.querySelectorAll('script,style,iframe,object').forEach(el => el.remove());
        const href = anchor => {
            try { const url=new URL(anchor.getAttribute('href'),source); url.hash=''; return url.href; }
            catch { return ''; }
        };
        const links = el => [...new Set([...el.querySelectorAll('a[href]')].map(href).filter(url => sameHost(url,source) && listing(url)))];
        for (const anchor of template.content.querySelectorAll('a[href]')) {
            const url=href(anchor);
            if (!sameHost(url,source) || !listing(url) || found.has(url)) continue;
            let node=anchor.parentElement;
            let candidate;
            for (let depth=0; node && depth<9; depth++,node=node.parentElement) {
                if (links(node).some(other => other!==url)) break;
                const copy=node.cloneNode(true);
                copy.querySelectorAll('p,div,li,br,h1,h2,h3,h4,dt,dd').forEach(el => { el.insertBefore(document.createTextNode('\n'),el.firstChild); el.appendChild(document.createTextNode('\n')); });
                const content=copy.textContent.trim();
                const row=parseComparableBlock(content);
                if (row.price_amount && row.area_m2) {
                    const heading=(node.querySelector('h2,h3')?.textContent || '').trim();
                    row.listing_title=heading.slice(0,180);
                    const type=heading.match(/^(oficina|apartamento|casa|local|bodega|lote|consultorio)\b/i)?.[1];
                    if (type) row.property_type=type[0].toUpperCase()+type.slice(1).toLowerCase();
                    // A description about a building is not its name.
                    row.project_name=content.match(/(?:edificio|conjunto|proyecto|torre)\s*:\s*([^\n.;]{2,120})/i)?.[1]?.trim() || '';
                    candidate=row;
                }
                if (candidate && (['ARTICLE','LI'].includes(node.tagName) || node.classList.contains('listingCard'))) break;
            }
            if (candidate) accept(candidate,url);
        }
    }
    // Plain text must contain separate blocks with their own explicit source URL.
    if (!found.size) for (const block of String(text || '').split(/\n\s*\n/)) {
        const urls=[...new Set(block.match(/https?:\/\/[^\s)]+/g) || [])];
        if (urls.length===1 && sameHost(urls[0],source)) accept(parseComparableBlock(block),urls[0]);
    }
    if (!found.size && String(text || '').includes('\t')) for (const row of parseComparableText(text)) accept(row,row.source_url);
    return [...found.values()];
}
export function sourceResultsPaste(source, label, agency = false) {
    return portalResultsPaste({label, readText:true, validUrl:url => sameHost(url,source),
        readRows:(html,city,text) => readSourceRows(html,text,source,label,agency),
        emptyMessage:'No se pudieron separar avisos con enlace, precio y área. Abre una ficha, copia su enlace y debajo pega su texto; separa las fichas con una línea vacía. Los datos no se agregan hasta revisar.'});
}
