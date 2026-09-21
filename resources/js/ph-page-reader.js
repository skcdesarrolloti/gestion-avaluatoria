export async function readPhPages(pdf, recognize, progress = () => {}) {
    const pages = [];
    for (let number = 1; number <= pdf.numPages; number++) {
        const page = await pdf.getPage(number);
        try {
            progress(number, pdf.numPages, 'leyendo texto');
            const content = await page.getTextContent();
            let text = content.items.map(item => (item.str || '') + (item.hasEOL ? '\n' : ' ')).join('').trim();
            let method = 'text', confidence = 100;
            if ((text.match(/\p{L}/gu) || []).length < 100) {
                progress(number, pdf.numPages, 'OCR local');
                const result = await recognize(page);
                text = result.text.trim(); confidence = result.confidence; method = 'ocr';
            }
            pages.push({ page: number, text, method, confidence });
        } finally { page.cleanup(); }
    }
    return pages;
}
