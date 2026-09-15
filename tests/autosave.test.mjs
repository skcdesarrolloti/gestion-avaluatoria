import { test } from 'node:test';
import assert from 'node:assert/strict';
import { appraisalForm } from '../resources/js/appraisal-form.js';

function setup() {
    globalThis.window = { addEventListener() {}, removeEventListener() {} };
    Object.defineProperty(globalThis, 'navigator', { value: { onLine: true }, configurable: true });
    globalThis.document = { querySelector: () => ({ content: 'csrf' }) };
    const form = appraisalForm();
    form.$el = { dataset: { initial: JSON.stringify({ endpoint: '/borrador', version: 1, fields: { titulo: '' } }) } };
    form.$nextTick = callback => callback();
    form.init();
    return form;
}

test('only acknowledged saves show success and allow leaving', async () => {
    const form = setup();
    form.fields.titulo = 'Casa'; form.changed();
    globalThis.fetch = async (_, request) => {
        assert.equal(JSON.parse(request.body).version, 1);
        assert.equal(request.headers['X-CSRF-Token'], 'csrf');
        return { ok: true, json: async () => ({ ok: true, version: 2, saved_at: new Date().toISOString() }) };
    };
    await form.save();
    assert.equal(form.dirty, false);
    assert.equal(form.version, 2);
    assert.match(form.message, /guardados/);
    form.destroy();
});

test('edits while saving are serialized and remain pending', async () => {
    const form = setup();
    form.fields.titulo = 'Uno'; form.changed();
    let resolve;
    let calls = 0;
    globalThis.fetch = () => { calls++; return new Promise(done => { resolve = done; }); };
    const pending = form.save();
    form.fields.titulo = 'Dos'; form.changed();
    await form.save();
    assert.equal(calls, 1);
    resolve({ ok: true, json: async () => ({ ok: true, version: 2, saved_at: new Date().toISOString() }) });
    await pending;
    assert.equal(form.dirty, true);
    assert.equal(form.fields.titulo, 'Dos');
    assert.equal(form.version, 2);
    form.destroy();
});

test('version conflicts preserve text and block overwrites', async () => {
    const form = setup();
    form.fields.titulo = 'Conservar'; form.changed();
    globalThis.fetch = async () => ({ ok: false, status: 409, json: async () => ({ message: 'Conflicto' }) });
    await form.save();
    assert.equal(form.blocked, true);
    assert.equal(form.dirty, true);
    assert.equal(form.fields.titulo, 'Conservar');
    assert.equal(form.version, 1);
    form.destroy();
});

test('network error keeps changes and beforeunload warning', async () => {
    const form = setup();
    form.fields.titulo = 'Pendiente'; form.changed();
    globalThis.fetch = async () => { throw new TypeError('Failed to fetch'); };
    await form.save();
    assert.equal(form.dirty, true);
    assert.equal(form.saving, false);
    let prevented = false;
    form.beforeLeave({ preventDefault: () => { prevented = true; } });
    assert.equal(prevented, true);
    form.destroy();
});

test('validation allows correction; expired session blocks retry', async () => {
    const form = setup();
    form.fields.titulo = 'Pendiente'; form.changed();
    globalThis.fetch = async () => ({ ok: false, status: 422, json: async () => ({ message: 'Revisar', errors: { titulo: 'Largo' } }) });
    await form.save();
    assert.equal(form.blocked, false);
    assert.equal(form.errors.titulo, 'Largo');
    globalThis.fetch = async () => ({ ok: false, status: 401, json: async () => ({ message: 'Sesión vencida' }) });
    await form.save();
    assert.equal(form.blocked, true);
    form.destroy();
});

test('offline never reports saved and invalid confirmation remains dirty', async () => {
    const form = setup();
    form.fields.titulo = 'Pendiente'; form.changed();
    navigator.onLine = false;
    globalThis.fetch = () => { throw new Error('Must not request'); };
    await form.save();
    assert.match(form.message, /Sin conexión/);
    assert.equal(form.dirty, true);
    navigator.onLine = true;
    globalThis.fetch = async () => ({ ok: true, json: async () => ({ ok: true }) });
    await form.save();
    assert.equal(form.dirty, true);
    assert.equal(form.version, 1);
    form.destroy();
});
