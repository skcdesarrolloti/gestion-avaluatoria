const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const states = new WeakMap();
const activeStates = new Set();

function stateFor(form) {
    if (!states.has(form)) {
        const state = { form, dirty: false, saving: false, timer: null, controller: null, promise: null, revision: 0, conflict: false, extras: {} };
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

function formBody(form, extras = {}) {
    const body = new FormData(form);
    form.querySelectorAll('input[type="file"][name]').forEach(input => body.delete(input.name));
    Object.entries(extras).forEach(([key, value]) => body.set(key, value));
    return body;
}

function updateVersion(form, result) {
    if (!Number.isInteger(result.version)) return;
    const field = form.querySelector('input[name="version"]');
    if (field) field.value = String(result.version);
    form.closest?.('[data-ph-section]')?.querySelectorAll('input[name="version"]').forEach(node => {
        node.value = String(result.version);
    });
}

function updateDossierNumber(form, result) {
    if (!result.expediente_number) return;
    form.ownerDocument.querySelectorAll('[data-expediente-number-output]').forEach(node => {
        if ('value' in node) node.value = result.expediente_number;
        else node.textContent = result.expediente_number;
    });
    form.ownerDocument.querySelectorAll('[data-dossier-card]').forEach(card => {
        card.classList.remove('border-amber-200', 'bg-amber-50', 'text-amber-950');
        card.classList.add('border-emerald-200', 'bg-emerald-50', 'text-emerald-950');
    });
    form.ownerDocument.querySelectorAll('[data-dossier-state]').forEach(node => { node.textContent = 'Expediente creado'; });
    form.ownerDocument.querySelectorAll('[data-dossier-help]').forEach(node => {
        node.classList.remove('text-amber-900');
        node.classList.add('text-emerald-900');
    });
    form.ownerDocument.querySelectorAll('[data-create-dossier-panel]').forEach(node => { node.hidden = true; });
    window.dispatchEvent?.(new CustomEvent('ga:dossier-created', { detail: { expediente_number: result.expediente_number } }));
}

async function save(form) {
    const state = stateFor(form);
    if (state.conflict) return;
    if (state.saving) return state.promise;
    if (!state.dirty) return;
    const revision = state.revision;
    const extras = state.extras;
    state.extras = {};
    state.saving = true;
    state.controller = new AbortController();
    const buttons = [...form.querySelectorAll('button[type="submit"]')];
    buttons.forEach(button => { button.disabled = true; });
    setStatus(form, 'Guardando cambios...', 'saving');
    state.promise = (async () => { try {
        const response = await fetch(form.dataset.autosaveEndpoint, {
            method: 'POST',
            body: formBody(form, extras),
            headers: { Accept: 'application/json', 'X-CSRF-Token': csrfToken() },
            credentials: 'same-origin',
            signal: state.controller.signal,
        });
        const result = await response.json();
        if (response.status === 409) state.conflict = true;
        if (!response.ok || result.ok !== true) throw new Error(result.message || 'No se pudo confirmar el guardado.');
        updateVersion(form, result);
        updateDossierNumber(form, result);
        state.dirty = state.revision !== revision;
        const time = result.saved_at ? new Date(result.saved_at).toLocaleTimeString('es-CO') : new Date().toLocaleTimeString('es-CO');
        setStatus(form, state.dirty ? 'Cambios pendientes' : 'Autoguardado confirmado: ' + time, state.dirty ? 'pending' : 'saved');
    } catch (error) {
        if (error.name !== 'AbortError') setStatus(form, 'Pendiente de guardar: ' + error.message, 'error');
        state.dirty = true;
    } finally {
        state.saving = false;
        state.controller = null;
        state.promise = null;
        buttons.forEach(button => { button.disabled = false; });
        if (state.dirty && !state.conflict && state.revision !== revision) state.timer = setTimeout(() => save(form), 800);
    } })();
    return state.promise;
}

function markDirty(form, target, delay = null, extras = {}) {
    if (!form?.matches?.('[data-module-autosave]') || !form.dataset.autosaveEndpoint) return;
    if (target instanceof HTMLInputElement && target.type === 'file') return;
    const state = stateFor(form);
    state.dirty = true;
    state.extras = { ...state.extras, ...extras };
    state.revision++;
    clearTimeout(state.timer);
    if (state.conflict) return;
    setStatus(form, 'Cambios pendientes', 'pending');
    state.timer = setTimeout(() => save(form), delay ?? (target?.hasAttribute?.('data-autosave-now') ? 0 : 800));
}

function cancel(form) {
    if (!form?.matches?.('[data-module-autosave]')) return;
    const state = stateFor(form);
    clearTimeout(state.timer);
    state.controller?.abort();
    state.dirty = false;
}

export async function flushModuleAutosaves() {
    for (const state of activeStates) if (state.form.isConnected === false) activeStates.delete(state);
    await Promise.all([...activeStates].map(async state => {
        clearTimeout(state.timer);
        await save(state.form);
        if (state.dirty && !state.conflict) await save(state.form);
    }));
    return ![...activeStates].some(state => state.dirty || state.saving);
}

export function installModuleAutosave() {
    window.gaFlushAutosaves = flushModuleAutosaves;
    document.addEventListener('input', event => markDirty(formFor(event.target), event.target));
    document.addEventListener('change', event => markDirty(formFor(event.target), event.target));
    document.addEventListener('click', event => {
        const button = event.target.closest?.('[data-create-dossier]');
        if (!button?.form) return;
        event.preventDefault();
        markDirty(button.form, button, 0, { create_expediente: '1' });
    });
    document.addEventListener('submit', event => {
        const form = event.target;
        if (form.hasAttribute?.('data-save-in-place')) {
            event.preventDefault();
            event.stopImmediatePropagation();
            const state = stateFor(form);
            clearTimeout(state.timer);
            state.dirty = true;
            save(form);
        } else { cancel(form); }
    }, true);
    window.addEventListener('beforeunload', event => {
        if (![...activeStates].some(state => state.dirty || state.saving)) return;
        event.preventDefault();
        event.returnValue = '';
    });
}
