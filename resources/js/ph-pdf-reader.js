import * as pdfjsLib from 'pdfjs-dist/build/pdf.mjs';
import { createWorker, PSM } from 'tesseract.js';
import { readPhPages } from './ph-page-reader.js';

const assets = new URL('.', import.meta.url);
pdfjsLib.GlobalWorkerOptions.workerSrc = new URL('pdf.worker.mjs', assets).toString();

export async function preparePhPdfText(files, { onProgress = () => {} } = {}) {
    let worker;
    const documents = [];
    const recognize = async page => {
        worker ??= await createWorker('spa+eng', 1, {
            workerPath: new URL('tesseract/worker.min.js', assets).toString(),
            corePath: new URL('tesseract/core/', assets).toString(),
            langPath: new URL('tesseract/lang/', assets).toString(),
            cacheMethod: 'none',
        });
        await worker.setParameters({ tessedit_pageseg_mode: PSM.AUTO, preserve_interword_spaces: '1' });
        const base = page.getViewport({ scale: 1 });
        const viewport = page.getViewport({ scale: Math.min(3, 2400 / Math.max(base.width, base.height)) });
        const canvas = document.createElement('canvas');
        canvas.width = Math.ceil(viewport.width);
        canvas.height = Math.ceil(viewport.height);
        try {
            await page.render({ canvasContext: canvas.getContext('2d', { alpha: false }), viewport }).promise;
            const { data } = await worker.recognize(canvas);
            return { text: data.text, confidence: data.confidence };
        } finally { canvas.width = 0; canvas.height = 0; }
    };
    try {
        for (const [index, file] of [...files].entries()) {
            if (!/\.pdf$/i.test(file.name)) continue;
            const pdf = await pdfjsLib.getDocument({ data: await file.arrayBuffer() }).promise;
            try {
                const pages = await readPhPages(pdf, recognize,
                    (page, total, mode) => onProgress(`${file.name}: ${mode}, página ${page} de ${total}`, page / total));
                documents.push({ index, name: file.name, size: file.size, total: pdf.numPages, pages });
            } finally { await pdf.destroy(); }
        }
        return documents;
    } finally { if (worker) await worker.terminate(); }
}
