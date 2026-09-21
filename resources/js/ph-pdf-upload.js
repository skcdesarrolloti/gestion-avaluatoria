const preparedImages = new WeakMap();

function uploadParts(form) {
    return {
        panel: form.querySelector('[data-upload-progress-panel]'),
        bar: form.querySelector('[data-upload-progress-bar]'),
        text: form.querySelector('[data-upload-progress-text]'),
        submits: [...form.querySelectorAll('button[type="submit"], input[type="submit"]')],
    };
}

function setProgress(form, percent, message) {
    const parts = uploadParts(form);
    parts.panel?.classList.remove('hidden');
    if (parts.bar) {
        parts.bar.style.width = `${Math.max(0, Math.min(100, percent))}%`;
        parts.bar.setAttribute('aria-valuenow', String(Math.round(percent)));
    }
    if (parts.text) parts.text.textContent = message;
}

function setDisabled(form, disabled) {
    uploadParts(form).submits.forEach(button => { button.disabled = disabled; });
}

function files(form) {
    return [...(form.querySelector('input[type="file"][name="ph_document[]"], input[type="file"][name="ph_document"]')?.files ?? [])];
}

function hasPdf(items) {
    return items.some(file => /\.pdf$/i.test(file.name) || String(file.type).includes('pdf'));
}

async function prepareClientPdf(event) {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || !form.matches('[data-ph-pdf-render]')) return;
    if (form.dataset.phPdfReady === '1' || !hasPdf(files(form))) return;
    event.preventDefault();
    event.stopImmediatePropagation();
    setDisabled(form, true);
    try {
        setProgress(form, 1, 'Preparando PDF escaneado para MiniMax...');
        const moduleUrl = new URL('ph-pdf-reader.js', import.meta.url).toString();
        const { preparePhPdfImages } = await import(moduleUrl);
        const maxPages = Number.parseInt(form.dataset.phPdfMaxPages || '6', 10);
        const images = await preparePhPdfImages(files(form), {
            maxPages,
            onProgress: (message, step) => setProgress(form, Math.min(28, 4 + step * 4), message),
        });
        if (images.length > 0) preparedImages.set(form, images);
        form.dataset.phPdfReady = '1';
        setProgress(form, 30, images.length > 0
            ? 'PDF convertido. Subiendo soporte e imágenes para lectura IA/OCR...'
            : 'Subiendo soporte PH...');
        setDisabled(form, false);
        if (event.submitter instanceof HTMLElement && typeof form.requestSubmit === 'function') form.requestSubmit(event.submitter);
        else form.requestSubmit();
    } catch (error) {
        form.dataset.phPdfReady = '1';
        setDisabled(form, false);
        setProgress(form, 100, 'No fue posible convertir el PDF en navegador; se subirá el soporte original.');
        setTimeout(() => form.requestSubmit(), 300);
    }
}

function appendPreparedImages(event) {
    const form = event.target;
    const body = event.detail?.body;
    const images = preparedImages.get(form) || [];
    if (!(form instanceof HTMLFormElement) || !(body instanceof FormData) || images.length === 0) return;
    images.forEach(image => {
        body.append('ph_client_pdf_image[]', image.blob, image.name);
        body.append('ph_client_pdf_source[]', image.source);
        body.append('ph_client_pdf_page[]', String(image.page));
    });
}

function resetPrepared(event) {
    const input = event.target;
    if (!(input instanceof HTMLInputElement) || input.type !== 'file') return;
    const form = input.closest('form[data-ph-pdf-render]');
    if (!form) return;
    delete form.dataset.phPdfReady;
    preparedImages.delete(form);
}

export function installPhPdfUpload() {
    document.addEventListener('submit', prepareClientPdf, true);
    document.addEventListener('change', resetPrepared, true);
    document.addEventListener('ga:upload-formdata', appendPreparedImages, true);
}
