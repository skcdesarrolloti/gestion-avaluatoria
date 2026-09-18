import { test } from 'node:test';
import assert from 'node:assert/strict';
import { installModuleAutosave } from '../resources/js/module-autosave.js';

class HTMLFormElement {
    constructor() {
        this.id = 'expediente-form';
        this.dataset = { autosaveEndpoint: '/avaluos/abc/expediente/autoguardar' };
        this.version = { name: 'version', value: '7' };
        this.status = { textContent: '', dataset: {} };
    }
    matches(selector) { return selector === '[data-module-autosave]'; }
    querySelector(selector) { return selector === 'input[name="version"]' ? this.version : null; }
    querySelectorAll(selector) { return selector === '[data-autosave-status]' ? [this.status] : []; }
}

class HTMLInputElement {
    constructor(form) { this.form = form; this.type = 'text'; this.name = 'titulo'; this.value = 'Casa'; }
}

class HTMLSelectElement {}
class HTMLTextAreaElement {}

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
    globalThis.window = { addEventListener: () => {} };
    globalThis.FormData = class {
        constructor(form) {
            this.values = new Map([['_token', 'old'], ['version', form.version.value]]);
        }
        delete(key) { this.values.delete(key); }
        get(key) { return this.values.get(key); }
    };
    let timerCallback = null;
    globalThis.setTimeout = callback => { timerCallback = callback; return 1; };
    globalThis.clearTimeout = () => {};
    installModuleAutosave();
    return {
        listeners,
        runTimer: () => timerCallback(),
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
        return { ok: true, json: async () => ({ ok: true, version: 8, saved_at: new Date().toISOString() }) };
    };
    listeners.input({ target: input });
    assert.equal(form.status.textContent, 'Cambios pendientes');
    await runTimer();
    assert.equal(form.version.value, '8');
    assert.match(form.status.textContent, /Autoguardado confirmado/);
    cleanup();
});
