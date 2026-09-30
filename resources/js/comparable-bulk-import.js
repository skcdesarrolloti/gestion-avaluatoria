export function parseComparableBlock(block) {
    const text = String(block ?? '').replace(/\r/g, '').trim();
    if (!text) return {};
    const url = (text.match(/https?:\/\/[^\s)]+/i)?.[0] ?? '').replace(/[.,;]+$/, '');
    const price = text.match(/(?:\$|cop\s*)\s*[\d.,]{5,}/i)?.[0] ?? '';
    const area = text.match(/(\d+(?:[.,]\d+)?)\s*(?:m2|m²|mt2|metros?\s*cuadrados?)/i)?.[1] ?? '';
    const phone = text.match(/(?:\+?57\s*)?(?:3\d{9}|60\d{8}|[1-9]\d{6,9})/)?.[0] ?? '';
    const lower = text.toLowerCase();
    const hostname = url ? new URL(url).hostname.replace(/^www\./, '') : '';
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
    if (tabular.length > 1) return tabular.map(line => parseTabularLine(line)).filter(row => meaningful(row));
    const blocks = raw.split(/\n\s*\n/).flatMap(block => splitMultiUrlBlock(block));
    return blocks.map(parseComparableBlock).filter(row => meaningful(row));
}

function parseTabularLine(line) {
    const cells = line.split('\t').map(cell => cell.trim());
    const block = cells.join('\n');
    const parsed = parseComparableBlock(block);
    return {
        ...parsed,
        source_name: cells[0] || parsed.source_name,
        source_url: cells.find(cell => /^https?:\/\//i.test(cell)) || parsed.source_url,
        price_amount: cells.find(cell => /(?:\$|cop\s*)?\s*[\d.,]{5,}/i.test(cell)) || parsed.price_amount,
        area_m2: cells.find(cell => /\d+(?:[.,]\d+)?\s*(?:m2|m²|mt2)?$/i.test(cell)) || parsed.area_m2,
        comparability_notes: block.replace(/\s+/g, ' ').slice(0, 500),
    };
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
    return ['source_name', 'source_url', 'price_amount', 'area_m2', 'neighborhood', 'project_name', 'comparability_notes']
        .every(key => String(field(row, key)?.value ?? '').trim() === '');
}

function setField(row, key, value) {
    const input = field(row, key);
    if (!input || value === undefined || value === null || String(value).trim() === '') return;
    if (input.tagName === 'SELECT' && ![...input.options].some(option => option.value === value)) return;
    input.value = value;
}

function fillRows(form, rows, defaultQuery) {
    const targets = [...form.querySelectorAll('tbody tr')].filter(isBlankRow);
    let count = 0;
    rows.forEach(data => {
        const row = targets.shift();
        if (!row) return;
        Object.entries(data).forEach(([key, value]) => setField(row, key, value));
        setField(row, 'query_used', defaultQuery);
        setField(row, 'active', 'si');
        setField(row, 'status', 'por_verificar');
        count++;
    });
    if (count > 0) form.dispatchEvent(new Event('input', { bubbles: true }));
    return count;
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
        const count = form ? fillRows(form, rows, panel?.dataset?.defaultQuery ?? '') : 0;
        if (message) message.textContent = count ? `${count} fila(s) cargada(s). Revisa y guarda.` : 'Pega enlaces, texto de avisos o filas con datos antes de cargar.';
        form?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
}
