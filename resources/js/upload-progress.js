const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const CHUNK_BYTES = 4 * 1024 * 1024;
const CHUNK_THRESHOLD = 12 * 1024 * 1024;

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

function uploadInput(form) {
    return form.querySelector('input[type="file"][name="ph_document[]"], input[type="file"][name="ph_document"]');
}

export function uploadErrorMessage(xhr) {
    try {
        const data = JSON.parse(xhr?.responseText ?? '');
        if (typeof data?.message === 'string' && data.message.trim() !== '') return data.message.slice(0, 220);
    } catch {}
    const text = (xhr?.responseText ?? '').replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
    if (xhr?.status === 413) return 'El servidor rechazó el archivo por tamaño. Lo intentaré por partes si el formulario ya está actualizado; si persiste, el hosting bloquea cargas grandes antes de PHP.';
    if (xhr?.status === 419) return 'La sesión de seguridad venció. Recarga la página e intenta de nuevo.';
    if (xhr?.status === 422 && text) return text.slice(0, 220);
    if (xhr?.status >= 500) return text ? text.slice(0, 220) : 'El servidor falló durante la lectura PH. Intenta de nuevo; si se repite, revisa el log del hosting.';
    return 'No se pudo completar la subida. Revisa el archivo e intenta nuevamente.';
}

function xhrRequest(url, body, { accept = 'text/html', timeout = 300000, progress = null } = {}) {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.timeout = timeout;
        xhr.setRequestHeader('Accept', accept);
        xhr.setRequestHeader('X-Requested-With', 'upload-progress');
        const token = csrfToken();
        if (token) xhr.setRequestHeader('X-CSRF-Token', token);
        if (progress) xhr.upload.onprogress = progress;
        xhr.onload = () => xhr.status >= 200 && xhr.status < 400 ? resolve(xhr) : reject(xhr);
        xhr.onerror = () => reject(new Error('La conexión se interrumpió durante la subida. Intenta nuevamente.'));
        xhr.ontimeout = () => reject(new Error('La subida o análisis tardó demasiado. Intenta con menos archivos por lote o un ZIP más liviano.'));
        xhr.send(body);
    });
}

async function submitChunkedUpload(form, submitter, file) {
    const parts = ui(form);
    const uploadId = (globalThis.crypto?.randomUUID?.() ?? `${Date.now()}-${Math.random()}`).replace(/[^A-Za-z0-9_-]/g, '');
    const total = Math.ceil(file.size / CHUNK_BYTES);
    setDisabled(parts, true);
    setProgress(parts, 1, 'Preparando subida por partes...');
    try {
        for (let index = 0; index < total; index++) {
            const start = index * CHUNK_BYTES;
            const body = syncToken(new FormData());
            body.set('upload_id', uploadId); body.set('index', String(index)); body.set('total', String(total));
            body.set('filename', file.name); body.set('size', String(file.size));
            body.append('chunk', file.slice(start, Math.min(start + CHUNK_BYTES, file.size)), file.name);
            await xhrRequest(form.dataset.uploadChunkUrl, body, {
                accept: 'application/json', timeout: 90000,
                progress: event => {
                    const loaded = event.lengthComputable ? event.loaded : 0;
                    const percent = Math.min(88, Math.round(((start + loaded) / Math.max(file.size, 1)) * 88));
                    setProgress(parts, percent, `Subiendo soporte PH por partes: ${percent}%`);
                },
            });
        }
        const finish = syncToken(new FormData());
        finish.set('upload_id', uploadId); finish.set('total', String(total));
        finish.set('filename', file.name); finish.set('size', String(file.size));
        finish.set('ph_typology', form.querySelector('[name="ph_typology"]')?.value ?? '');
        setProgress(parts, 92, 'Archivo recibido. Ensamblando y analizando soporte PH...');
        const xhr = await xhrRequest(form.dataset.uploadFinishUrl, finish, { timeout: Number.parseInt(form.dataset?.uploadTimeout || '300000', 10) });
        setProgress(parts, 100, 'Lectura terminada. Actualizando pantalla...');
        renderResponse(xhr, form);
    } catch (error) {
        setDisabled(parts, false);
        setProgress(parts, 100, error instanceof Error ? error.message : uploadErrorMessage(error));
    }
}

export function submitUpload(form, submitter = null) {
    const parts = ui(form);
    const input = uploadInput(form);
    const file = input?.files?.length === 1 ? input.files[0] : null;
    if (file && file.size > CHUNK_THRESHOLD && form.dataset?.uploadChunkUrl && form.dataset?.uploadFinishUrl) {
        submitChunkedUpload(form, submitter, file);
        return null;
    }
    const xhr = new XMLHttpRequest();
    const body = syncToken(formBody(form, submitter));
    setDisabled(parts, true);
    setProgress(parts, 1, 'Preparando subida...');
    xhr.open((form.method || 'POST').toUpperCase(), form.action, true);
    xhr.timeout = Number.parseInt(form.dataset?.uploadTimeout || '300000', 10);
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
        setProgress(parts, 100, uploadErrorMessage(xhr));
    };
    xhr.onerror = () => {
        setDisabled(parts, false);
        setProgress(parts, 100, 'La conexión se interrumpió durante la subida. Intenta nuevamente.');
    };
    xhr.ontimeout = () => {
        setDisabled(parts, false);
        setProgress(parts, 100, 'La subida o análisis tardó demasiado. Intenta con menos archivos por lote o un ZIP más liviano.');
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
