import { test } from 'node:test';
import assert from 'node:assert/strict';
import { installModuleAutosave } from '../resources/js/module-autosave.js';

class HTMLFormElement {
    constructor() {
        this.id = 'expediente-form';
        this.dataset = { autosaveEndpoint: '/avaluos/abc/expediente/autoguardar' };
        this.version = { name: 'version', value: '7' };
        this.status = { textContent: '', dataset: {} };
        this.dossier = { value: 'Pendiente de asignar' };
        this.banner = { textContent: 'Pendiente de asignar' };
        this.attrs = new Set();
        this.ownerDocument = {
            querySelectorAll: selector => selector === '[data-expediente-number-output]' ? [this.dossier, this.banner] : [],
        };
    }
    matches(selector) { return selector === '[data-module-autosave]'; }
    hasAttribute(name) { return this.attrs.has(name); }
    removeAttribute(name) { this.attrs.delete(name); }
    querySelector(selector) { return selector === 'input[name="version"]' ? this.version : null; }
    querySelectorAll(selector) { return selector === '[data-autosave-status]' ? [this.status] : []; }
}

class HTMLInputElement {
    constructor(form) { this.form = form; this.type = 'text'; this.name = 'titulo'; this.value = 'Casa'; }
}

class HTMLSelectElement {
    constructor(form) { this.form = form; this.name = 'appraiser_id'; this.attrs = new Set(['data-autosave-now']); }
    hasAttribute(name) { return this.attrs.has(name); }
}
class HTMLTextAreaElement {}

function setup(autoForms = []) {
    const originals = {
        HTMLFormElement: globalThis.HTMLFormElement,
        HTMLInputElement: globalThis.HTMLInputElement,
        HTMLSelectElement: globalThis.HTMLSelectElement,
        HTMLTextAreaElement: globalThis.HTMLTextAreaElement,
        CSS: globalThis.CSS,
        document: globalThis.document,
        window: globalThis.window,
        FormData: globalThis.FormData,
        setTimeout: globalThis.setTimeout,
        clearTimeout: globalThis.clearTimeout,
        fetch: globalThis.fetch,
    };
    const listeners = {};
    globalThis.HTMLFormElement = HTMLFormElement;
    globalThis.HTMLInputElement = HTMLInputElement;
    globalThis.HTMLSelectElement = HTMLSelectElement;
    globalThis.HTMLTextAreaElement = HTMLTextAreaElement;
    globalThis.CSS = { escape: value => value };
    globalThis.document = {
        querySelector: selector => selector === 'meta[name="csrf-token"]' ? { content: 'csrf-token' } : null,
        querySelectorAll: selector => selector === '[data-module-autosave][data-autosave-on-load]' ? autoForms : [],
        addEventListener: (type, handler) => { listeners[type] = handler; },
    };
    globalThis.window = { addEventListener: () => {} };
    globalThis.FormData = class {
        constructor(form) {
            this.values = new Map([['_token', 'old'], ['version', form.version.value]]);
        }
        delete(key) { this.values.delete(key); }
        get(key) { return this.values.get(key); }
    };
    let timerCallback = null, timerDelay = null;
    globalThis.setTimeout = (callback, delay) => { timerCallback = callback; timerDelay = delay; return 1; };
    globalThis.clearTimeout = () => {};
    installModuleAutosave();
    return {
        listeners,
        runTimer: () => timerCallback(),
        timerDelay: () => timerDelay,
        cleanup: () => Object.entries(originals).forEach(([key, value]) => { globalThis[key] = value; }),
    };
}

