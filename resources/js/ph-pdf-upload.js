const prepared = new WeakMap();
const running = new Set();

function progress(form, percent, message) {
    form.querySelector('[data-upload-progress-panel]')?.classList.remove('hidden');
    const bar = form.querySelector('[data-upload-progress-bar]');
    if (bar) {
        bar.style.width = `${percent}%`;
        bar.setAttribute('aria-valuenow', String(Math.round(percent)));
    }
    const label = form.querySelector('[data-upload-progress-text]');
    if (label) label.textContent = message;
}

async function prepare(event) {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || !form.matches('[data-ph-pdf-render]')) return;
    if (form.dataset.phPdfReady === '1') return;
    event.preventDefault(); event.stopImmediatePropagation();
    if (running.has(form)) return;
    running.add(form);
    const controls = [...form.querySelectorAll('button, input[type=file], select')];
    try {
        if (window.gaFlushAutosaves && !await window.gaFlushAutosaves()) {
            throw new Error('Guarda los cambios pendientes de la ficha antes de analizar el soporte.');
        }
        const profile = form.closest('[data-ph-section]')?.querySelector('[data-module-autosave] input[name=version]');
        if (profile) form.querySelector('input[name=version]').value = profile.value;
        controls.forEach(node => { node.disabled = true; });
        progress(form, 1, 'Preparando lectura completa del documento…');
        const files = [...(form.querySelector('input[type=file]')?.files || [])];
        if (!prepared.has(form) && files.some(file => /\.pdf$/i.test(file.name))) {
            const { preparePhPdfText } = await import(new URL('ph-pdf-reader.js', import.meta.url));
            prepared.set(form, await preparePhPdfText(files, {
                onProgress: (message, fraction) => progress(form, 5 + fraction * 80, message),
            }));
        }
        if (window.gaFlushAutosaves && !await window.gaFlushAutosaves()) {
            throw new Error('Hay cambios sin guardar. Reintenta cuando la ficha confirme el guardado.');
        }
        form.dataset.phPdfReady = '1';
        controls.forEach(node => { node.disabled = false; });
        progress(form, 88, 'Lectura preparada. Guardando soporte y sugerencias…');
        form.requestSubmit(event.submitter || undefined);
    } catch (error) {
        progress(form, 0, `Lectura no completada: ${error.message} Puedes reintentar.`);
        controls.forEach(node => { node.disabled = false; });
    } finally { running.delete(form); }
}

export function installPhPdfUpload() {
    document.addEventListener('submit', prepare, true);
    document.addEventListener('change', event => {
        const input = event.target;
        const form = input.closest?.('form[data-ph-pdf-render]');
        if (!form || input.type !== 'file') return;
        prepared.delete(form); delete form.dataset.phPdfReady;
    }, true);
    document.addEventListener('ga:upload-formdata', event => {
        const docs = prepared.get(event.target);
        if (docs?.length) event.detail.body.set('ph_client_text',
            new Blob([JSON.stringify(docs)], { type: 'application/json' }), 'lectura-ph.json');
        const version = event.target.querySelector('input[name=version]');
        if (version) event.detail.body.set('version', version.value);
        delete event.target.dataset.phPdfReady;
    });
    window.addEventListener('beforeunload', event => {
        if (!running.size) return;
        event.preventDefault(); event.returnValue = '';
    });
}
