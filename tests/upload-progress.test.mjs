import { test } from 'node:test';
import assert from 'node:assert/strict';
import { installUploadProgress, uploadErrorMessage, uploadRedirectUrl } from '../resources/js/upload-progress.js';

const current = 'https://example.test/public/avaluos/abc/bien-sujeto#ph';
const action = 'https://example.test/public/avaluos/abc/bien-sujeto/ph/soportes';

test('adds ph anchor after upload redirects without fragment', () => {
    assert.equal(uploadRedirectUrl('https://example.test/public/avaluos/abc/bien-sujeto', action, current),
        'https://example.test/public/avaluos/abc/bien-sujeto#ph');
});

test('shows specific message for rejected large uploads', () => {
    assert.match(uploadErrorMessage({ status: 413, responseText: '' }), /tamaño/);
    assert.equal(uploadErrorMessage({ status: 422, responseText: '{"message":"RAR no disponible"}' }), 'RAR no disponible');
});

test('submits marked upload form through xhr progress', () => {
    const originals = {
        document: globalThis.document, window: globalThis.window, history: globalThis.history,
        FormData: globalThis.FormData, HTMLFormElement: globalThis.HTMLFormElement,
        XMLHttpRequest: globalThis.XMLHttpRequest, DOMParser: globalThis.DOMParser,
    };
    const listeners = {};
    const events = [];
    class Form {}
    class Body { constructor(form, submitter) { this.form = form; this.submitter = submitter; } set(key, value) { this[key] = value; } }
    class Xhr {
        constructor() { this.upload = {}; Xhr.last = this; }
        open(method, url) { this.method = method; this.url = url; }
        setRequestHeader(key, value) { this[key] = value; }
        getResponseHeader() { return 'text/html; charset=utf-8'; }
        send(body) { this.body = body; }
    }
    const panel = { classList: { remove: value => events.push(['show', value]) } };
    const bar = { style: {}, setAttribute: (key, value) => { bar[key] = value; } };
    const text = {};
    const button = { disabled: false };
    const form = Object.assign(new Form(), {
        action, method: 'post',
        matches: selector => selector === '[data-upload-progress]',
        querySelector: selector => ({ '[data-upload-progress-panel]': panel, '[data-upload-progress-bar]': bar, '[data-upload-progress-text]': text }[selector] ?? null),
        querySelectorAll: selector => selector.includes('submit') ? [button] : [],
    });
    globalThis.HTMLFormElement = Form;
    globalThis.FormData = Body;
    globalThis.XMLHttpRequest = Xhr;
    globalThis.window = { location: { href: current, assign: url => events.push(['assign', url]) } };
    globalThis.history = { replaceState: (_state, _title, url) => events.push(['history', url]) };
    globalThis.DOMParser = class { parseFromString() { return { body: { marker: 'new' }, title: 'PH', querySelector: () => null }; } };
    globalThis.document = {
        body: { replaceWith: body => events.push(['body', body.marker]) },
        addEventListener: (type, handler) => { listeners[type] = handler; },
        querySelector: () => ({ content: 'fresh-token' }),
        open: () => events.push(['open']),
        write: html => events.push(['write', html]),
        close: () => events.push(['close']),
    };
    installUploadProgress();
    let prevented = false;
    listeners.submit({ defaultPrevented: false, target: form, submitter: button, preventDefault: () => { prevented = true; } });
    Xhr.last.upload.onprogress({ lengthComputable: true, loaded: 40, total: 100 });
    Object.assign(Xhr.last, { status: 200, responseURL: 'https://example.test/public/avaluos/abc/bien-sujeto', responseText: '<html>ok</html>' });
    Xhr.last.onload();
    assert.deepEqual(events.find(event => event[0] === 'body'), ['body', 'new']);
    assert.equal(events.some(event => event[0] === 'write'), false);
    assert.equal(prevented, true);
    assert.equal(bar.style.width, '100%');
    assert.equal(button.disabled, true);
    assert.deepEqual(events.filter(event => event[0] === 'history')[0], ['history', 'https://example.test/public/avaluos/abc/bien-sujeto#ph']);
    Object.assign(globalThis, originals);
});
