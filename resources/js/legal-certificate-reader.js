import * as pdfjsLib from 'pdfjs-dist/build/pdf.mjs';

const form = document.querySelector('[data-legal-certificate-form]');
const workerSrc = new URL('/assets/pdf.worker.mjs', window.location.origin).toString();
pdfjsLib.GlobalWorkerOptions.workerSrc = workerSrc;

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

async function prepareBrowserText(event) {
    if (!form || form.dataset.clientTextReady === '1') return;
    const fileInput = form.querySelector('input[type="file"][name="legal_certificate"]');
    const hidden = form.querySelector('[data-client-extracted-text]');
    const submitButton = form.querySelector('button[type="submit"]');
    const file = fileInput?.files?.[0] || null;
    if (!file || !hidden || !isPdf(file)) return;

    event.preventDefault();
    submitButton?.setAttribute('disabled', 'disabled');
    try {
        setStatus('Preparando lectura rigurosa del PDF antes de subirlo...');
        const text = await extractPdfText(file);
        if (text.length > 200 && looksLikeCertificate(text)) {
            hidden.value = text;
            setStatus(`Texto del PDF preparado: ${text.length.toLocaleString('es-CO')} caracteres. Subiendo certificado...`);
        } else {
            hidden.value = '';
            setStatus('El navegador no encontró texto claro; se intentará la lectura del servidor.', 'error');
        }
    } catch (error) {
        hidden.value = '';
        setStatus('No fue posible leer el PDF en navegador; se intentará la lectura del servidor.', 'error');
    } finally {
        form.dataset.clientTextReady = '1';
        HTMLFormElement.prototype.submit.call(form);
    }
}

if (form) {
    form.addEventListener('submit', prepareBrowserText);
}
