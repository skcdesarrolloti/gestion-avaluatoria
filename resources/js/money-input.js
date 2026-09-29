export function normalizeMoney(value) {
    const clean = String(value ?? '').replace(/[^\d,.]/g, '');
    if (!clean) return '';
    const comma = clean.lastIndexOf(',');
    const dot = clean.lastIndexOf('.');
    let normalized = clean;
    if (comma > dot) normalized = clean.replaceAll('.', '').replace(',', '.');
    else if (/^\d{1,3}(\.\d{3})+$/.test(clean)) normalized = clean.replaceAll('.', '');
    else normalized = clean.replaceAll(',', '');
    const amount = Number(normalized);
    if (!Number.isFinite(amount)) return '';
    const decimals = amount % 1 === 0 ? 0 : 2;
    return new Intl.NumberFormat('es-CO', {
        style: 'currency', currency: 'COP', minimumFractionDigits: decimals, maximumFractionDigits: decimals,
    }).format(amount).replace('COP', '$').replace(/\s+/g, ' ').trim();
}

export function installMoneyInputs() {
    document.addEventListener('blur', event => {
        const input = event.target;
        if (!input?.matches?.('[data-money-input]')) return;
        input.value = normalizeMoney(input.value);
    }, true);
}
