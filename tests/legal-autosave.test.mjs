import { test } from 'node:test';
import assert from 'node:assert/strict';
import { legalAutosave } from '../resources/js/legal-autosave.js';

function setup() {
    globalThis.window = { addEventListener() {}, removeEventListener() {} };
    globalThis.document = { querySelector: () => ({ content: 'csrf-token' }) };
    globalThis.FormData = class {
        constructor(form) { this.form = form; }
    };
    const form = legalAutosave();
    form.$el = { dataset: { autosaveEndpoint: '/legal/autoguardar' } };
    form.init();
    return form;
}

test('legal autosave posts fields and confirms only server acknowledgement', async () => {
    const form = setup();
    form.changed();
    assert.equal(form.dirty, true);
    globalThis.fetch = async (url, request) => {
        assert.equal(url, '/legal/autoguardar');
        assert.equal(request.method, 'POST');
        assert.equal(request.headers['X-CSRF-Token'], 'csrf-token');
        assert.equal(request.credentials, 'same-origin');
        return { ok: true, json: async () => ({ ok: true, saved_at: new Date().toISOString() }) };
    };
    await form.save();
    assert.equal(form.dirty, false);
    assert.match(form.message, /guardados/);
    form.cancel();
});

test('legal autosave keeps dirty state when server rejects save', async () => {
    const form = setup();
    form.changed();
    globalThis.fetch = async () => ({ ok: false, json: async () => ({ message: 'Error' }) });
    await form.save();
    assert.equal(form.dirty, true);
    assert.match(form.message, /Pendiente/);
    form.cancel();
});
