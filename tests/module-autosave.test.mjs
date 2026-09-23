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
        this.cardClasses = new Set(['border-amber-200', 'bg-amber-50', 'text-amber-950']);
        this.card = { classList: {
            add: (...names) => names.forEach(name => this.cardClasses.add(name)),
            remove: (...names) => names.forEach(name => this.cardClasses.delete(name)),
            contains: name => this.cardClasses.has(name),
        } };
        this.dossierState = { textContent: 'Número de expediente' };
        this.dossierHelpClasses = new Set(['text-amber-900']);
        this.dossierHelp = { classList: {
            add: (...names) => names.forEach(name => this.dossierHelpClasses.add(name)),
            remove: (...names) => names.forEach(name => this.dossierHelpClasses.delete(name)),
            contains: name => this.dossierHelpClasses.has(name),
        } };
        this.createPanel = { hidden: false };
        this.attrs = new Set();
        this.ownerDocument = {
            querySelectorAll: selector => ({
                '[data-expediente-number-output]': [this.dossier, this.banner],
                '[data-dossier-card]': [this.card],
                '[data-dossier-state]': [this.dossierState],
                '[data-dossier-help]': [this.dossierHelp],
                '[data-create-dossier-panel]': [this.createPanel],
            }[selector] || []),
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
    constructor(form) { this.form = form; this.name = 'appraiser_id'; this.attrs = new Set(); }
    hasAttribute(name) { return this.attrs.has(name); }
}
class HTMLTextAreaElement {}

class CreateDossierButton {
    constructor(form) { this.form = form; }
    closest(selector) { return selector === '[data-create-dossier]' ? this : null; }
    hasAttribute() { return false; }
}

function setup() {
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
        CustomEvent: globalThis.CustomEvent,
    };
    const listeners = {};
    globalThis.HTMLFormElement = HTMLFormElement;
    globalThis.HTMLInputElement = HTMLInputElement;
    globalThis.HTMLSelectElement = HTMLSelectElement;
    globalThis.HTMLTextAreaElement = HTMLTextAreaElement;
    globalThis.CSS = { escape: value => value };
    globalThis.document = {
        querySelector: selector => selector === 'meta[name="csrf-token"]' ? { content: 'csrf-token' } : null,
        querySelectorAll: () => [],
        addEventListener: (type, handler) => { listeners[type] = handler; },
    };
    const dispatched = [];
    globalThis.window = { addEventListener: () => {}, dispatchEvent: event => dispatched.push(event) };
    globalThis.CustomEvent = class { constructor(type, params = {}) { this.type = type; this.detail = params.detail || {}; } };
    globalThis.FormData = class {
        constructor(form) {
            this.values = new Map([['_token', 'old'], ['version', form.version.value]]);
        }
        delete(key) { this.values.delete(key); }
        get(key) { return this.values.get(key); }
        set(key, value) { this.values.set(key, value); }
    };
    let timerCallback = null, timerDelay = null;
    globalThis.setTimeout = (callback, delay) => { timerCallback = callback; timerDelay = delay; return 1; };
    globalThis.clearTimeout = () => {};
    installModuleAutosave();
    return {
        listeners,
        runTimer: () => timerCallback(),
        timerDelay: () => timerDelay,
        dispatched,
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

test('appraiser change only saves draft without creating dossier number', async () => {
    const { listeners, runTimer, timerDelay, cleanup } = setup();
    const form = new HTMLFormElement(), select = new HTMLSelectElement(form);
    globalThis.fetch = async () => ({ ok: true, json: async () => ({ ok: true, version: 8 }) });
    listeners.change({ target: select });
    assert.equal(timerDelay(), 800);
    await runTimer();
    assert.equal(form.dossier.value, 'Pendiente de asignar');
    cleanup();
});

test('create dossier button sends explicit flag and updates number', async () => {
    const { listeners, runTimer, timerDelay, dispatched, cleanup } = setup();
    const form = new HTMLFormElement(), button = new CreateDossierButton(form);
    globalThis.fetch = async (url, request) => {
        assert.equal(request.body.get('create_expediente'), '1');
        return { ok: true, json: async () => ({ ok: true, version: 8, expediente_number: '01-2026-09-001' }) };
    };
    listeners.click({ target: button, preventDefault() { this.prevented = true; } });
    assert.equal(timerDelay(), 0);
    await runTimer();
    assert.equal(form.dossier.value, '01-2026-09-001');
    assert.equal(form.dossierState.textContent, 'Expediente creado');
    assert.equal(form.cardClasses.has('bg-emerald-50'), true);
    assert.equal(form.createPanel.hidden, true);
    assert.equal(dispatched.at(-1).type, 'ga:dossier-created');
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


test('save-in-place submit stays in module and posts to autosave endpoint', async () => {
    const { listeners, cleanup } = setup();
    const form = new HTMLFormElement();
    form.attrs.add('data-save-in-place');
    let prevented = false, stopped = false, calls = 0;
    globalThis.fetch = async (url, request) => {
        calls++;
        assert.equal(url, '/avaluos/abc/expediente/autoguardar');
        assert.equal(request.method, 'POST');
        return { ok: true, json: async () => ({ ok: true, version: 8, saved_at: new Date().toISOString() }) };
    };
    listeners.submit({
        target: form,
        preventDefault() { prevented = true; },
        stopImmediatePropagation() { stopped = true; },
    });
    await globalThis.window.gaFlushAutosaves();
    assert.equal(prevented, true);
    assert.equal(stopped, true);
    assert.equal(calls, 1);
    assert.equal(form.version.value, '8');
    assert.match(form.status.textContent, /confirmado/);
    cleanup();
});
test('module autosave reports html server errors without injecting pages', async () => {
    const { listeners, runTimer, cleanup } = setup();
    const form = new HTMLFormElement(), input = new HTMLInputElement(form);
    globalThis.fetch = async () => ({
        ok: false,
        status: 500,
        headers: { get: () => 'text/html; charset=utf-8' },
        text: async () => '<!doctype html><body><header>Menu</header><main>No pudimos completar la solicitud</main></body>',
    });
    listeners.input({ target: input });
    await runTimer();
    assert.match(form.status.textContent, /No pudimos completar la solicitud/);
    assert.equal(form.version.value, '7');
    cleanup();
});
