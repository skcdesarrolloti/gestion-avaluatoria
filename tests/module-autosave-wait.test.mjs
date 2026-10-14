import { test } from 'node:test';
import assert from 'node:assert/strict';
import { flushModuleForm } from '../resources/js/module-autosave.js';
import { HTMLFormElement, HTMLInputElement, HTMLSelectElement, CreateDossierButton, setup } from './module-autosave-support.mjs';

test('batch waits for server acknowledgment and rejects a missing version', async () => {
    const { listeners, cleanup } = setup();
    const form = new HTMLFormElement();
    let acknowledge;
    globalThis.fetch = () => new Promise(resolve => { acknowledge = resolve; });
    listeners.input({ target: form });
    let completed = false;
    const pending = flushModuleForm(form).then(saved => { completed = true; return saved; });
    await Promise.resolve();
    assert.equal(completed, false);
    assert.equal(form.status.dataset.autosaveTone, 'saving');
    acknowledge({ ok: true, json: async () => ({ ok: true, version: 8 }) });
    assert.equal(await pending, true);
    listeners.input({ target: form });
    globalThis.fetch = async () => ({ ok: true, json: async () => ({ ok: true }) });
    assert.equal(await flushModuleForm(form), false);
    assert.equal(form.version.value, '8');
    assert.equal(form.status.dataset.autosaveTone, 'error');
    globalThis.fetch = async () => ({ ok: true, json: async () => ({ ok: true, version: 9 }) });
    assert.equal(await flushModuleForm(form), true);
    assert.equal(form.version.value, '9');
    cleanup();
});

test('navigation stops waiting on a stalled response without resending or discarding changes', async () => {
    const { listeners, runTimer, cleanup, timerDelay } = setup();
    const form = new HTMLFormElement(), input = new HTMLInputElement(form);
    let calls = 0, signal;
    globalThis.fetch = async (_url, request) => {
        calls++; signal = request.signal;
        return { ok: true, json: () => new Promise(() => {}) };
    };
    try {
        listeners.input({ target: input });
        const pending = flushModuleForm(form);
        await Promise.resolve();
        assert.equal(timerDelay(), 20000);
        runTimer();
        assert.equal(await pending, false);
        assert.equal(signal.aborted, true);
        assert.equal(calls, 1);
        assert.equal(form.version.value, '7');
        assert.equal(input.value, 'Casa');
        assert.match(form.status.textContent, /20 segundos/);
        assert.equal(form.status.dataset.autosaveTone, 'error');
        globalThis.fetch = async () => { calls++; return { ok: true, json: async () => ({ ok: true, version: 8 }) }; };
        assert.equal(await flushModuleForm(form), true);
        assert.equal(calls, 2);
    } finally { form.isConnected = false; cleanup(); }
});

test('navigation makes one attempt on a failed save, including edits during the request', async () => {
    const { listeners, cleanup } = setup();
    const form = new HTMLFormElement();
    let fail, calls = 0;
    globalThis.fetch = () => { calls++; return new Promise((_resolve, reject) => { fail = reject; }); };
    try {
        listeners.input({ target: form });
        const pending = flushModuleForm(form);
        listeners.input({ target: form });
        fail(new TypeError('Failed to fetch'));
        assert.equal(await pending, false);
        assert.equal(calls, 1);
        assert.equal(form.version.value, '7');
        assert.equal(form.status.dataset.autosaveTone, 'error');
    } finally { form.isConnected = false; cleanup(); }
});

test('offline batch stays pending and reconnect retries without overwriting a conflict', async () => {
    const { listeners, cleanup } = setup();
    const priorNavigator = Object.getOwnPropertyDescriptor(globalThis, 'navigator');
    Object.defineProperty(globalThis, 'navigator', { configurable: true, value: { onLine: false } });
    const form = new HTMLFormElement();
    let calls = 0;
    globalThis.fetch = async () => { calls++; return { ok: true, json: async () => ({ ok: true, version: 8 }) }; };
    listeners.input({ target: form });
    assert.equal(await flushModuleForm(form), false);
    assert.equal(calls, 0);
    assert.equal(form.status.dataset.autosaveTone, 'offline');
    navigator.onLine = true;
    assert.equal(await flushModuleForm(form), true);
    assert.equal(calls, 1);
    listeners.input({ target: form });
    globalThis.fetch = async () => { calls++; return { ok: false, status: 409, json: async () => ({ message: 'Conflicto' }) }; };
    assert.equal(await flushModuleForm(form), false);
    assert.equal(await flushModuleForm(form), false);
    assert.equal(calls, 2);
    assert.equal(form.version.value, '8');
    if (priorNavigator) Object.defineProperty(globalThis, 'navigator', priorNavigator);
    else delete globalThis.navigator;
    cleanup();
});
