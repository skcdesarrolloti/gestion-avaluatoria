import test from 'node:test';
import assert from 'node:assert/strict';
import { locationPhotos } from '../resources/js/location-photos.js';

function component() {
    const item = locationPhotos();
    item.$refs = { photos: { files: [new Blob(['photo'], { type: 'image/png' })], value: 'pending' } };
    item.$root = { dataset: { uploadUrl: '/avaluos/test/bien-sujeto/fotos' }, isConnected: true, scrollIntoView() {} };
    // Real Alpine evaluates @click on the button: $el has no upload URL.
    item.$el = { dataset: {} };
    item.hasFiles = true;
    item.update = function () { this.hasFiles = false; };
    globalThis.document = { querySelector: () => ({ content: 'csrf-test' }), addEventListener() {} };
    globalThis.window = { addEventListener() {} };
    item.init();
    return item;
}

test('foto espera guardado del expediente y confirma solo después del servidor', async () => {
    const item = component(); const order = [];
    globalThis.window = { gaFlushAutosaves: async () => { order.push('draft'); return true; } };
    globalThis.fetch = async (url, options) => {
        assert.equal(url, '/avaluos/test/bien-sujeto/fotos');
        order.push('upload');
        assert.equal(options.body.get('_token'), 'csrf-test');
        assert.equal(options.body.get('photo_caption'), 'location:chapter-one');
        return { ok: true, json: async () => ({ ok: true, message: 'Guardada', photos: [{ id: 'p', url: '/fotos/p' }] }) };
    };
    await item.save();
    assert.deepEqual(order, ['draft', 'upload']);
    assert.equal(item.saved[0].id, 'p'); assert.equal(item.hasFiles, false);
});

test('no confirma éxito sin una nueva imagen en la galería del apartado', async () => {
    const item = component();
    globalThis.fetch = async () => ({ ok: true, json: async () => ({ ok: true, photos: [] }) });
    await item.save();
    assert.match(item.status, /no devolvió la imagen/);
    assert.equal(item.hasFiles, true);
});

test('sesión vencida conserva la imagen y pide reingreso sin recargar', async () => {
    const item = component();
    globalThis.fetch = async () => ({ ok: false, status: 401 });
    await item.save();
    assert.match(item.status, /sesión venció/);
    assert.equal(item.hasFiles, true);
});

test('renueva CSRF caducado una vez y conserva el archivo durante el reintento', async () => {
    const item = component(), token = { content: 'old' }, calls = [];
    globalThis.window.location = { href: 'https://example.test/expediente' };
    globalThis.document.querySelector = () => token;
    globalThis.document.querySelectorAll = () => [];
    globalThis.DOMParser = class { parseFromString() { return { querySelector: () => ({ content: 'new' }) }; } };
    globalThis.fetch = async (url, options) => {
        calls.push(options.method || 'GET');
        if (calls.length === 1) return { ok: false, status: 419 };
        if (calls.length === 2) return { ok: true, url, text: async () => '<meta>' };
        assert.equal(options.body.get('_token'), 'new');
        assert.equal(options.body.getAll('photos[]').length, 1);
        return { ok: true, json: async () => ({ ok: true, photos: [{ id: 'p', url: '/fotos/p' }] }) };
    };
    await item.save();
    assert.deepEqual(calls, ['POST', 'GET', 'POST']);
    assert.equal(item.saved.length, 1);
    assert.equal(item.hasFiles, false);
});

test('una imagen pendiente impide navegar pero una confirmada permite continuar', () => {
    const item = component();
    globalThis.window.location = { href: 'https://example.test/expediente' };
    let blocked = 0;
    const event = { type: 'click', target: { closest: () => ({ href: 'https://example.test/bien-sujeto' }) },
        preventDefault() { blocked++; }, stopImmediatePropagation() {} };
    item.guardLeave(event);
    assert.equal(blocked, 1);
    item.hasFiles = false;
    item.guardLeave(event);
    assert.equal(blocked, 1);
});
test('conflicto de borrador evita cargar y conserva archivo para reintento', async () => {
    const item = component();
    globalThis.window = { gaFlushAutosaves: async () => false };
    globalThis.fetch = async () => { throw new Error('No debe enviar'); };
    await item.save();
    assert.match(item.status, /resuelve el guardado pendiente/);
    assert.equal(item.hasFiles, true); assert.equal(item.busy, false);
});
test('error de red nunca anuncia foto guardada ni pierde la selección', async () => {
    const item = component();
    globalThis.window = { gaFlushAutosaves: async () => true };
    globalThis.fetch = async () => { throw new Error('Sin conexión'); };
    await item.save();
    assert.match(item.status, /No se confirmó/);
    assert.equal(item.saved.length, 0); assert.equal(item.hasFiles, true);
});
