// Chrome can omit article/data-test attributes when serializing a selection.
// Keep each result bounded by its own detail URL; never pair adjacent cards.
export function properatiClipboardCards(root, validUrl) {
    const cards = [], seen = new Set();
    for (const anchor of root.querySelectorAll('a[href]')) {
        const url = validUrl(anchor.getAttribute('href'));
        if (!url || seen.has(url)) continue;
        for (let box = anchor.parentElement; box; box = box.parentElement) {
            const urls = new Set([...box.querySelectorAll('a[href]')]
                .map(link => validUrl(link.getAttribute('href'))).filter(Boolean));
            if (urls.size !== 1) break;
            const values = [...new Set([...box.querySelectorAll('*')]
                .filter(el => !el.closest('script, style, svg'))
                .map(el => el.textContent.replace(/\s+/g, ' ').trim()).filter(Boolean))];
            const prices = values.filter(value => /^\$\s*[\d.]+(?:,\d{1,2})?$/.test(value));
            const areas = values.filter(value => /^\d+(?:[.,]\d+)?\s*m[2²]$/.test(value));
            const locations = values.filter(value => /^[^,$\d]+,\s*[^,$\d]+(?:,\s*[^,$\d]+)?$/.test(value)
                && !/publicado|foto|baños/i.test(value));
            const heading = [anchor.textContent, anchor.getAttribute('title'), ...values]
                .find(value => /^oficina en venta en\s/i.test(String(value || '').trim()));
            if (!heading || prices.length !== 1 || areas.length !== 1 || locations.length !== 1) continue;
            cards.push({ url, heading: heading.trim(), price: prices[0], area: areas[0], location: locations[0],
                agency: box.querySelector('.agency__name')?.textContent || '', text:box.textContent });
            seen.add(url);
            break;
        }
    }
    return cards;
}
