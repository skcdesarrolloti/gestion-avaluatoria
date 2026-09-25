import { test } from 'node:test';
import assert from 'node:assert/strict';
import { installFetchNavigation, isFetchableUrl, isLoginRedirect, redirectedUrl, shouldHandleLink, submitAction, submitMethod, syncFormToken } from '../resources/js/fetch-navigation.js';

const current = 'https://example.test/public/avaluos?page=1';

function link(overrides = {}) {
    return {
        href: 'https://example.test/public/normas-tecnicas-sectoriales',
        target: '',
        hasAttribute: () => false,
        closest: () => null,
        ...overrides,
    };
}

function click(overrides = {}) {
    return {
        altKey: false,
        button: 0,
        ctrlKey: false,
        defaultPrevented: false,
        metaKey: false,
        shiftKey: false,
        ...overrides,
    };
}

test('accepts same-origin html navigation', () => {
    assert.equal(isFetchableUrl('/public/normas-tecnicas-sectoriales', current), true);
    assert.equal(shouldHandleLink(link(), click(), current), true);
});

test('keeps external, modified and native links out of fetch navigation', () => {
    assert.equal(isFetchableUrl('https://otro.test/public', current), false);
    assert.equal(shouldHandleLink(link({ target: '_blank' }), click(), current), false);
    assert.equal(shouldHandleLink(link(), click({ ctrlKey: true }), current), false);
    assert.equal(shouldHandleLink(link({ hasAttribute: name => name === 'download' }), click(), current), false);
});

test('lets same-page anchors scroll normally', () => {
    const anchor = 'https://example.test/public/avaluos?page=1#nuevo-avaluo';
    assert.equal(isFetchableUrl(anchor, current), false);
});

test('keeps return_to hash after post redirects', () => {
    const body = new FormData();
    body.set('return_to', '/public/avaluos/abc/bien-sujeto#atributos');
    assert.equal(
        redirectedUrl('https://example.test/public/avaluos/abc/bien-sujeto', current, body, current),
        'https://example.test/public/avaluos/abc/bien-sujeto#atributos',
    );
});

test('keeps app-route return_to hash after post redirects', () => {
    const body = new FormData();
    body.set('return_to', 'avaluos/abc/bien-sujeto#fotos-general');
    assert.equal(
        redirectedUrl('https://example.test/public/avaluos/abc/bien-sujeto', current, body, current),
        'https://example.test/public/avaluos/abc/bien-sujeto#fotos-general',
    );
});

test('keeps active sector hash after post redirects', () => {
    const body = new FormData();
    body.set('active_sector', 'banco-02');
    assert.equal(
        redirectedUrl('https://example.test/public/avaluos/abc/sector', current, body, current),
        'https://example.test/public/avaluos/abc/sector#banco-02',
    );
});

test('prefers target sector over current sector after save', () => {
    const body = new FormData();
    body.set('active_sector', 'banco-01');
    body.set('target_sector', 'banco-02');
    assert.equal(
        redirectedUrl('https://example.test/public/avaluos/abc/sector', current, body, current),
        'https://example.test/public/avaluos/abc/sector#banco-02',
    );
});

test('normalizes one digit sector anchors after save', () => {
    const body = new FormData();
    body.set('active_sector', 'banco-4');
    assert.equal(
        redirectedUrl('https://example.test/public/avaluos/abc/sector', current, body, current),
        'https://example.test/public/avaluos/abc/sector#banco-04',
    );
});



test('uses submitter formaction and formmethod for special form buttons', () => {
    const form = {
        action: 'https://example.test/public/avaluos/abc/normatividad-urbana',
        method: 'post',
        getAttribute: name => ({ action: '/public/avaluos/abc/normatividad-urbana', method: 'post' })[name] ?? null,
    };
    const submitter = {
        getAttribute: name => ({ formaction: '/public/avaluos/abc/normatividad-urbana/midas/consultar', formmethod: 'post' })[name] ?? null,
    };
    assert.equal(submitAction(form, submitter, current), 'https://example.test/public/avaluos/abc/normatividad-urbana/midas/consultar');
    assert.equal(submitMethod(form, submitter), 'POST');
});

