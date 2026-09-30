import { comparableUrlKey, hasComparableData } from './comparable-review.js';
import { checkComparableDuplicates } from './comparable-duplicates.js';

export function parseComparableBlock(block) {
    const text = String(block ?? '').replace(/\r/g, '').trim();
    if (!text) return {};
    const url = (text.match(/https?:\/\/[^\s)]+/i)?.[0] ?? '').replace(/[.,;]+$/, '');
    const price = text.match(/(?:\$|cop\s*)\s*[\d.,]{5,}/i)?.[0] ?? '';
    const area = text.match(/(\d+(?:[.,]\d+)?)\s*(?:m2|m²|mt2|metros?\s*cuadrados?)/i)?.[1] ?? '';
    const phone = text.replace(/https?:\/\/\S+/gi, '').replace(/(?:\$|cop\s*)\s*[\d.,]+/gi, '')
        .match(/(?:tel[eé]fono|tel|celular|contacto|whatsapp)\s*[:.]?\s*(\+?[\d ()-]{7,20})/i)?.[1]?.trim() ?? '';
    const lower = text.toLowerCase();
    let hostname = '';
    try { hostname = url ? new URL(url).hostname.replace(/^www\./, '') : ''; } catch { /* Keep unrecognized text for review. */ }
    const operation = /arriendo|canon|renta/.test(lower) ? 'Arriendo' : (/venta|vende|precio/.test(lower) ? 'Venta' : '');
    const priceUnit = operation === 'Arriendo' ? 'canon_mensual' : (price ? 'precio_total' : '');
    const project = text.split('\n').find(line => /edificio|conjunto|proyecto|condominio|torre/i.test(line)) ?? '';
    return {
        source_type: url ? 'portal' : 'otro',
        source_name: hostname ? titleFromHost(hostname) : '',
        source_url: url,
        operation,
        price_amount: price,
        price_unit: priceUnit,
        area_m2: area,
        contact_phone: phone,
        project_name: project.trim().slice(0, 180),
        comparability_notes: text.replace(/\s+/g, ' ').slice(0, 500),
    };
}

export function parseComparableText(text) {
    const raw = String(text ?? '').trim();
    if (!raw) return [];
    const lines = raw.split(/\n+/).filter(line => line.trim() !== '');
    const tabular = lines.filter(line => line.includes('\t'));
    if (tabular.length) return parseTabularRows(tabular);
    const blocks = raw.split(/\n\s*\n/).flatMap(block => splitMultiUrlBlock(block));
    return blocks.map(parseComparableBlock).filter(row => meaningful(row));
}

function parseTabularRows(lines) {
    const aliases = { fuente: 'source_name', enlace: 'source_url', url: 'source_url', precio: 'price_amount',
        canon: 'price_amount', area: 'area_m2', 'area m2': 'area_m2', telefono: 'contact_phone',
        barrio: 'neighborhood', sector: 'neighborhood', operacion: 'operation', proyecto: 'project_name' };
    const normalize = text => text.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
    const headers = lines[0].split('\t').map(cell => aliases[normalize(cell)] ?? '');
    const hasHeaders = headers.filter(Boolean).length >= 2;
    return lines.slice(hasHeaders ? 1 : 0).map(line => {
        const cells = line.split('\t').map(cell => cell.trim());
        const parsed = parseComparableBlock(cells.join('\n'));
        if (hasHeaders) headers.forEach((key, index) => { if (key) parsed[key] = cells[index] ?? ''; });
        // Bare numbers are ambiguous without column headers; leave them for review.
        return parsed;
    }).filter(meaningful);
}

function splitMultiUrlBlock(block) {
    const text = block.trim();
    if ((text.match(/https?:\/\//gi) ?? []).length < 2) return [text];
    return text.split(/(?=https?:\/\/)/i).map(part => part.trim()).filter(Boolean);
}

function titleFromHost(hostname) {
    return hostname.split('.')[0].replace(/[-_]+/g, ' ').replace(/\b\w/g, char => char.toUpperCase());
}

function meaningful(row) {
    return ['source_url', 'price_amount', 'area_m2', 'comparability_notes'].some(key => String(row[key] ?? '').trim() !== '');
}

function field(row, key) {
    return [...row.querySelectorAll('[name]')].find(input => input.name.endsWith(`[${key}]`)) ?? null;
}

function isBlankRow(row) {
    return !hasComparableData(Object.fromEntries([...row.querySelectorAll('[name]')]
        .map(input => [input.name.match(/\[([^\]]+)\]$/)[1], input.value])));
}

function setField(row, key, value) {
    const input = field(row, key);
    if (!input || value === undefined || value === null || String(value).trim() === '') return;
    if (input.tagName === 'SELECT' && ![...input.options].some(option => option.value === value)) return;
    input.value = value;
}

export function fillRows(form, rows, defaultQuery, confirmDistinct) {
    const existing = [...form.querySelectorAll('tbody tr')].map(row => Object.fromEntries([...row.querySelectorAll('[name]')]
        .map(input => [input.name.match(/\[([^\]]+)\]$/)[1], input.value])));
    const targets = [...form.querySelectorAll('tbody tr')].filter(isBlankRow);
    const known = new Set([...form.querySelectorAll('tbody tr')]
        .map(row => comparableUrlKey(field(row, 'source_url')?.value)).filter(Boolean));
    let count = 0, firstRow = null;
    let duplicates = 0, overflow = 0, suspected = 0;
    rows.forEach(data => {
        const key = comparableUrlKey(data.source_url);
        if (key && known.has(key)) { duplicates++; return; }
        const review = checkComparableDuplicates(data, existing, confirmDistinct ? message => confirmDistinct(data, message) : undefined);
        if (review.blocked) { review.exact ? duplicates++ : suspected++; return; }
        const row = targets.shift();
        if (!row) { overflow++; return; }
        firstRow ??= row.sectionRowIndex;
        if (key) known.add(key);
        existing[row.sectionRowIndex] = data;
        Object.entries(data).forEach(([key, value]) => setField(row, key, value));
        setField(row, 'query_used', defaultQuery);
        setField(row, 'active', 'si');
        setField(row, 'status', 'por_verificar');
        count++;
    });
    if (count > 0) {
        form.dispatchEvent(new Event('input', { bubbles: true }));
        form.dispatchEvent(new CustomEvent('comparable-imported', { detail: firstRow }));
    }
    return { count, duplicates, overflow, suspected };
}

export function installComparableBulkImport() {
    document.addEventListener('click', event => {
        const button = event.target.closest?.('[data-comparable-bulk-apply]');
        if (!button) return;
        const panel = button.closest('[data-comparable-bulk-panel]');
        const form = document.getElementById('tabla-madre-83');
        const input = panel?.querySelector('[data-comparable-bulk-input]');
        const message = panel?.querySelector('[data-comparable-bulk-message]');
        const rows = parseComparableText(input?.value ?? '');
        const result = form ? fillRows(form, rows, panel?.dataset?.defaultQuery ?? '') : { count: 0, duplicates: 0, overflow: 0 };
        if (message) message.textContent = rows.length
            ? `${result.count} muestra(s) cargada(s). ${result.duplicates} enlace(s) repetido(s) omitido(s). ${result.suspected || 0} posible(s) duplicado(s) sin agregar: revisa la tabla. ${result.overflow} sin cargar por límite de 60. El texto original se conserva; consulta el estado de guardado.`
            : 'Pega enlaces, texto de avisos o filas con datos antes de cargar.';
        form?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
}
