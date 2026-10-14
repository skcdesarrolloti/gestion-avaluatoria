import { test } from 'node:test';
import assert from 'node:assert/strict';
import { flushModuleForm } from '../resources/js/module-autosave.js';
import { HTMLFormElement, HTMLInputElement, HTMLSelectElement, CreateDossierButton, setup } from './module-autosave-support.mjs';

test('module autosave posts form data and updates optimistic version', async () => {
    const { listeners, runTimer, dispatched, cleanup } = setup();
    const form = new HTMLFormElement();
    form.dataset.autosaveTopic = 'appraisal:abc:chapter-zero';
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
    assert.equal(dispatched.at(-1).type, 'ga:record-saved');
    assert.equal(dispatched.at(-1).detail.topic, 'appraisal:abc:chapter-zero');
    cleanup();
});
test('autosave distinguishes preparation failure from a network failure and keeps unsaved changes pending',async()=>{
    const {listeners,runTimer,cleanup}=setup(),form=new HTMLFormElement();let calls=0;
    globalThis.fetch=async()=>{calls++;throw new TypeError('network');};
    listeners.input({target:new HTMLInputElement(form)});await runTimer();
    assert.match(form.status.textContent,/no se pudo conectar/);assert.equal(form.version.value,'7');
    globalThis.FormData=class {constructor(){throw new TypeError('preparation');}};
    listeners.input({target:new HTMLInputElement(form)});await runTimer();
    assert.match(form.status.textContent,/preparar o procesar/);assert.equal(calls,1);assert.equal(form.version.value,'7');form.isConnected=false;cleanup();
});
test('autosave does not report lost connection when server confirmation precedes a screen update failure',async()=>{
    const {listeners,runTimer,cleanup}=setup(),form=new HTMLFormElement(),query=form.querySelectorAll.bind(form);
    form.querySelectorAll=selector=>{if(selector==='[data-object-preview]')throw new TypeError('screen update');return query(selector);};
    globalThis.fetch=async()=>({ok:true,json:async()=>({ok:true,version:8,object_text:'Confirmado'})});
    listeners.input({target:new HTMLInputElement(form)});await runTimer();
    assert.equal(form.version.value,'8');assert.match(form.status.textContent,/servidor confirmó/);assert.doesNotMatch(form.status.textContent,/no se pudo conectar/);form.isConnected=false;cleanup();
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
