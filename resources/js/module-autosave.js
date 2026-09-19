const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const states = new WeakMap();
const activeStates = new Set();

function stateFor(form) {
    if (!states.has(form)) {
        const state = { form, dirty: false, saving: false, timer: null, controller: null, promise: null };
        states.set(form, state);
        activeStates.add(state);
    }
    return states.get(form);
}

function statusNodes(form) {
    const id = form.id ? `[data-autosave-status-for="${CSS.escape(form.id)}"]` : '';
    return [...form.querySelectorAll('[data-autosave-status]'), ...(id ? document.querySelectorAll(id) : [])];
}

function setStatus(form, text, tone = 'neutral') {
    statusNodes(form).forEach(node => {
        node.textContent = text;
        node.dataset.autosaveTone = tone;
    });
}

function formFor(target) {
    if (target instanceof HTMLFormElement) return target;
    if (target instanceof HTMLInputElement || target instanceof HTMLSelectElement || target instanceof HTMLTextAreaElement) {
        return target.form;
    }
    return target.closest?.('form') ?? null;
}

function formBody(form) {
    const body = new FormData(form);
    form.querySelectorAll('input[type="file"][name]').forEach(input => body.delete(input.name));
    return body;
}

function updateVersion(form, result) {
    if (!Number.isInteger(result.version)) return;
    const field = form.querySelector('input[name="version"]');
    if (field) field.value = String(result.version);
}

async function save(form) {
    const state = stateFor(form);
    if (state.saving) return state.promise;
    if (!state.dirty) return;
    state.saving = true;
    state.controller = new AbortController();
    setStatus(form, 'Guardando cambios...', 'saving');
    state.promise = (async () => { try {
        const response = await fetch(form.dataset.autosaveEndpoint, {
            method: 'POST',
            body: formBody(form),
            headers: { Accept: 'application/json', 'X-CSRF-Token': csrfToken() },
            credentials: 'same-origin',
            signal: state.controller.signal,
        });
        const result = await response.json();
        if (!response.ok || result.ok !== true) throw new Error(result.message || 'No se pudo confirmar el guardado.');
        updateVersion(form, result);
        state.dirty = false;
        const time = result.saved_at ? new Date(result.saved_at).toLocaleTimeString('es-CO') : new Date().toLocaleTimeString('es-CO');
        setStatus(form, 'Autoguardado confirmado: ' + time, 'saved');
    } catch (error) {
        if (error.name !== 'AbortError') setStatus(form, 'Pendiente de guardar: ' + error.message, 'error');
        state.dirty = true;
    } finally {
        state.saving = false;
        state.controller = null;
        state.promise = null;
    } })();
    return state.promise;
}

function markDirty(form, target) {
    if (!form?.matches?.('[data-module-autosave]') || !form.dataset.autosaveEndpoint) return;
    if (target instanceof HTMLInputElement && target.type === 'file') return;
    const state = stateFor(form);
    state.dirty = true;
    clearTimeout(state.timer);
    setStatus(form, 'Cambios pendientes', 'pending');
    state.timer = setTimeout(() => save(form), 800);
}

function cancel(form) {
    if (!form?.matches?.('[data-module-autosave]')) return;
    const state = stateFor(form);
    clearTimeout(state.timer);
    state.controller?.abort();
    state.dirty = false;
}

export async function flushModuleAutosaves() {
    await Promise.all([...activeStates].map(state => {
        clearTimeout(state.timer);
        return save(state.form);
    }));
    return ![...activeStates].some(state => state.dirty || state.saving);
}

export function installModuleAutosave() {
    window.gaFlushAutosaves = flushModuleAutosaves;
    document.addEventListener('input', event => markDirty(formFor(event.target), event.target));
    document.addEventListener('change', event => markDirty(formFor(event.target), event.target));
    document.addEventListener('submit', event => cancel(event.target), true);
    window.addEventListener('beforeunload', event => {
        if (![...activeStates].some(state => state.dirty || state.saving)) return;
        event.preventDefault();
        event.returnValue = '';
    });
}