test('syncs hidden csrf field before form post', () => {
    const body = new FormData();
    body.set('_token', 'old');
    syncFormToken(body, 'fresh');
    assert.equal(body.get('_token'), 'fresh');
});

test('detects protected action redirected to login', () => {
    assert.equal(isLoginRedirect('https://example.test/public/login', current), true);
    assert.equal(isLoginRedirect('https://other.test/public/login', current), false);
    assert.equal(isLoginRedirect('https://example.test/public/avaluos', current), false);
});

test('waits for pending module autosave before link navigation', async () => {
    const originals = { document: globalThis.document, window: globalThis.window, fetch: globalThis.fetch };
    const events = [];
    const listeners = {};
    globalThis.document = {
        addEventListener: (type, handler) => { listeners[type] = handler; },
        body: { setAttribute() {} },
        documentElement: { dataset: {} },
        getElementById: () => null,
        querySelector: () => ({ content: 'csrf-token' }),
    };
    globalThis.window = {
        location: { href: current, assign: () => events.push('assign') },
        addEventListener() {},
        gaFlushAutosaves: async () => { events.push('flush'); return true; },
    };
    globalThis.fetch = async () => {
        events.push('fetch');
        return { headers: { get: () => 'application/octet-stream' }, url: '' };
    };
    installFetchNavigation();
    await listeners.click({
        altKey: false, button: 0, ctrlKey: false, defaultPrevented: false, metaKey: false, shiftKey: false,
        preventDefault() {},
        target: { closest: () => link({ href: 'https://example.test/public/avaluos/abc/bien-sujeto' }) },
    });
    assert.deepEqual(events.slice(0, 2), ['flush', 'fetch']);
    Object.assign(globalThis, originals);
});

test('leaves multipart uploads to native browser submit', () => {
    const originals = { document: globalThis.document, window: globalThis.window, HTMLFormElement: globalThis.HTMLFormElement };
    const listeners = {};
    globalThis.HTMLFormElement = class {};
    const form = new globalThis.HTMLFormElement();
    form.enctype = 'multipart/form-data';
    form.closest = () => null;
    globalThis.document = { addEventListener: (type, handler) => { listeners[type] = handler; } };
    globalThis.window = { location: { href: current }, addEventListener() {} };
    installFetchNavigation();
    let prevented = false;
    listeners.submit({
        defaultPrevented: false,
        preventDefault: () => { prevented = true; },
        target: form,
    });
    assert.equal(prevented, false);
    Object.assign(globalThis, originals);
});

test('leaves marked forms to native browser submit', () => {
    const originals = { document: globalThis.document, window: globalThis.window, HTMLFormElement: globalThis.HTMLFormElement };
    const listeners = {};
    globalThis.HTMLFormElement = class {};
    const form = new globalThis.HTMLFormElement();
    form.enctype = '';
    form.closest = selector => selector === '[data-no-fetch]' ? form : null;
    globalThis.document = { addEventListener: (type, handler) => { listeners[type] = handler; } };
    globalThis.window = { location: { href: current }, addEventListener() {} };
    installFetchNavigation();
    let prevented = false;
    listeners.submit({ defaultPrevented: false, preventDefault: () => { prevented = true; }, target: form });
    assert.equal(prevented, false);
    Object.assign(globalThis, originals);
});


test('implicit enter submit in module autosave forms does not navigate', () => {
    const originals = { document: globalThis.document, window: globalThis.window, HTMLFormElement: globalThis.HTMLFormElement, FormData: globalThis.FormData };
    const listeners = {};
    globalThis.HTMLFormElement = class {};
    const form = new globalThis.HTMLFormElement();
    form.enctype = '';
    form.target = '';
    form.method = 'POST';
    form.action = 'https://example.test/public/avaluos/abc/expediente';
    form.closest = () => null;
    form.matches = selector => selector === '[data-module-autosave]';
    globalThis.document = { addEventListener: (type, handler) => { listeners[type] = handler; } };
    globalThis.window = { location: { href: current }, addEventListener() {} };
    globalThis.FormData = class {};
    installFetchNavigation();
    let prevented = false;
    listeners.submit({ defaultPrevented: false, submitter: null, preventDefault: () => { prevented = true; }, target: form });
    assert.equal(prevented, true);
    Object.assign(globalThis, originals);
});
