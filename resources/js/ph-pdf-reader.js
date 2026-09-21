import * as pdfjsLib from 'pdfjs-dist/build/pdf.mjs';

pdfjsLib.GlobalWorkerOptions.workerSrc = new URL('pdf.worker.mjs', import.meta.url).toString();

function isPdf(file) {
    const name = String(file?.name || '').toLowerCase();
    const type = String(file?.type || '').toLowerCase();
    return name.endsWith('.pdf') || type.includes('pdf');
}

async function renderPage(pdf, file, pageNumber, maxSide, quality) {
    const page = await pdf.getPage(pageNumber);
    const base = page.getViewport({ scale: 1 });
    const scale = Math.min(2.2, Math.max(1.25, maxSide / Math.max(base.width, base.height)));
    const viewport = page.getViewport({ scale });
    const canvas = document.createElement('canvas');
    canvas.width = Math.floor(viewport.width);
    canvas.height = Math.floor(viewport.height);
    const context = canvas.getContext('2d', { alpha: false });
    context.fillStyle = '#fff';
    context.fillRect(0, 0, canvas.width, canvas.height);
    await page.render({ canvasContext: context, viewport }).promise;
    const dataUrl = canvas.toDataURL('image/jpeg', quality);
    canvas.width = 0;
    canvas.height = 0;
    return {
        dataUrl,
        page: pageNumber,
        source: file.name,
        name: `${file.name.replace(/\.pdf$/i, '')}-pagina-${pageNumber}.jpg`,
    };
}

export async function preparePhPdfImages(files, { maxPages = 300, maxSide = 1200, quality = 0.72, onProgress = () => {} } = {}) {
    const pdfs = [...files].filter(isPdf);
    const images = [];
    let rendered = 0;
    for (const file of pdfs) {
        const pdf = await pdfjsLib.getDocument({ data: await file.arrayBuffer() }).promise;
        const pages = Math.min(pdf.numPages, Math.max(1, maxPages - images.length));
        for (let page = 1; page <= pages; page += 1) {
            rendered += 1;
            onProgress(`Convirtiendo PDF para MiniMax: página ${page} de ${pdf.numPages}...`, rendered);
            images.push(await renderPage(pdf, file, page, maxSide, quality));
            if (images.length >= maxPages) return images;
        }
    }
    return images;
}
