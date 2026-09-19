const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

function formBody(form, submitter) {
    try { return new FormData(form, submitter); } catch { return new FormData(form); }
}

function syncToken(body) {
    const token = csrfToken();
    if (token && body instanceof FormData) body.set('_token', token);
    return body;
}

function ui(form) {
    return {
        bar: form.querySelector('[data-upload-progress-bar]'),
        panel: form.querySelector('[data-upload-progress-panel]'),
        text: form.querySelector('[data-upload-progress-text]'),
        submits: [...form.querySelectorAll('button[type="submit"], input[type="submit"]')],
    };
}

function setProgress(parts, percent, message) {
    parts.panel?.classList.remove('hidden');
    if (parts.bar) {
        parts.bar.style.width = `${Math.max(0, Math.min(100, percent))}%`;
        parts.bar.setAttribute('aria-valuenow', String(Math.round(percent)));
    }
    if (parts.text) parts.text.textContent = message;
}

function setDisabled(parts, disabled) {
    parts.submits.forEach(button => { button.disabled = disabled; });
}

export function uploadRedirectUrl(responseUrl, fallbackUrl, currentHref = window.location.href) {
    const target = new URL(responseUrl || fallbackUrl, currentHref);
    const action = new URL(fallbackUrl, currentHref);
    if (!target.hash && /\/ph\/soportes\/?$/i.test(action.pathname)) target.hash = 'ph';
    return target.toString();
}

function renderResponse(xhr, form) {
    const nextUrl = uploadRedirectUrl(xhr.responseURL, form.action);
    const type = xhr.getResponseHeader('content-type') ?? '';
    if (type.includes('text/html') && xhr.responseText) {
        history.replaceState({}, '', nextUrl);
        document.open();
        document.write(xhr.responseText);
        document.close();
        return;
    }
    window.location.assign(nextUrl);
}

export function submitUpload(form, submitter = null) {
    const parts = ui(form);
    const xhr = new XMLHttpRequest();
    const body = syncToken(formBody(form, submitter));
    setDisabled(parts, true);
    setProgress(parts, 1, 'Preparando subida...');
    xhr.open((form.method || 'POST').toUpperCase(), form.action, true);
    xhr.setRequestHeader('Accept', 'text/html');
    xhr.setRequestHeader('X-Requested-With', 'upload-progress');
    const token = csrfToken();
    if (token) xhr.setRequestHeader('X-CSRF-Token', token);
    xhr.upload.onprogress = event => {
        if (!event.lengthComputable) {
            setProgress(parts, 12, 'Subiendo archivo...');
            return;
        }
        const percent = Math.round((event.loaded / Math.max(event.total, 1)) * 100);
        setProgress(parts, percent, percent >= 100 ? 'Archivo recibido. Analizando soporte PH...' : `Subiendo soporte PH: ${percent}%`);
    };
    xhr.onload = () => {
        if (xhr.status >= 200 && xhr.status < 400) {
            setProgress(parts, 100, 'Lectura terminada. Actualizando pantalla...');
            renderResponse(xhr, form);
            return;
        }
        setDisabled(parts, false);
        setProgress(parts, 100, 'No se pudo completar la subida. Revisa el archivo e intenta nuevamente.');
    };
    xhr.onerror = () => {
        setDisabled(parts, false);
        setProgress(parts, 100, 'La conexión se interrumpió durante la subida. Intenta nuevamente.');
    };
    xhr.send(body);
    return xhr;
}

export function installUploadProgress() {
    document.addEventListener('submit', event => {
        const form = event.target;
        if (event.defaultPrevented || !(form instanceof HTMLFormElement) || !form.matches('[data-upload-progress]')) return;
        if (typeof XMLHttpRequest === 'undefined' || typeof FormData === 'undefined') return;
        event.preventDefault();
        submitUpload(form, event.submitter ?? null);
    });
}
