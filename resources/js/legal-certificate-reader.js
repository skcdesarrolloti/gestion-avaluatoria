import * as pdfjsLib from 'pdfjs-dist/build/pdf.mjs';
import { createWorker, PSM } from 'tesseract.js';

const form = document.querySelector('[data-legal-certificate-form]');
const script = document.querySelector('script[src*="legal-certificate-reader.js"]');
const assetsBase = new URL('.', script?.src || window.location.href).toString();
const workerSrc = new URL('pdf.worker.mjs', assetsBase).toString();
pdfjsLib.GlobalWorkerOptions.workerSrc = workerSrc;
let ocrWorker = null;

function setStatus(text, mode = '') {
    const target = form?.querySelector('[data-legal-reader-status]');
    if (!target) return;
    target.textContent = text;
    target.className = 'text-sm font-semibold lg:col-span-2 '
        + (mode === 'error' ? 'text-red-700' : 'text-teal-800');
}

function isPdf(file) {
    const name = String(file?.name || '').toLowerCase();
    const type = String(file?.type || '').toLowerCase();
    return name.endsWith('.pdf') || type.includes('pdf');
}

function isImage(file) {
    const name = String(file?.name || '').toLowerCase();
    const type = String(file?.type || '').toLowerCase();
    return type.startsWith('image/')
        || /\.(jpe?g|png|webp|tiff?|bmp)$/i.test(name);
}

function normalizeText(text) {
    return String(text || '')
        .replace(/[ \t]+/g, ' ')
        .replace(/\n{3,}/g, '\n\n')
        .trim();
}

function looksLikeCertificate(text) {
    return /matr|anotaci|folio|certificado|registro|supernotariado|departamento|municipio/i.test(text);
}

async function extractPdfText(file) {
    const buffer = await file.arrayBuffer();
    const loadingTask = pdfjsLib.getDocument({ data: buffer });
    const pdf = await loadingTask.promise;
    const pages = [];
    for (let pageNumber = 1; pageNumber <= pdf.numPages; pageNumber += 1) {
        setStatus(`Leyendo PDF en navegador: página ${pageNumber} de ${pdf.numPages}...`);
        const page = await pdf.getPage(pageNumber);
        const content = await page.getTextContent();
        const strings = content.items
            .map((item) => ('str' in item ? item.str : ''))
            .filter(Boolean);
        pages.push(strings.join(' '));
    }
    return normalizeText(pages.join('\n\n'));
}

async function createOcrWorker() {
    if (ocrWorker) return ocrWorker;
    setStatus('Preparando OCR local en el navegador...');
    ocrWorker = await createWorker('spa+eng', 1, {
        workerPath: new URL('tesseract/worker.min.js', assetsBase).toString(),
        corePath: new URL('tesseract/core', assetsBase).toString(),
        langPath: new URL('tesseract/lang', assetsBase).toString().replace(/\/$/, ''),
        cacheMethod: 'write',
        logger: (message) => {
            if (message.status === 'recognizing text') {
                const progress = Math.round((message.progress || 0) * 100);
                setStatus(`OCR en navegador: ${progress}%...`);
            }
        },
    });
    await ocrWorker.setParameters({
        preserve_interword_spaces: '1',
        tessedit_pageseg_mode: PSM.AUTO,
        user_defined_dpi: '220',
    });
    return ocrWorker;
}

async function extractImageOcr(image, detail = 'imagen') {
    const worker = await createOcrWorker();
    setStatus(`Leyendo ${detail} con OCR del navegador...`);
    const result = await worker.recognize(image);
    return normalizeText(result?.data?.text || '');
}

async function renderPageCanvas(page) {
    const viewport = page.getViewport({ scale: 1.7 });
    const canvas = document.createElement('canvas');
    canvas.width = Math.floor(viewport.width);
    canvas.height = Math.floor(viewport.height);
    const context = canvas.getContext('2d', { alpha: false });
    await page.render({ canvasContext: context, viewport }).promise;
    return canvas;
}

async function extractPdfOcr(file) {
    const pdf = await pdfjsLib.getDocument({ data: await file.arrayBuffer() }).promise;
    const pages = [];
    for (let pageNumber = 1; pageNumber <= pdf.numPages; pageNumber += 1) {
        setStatus(`PDF escaneado: OCR página ${pageNumber} de ${pdf.numPages}...`);
        const page = await pdf.getPage(pageNumber);
        const canvas = await renderPageCanvas(page);
        pages.push(await extractImageOcr(canvas, `página ${pageNumber}`));
        canvas.width = 0;
        canvas.height = 0;
    }
    return normalizeText(pages.join('\n\n'));
}

async function prepareBrowserText(event) {
    if (!form || form.dataset.clientTextReady === '1') return;
    const fileInput = form.querySelector('input[type="file"][name="legal_certificate"]');
    const hidden = form.querySelector('[data-client-extracted-text]');
    const submitButton = form.querySelector('button[type="submit"]');
    const file = fileInput?.files?.[0] || null;
    if (!file || !hidden || (!isPdf(file) && !isImage(file))) return;

    event.preventDefault();
    submitButton?.setAttribute('disabled', 'disabled');
    try {
        setStatus('Preparando lectura rigurosa antes de subir el certificado...');
        let text = isPdf(file) ? await extractPdfText(file) : '';
        if (isPdf(file) && (text.length < 200 || !looksLikeCertificate(text))) {
            setStatus('El PDF parece escaneado; activando OCR en navegador...');
            text = await extractPdfOcr(file);
        }
        if (isImage(file)) {
            text = await extractImageOcr(file);
        }
        if (text.length > 200 && looksLikeCertificate(text)) {
            hidden.value = text;
            setStatus(`Lectura preparada: ${text.length.toLocaleString('es-CO')} caracteres. Subiendo certificado...`);
        } else {
            hidden.value = '';
            setStatus('El navegador no encontró texto claro; se intentará la lectura del servidor.', 'error');
        }
    } catch (error) {
        hidden.value = '';
        setStatus('No fue posible leer el archivo en navegador; se intentará la lectura del servidor.', 'error');
    } finally {
        form.dataset.clientTextReady = '1';
        HTMLFormElement.prototype.submit.call(form);
    }
}

if (form) {
    form.addEventListener('submit', prepareBrowserText);
}