test('module autosave posts form data and updates optimistic version', async () => {
    const { listeners, runTimer, cleanup } = setup();
    const form = new HTMLFormElement();
    const input = new HTMLInputElement(form);
    globalThis.fetch = async (url, request) => {
        assert.equal(url, '/avaluos/abc/expediente/autoguardar');
        assert.equal(request.method, 'POST');
        assert.equal(request.headers['X-CSRF-Token'], 'csrf-token');
        assert.equal(request.credentials, 'same-origin');
        assert.equal(request.body.get('version'), '7');
        return { ok: true, json: async () => ({ ok: true, version: 8, expediente_number: '01-2026-09-001', saved_at: new Date().toISOString() }) };
    };
    listeners.input({ target: input });
    assert.equal(form.status.textContent, 'Cambios pendientes');
    await runTimer();
    assert.equal(form.version.value, '8');
    assert.equal(form.dossier.value, '01-2026-09-001');
    assert.equal(form.banner.textContent, '01-2026-09-001');
    assert.match(form.status.textContent, /Autoguardado confirmado/);
    cleanup();
});

test('appraiser change saves immediately to create dossier number', async () => {
    const { listeners, runTimer, timerDelay, cleanup } = setup();
    const form = new HTMLFormElement(), select = new HTMLSelectElement(form);
    globalThis.fetch = async () => ({ ok: true, json: async () => ({ ok: true, version: 8, expediente_number: '01-2026-09-001' }) });
    listeners.change({ target: select });
    assert.equal(timerDelay(), 0);
    await runTimer();
    assert.equal(form.dossier.value, '01-2026-09-001');
    cleanup();
});

test('auto selected appraiser saves on page load', async () => {
    const form = new HTMLFormElement();
    form.attrs.add('data-autosave-on-load');
    globalThis.fetch = async () => ({ ok: true, json: async () => ({ ok: true, version: 8, expediente_number: '01-2026-09-001' }) });
    const { runTimer, timerDelay, cleanup } = setup([form]);
    assert.equal(timerDelay(), 0);
    await runTimer();
    assert.equal(form.dossier.value, '01-2026-09-001');
    assert.equal(form.hasAttribute('data-autosave-on-load'), false);
    cleanup();
});

test('module autosave preserves edits made while a save is in flight', async () => {
    const { listeners, runTimer, cleanup } = setup();
    const form = new HTMLFormElement(), input = new HTMLInputElement(form);
    let resolve;
    globalThis.fetch = () => new Promise(done => { resolve = done; });
    listeners.input({ target: input });
    const saving = runTimer();
    listeners.input({ target: input });
    resolve({ ok: true, json: async () => ({ ok: true, version: 8 }) });
    await saving;
    assert.equal(form.status.textContent, 'Cambios pendientes');
    globalThis.fetch = async () => ({ ok: true, json: async () => ({ ok: true, version: 9 }) });
    await runTimer();
    assert.equal(form.version.value, '9');
    assert.match(form.status.textContent, /confirmado/);
    cleanup();
});

test('module autosave locks a conflict and retains the entered data', async () => {
    const { listeners, runTimer, cleanup } = setup();
    const form = new HTMLFormElement(), input = new HTMLInputElement(form);
    let calls = 0;
    globalThis.fetch = async () => {
        calls++;
        return { status: 409, ok: false, json: async () => ({ message: 'Conflicto entre pestañas' }) };
    };
    listeners.input({ target: input });
    await runTimer();
    listeners.input({ target: input });
    await runTimer();
    assert.equal(calls, 1);
    assert.equal(input.value, 'Casa');
    assert.equal(form.version.value, '7');
    assert.match(form.status.textContent, /Conflicto/);
    cleanup();
});

test('module autosave allows retry after network failure without reporting saved', async () => {
    const { listeners, runTimer, cleanup } = setup();
    const form = new HTMLFormElement(), input = new HTMLInputElement(form);
    globalThis.fetch = async () => { throw new Error('Sin conexión'); };
    listeners.input({ target: input });
    await runTimer();
    assert.match(form.status.textContent, /Sin conexión/);
    assert.equal(form.version.value, '7');
    globalThis.fetch = async () => ({ ok: true, json: async () => ({ ok: true, version: 8 }) });
    listeners.input({ target: input });
    await runTimer();
    assert.match(form.status.textContent, /confirmado/);
    cleanup();
});
