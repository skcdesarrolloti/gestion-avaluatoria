import test from 'node:test';
import assert from 'node:assert/strict';
import { locationPhotos } from '../resources/js/location-photos.js';

function component() {
    const item = locationPhotos();
    item.$refs = { photos: { files: [new Blob(['photo'], { type: 'image/png' })], value: 'pending' } };
    item.$el = { dataset: { uploadUrl: '/avaluos/test/bien-sujeto/fotos' } };
    item.hasFiles = true;
    item.update = function () { this.hasFiles = false; };
    globalThis.document = { querySelector: () => ({ content: 'csrf-test' }) };
    return item;
}

test('foto espera guardado del expediente y confirma solo después del servidor', async () => {
    const item = component(); const order = [];
    globalThis.window = { gaFlushAutosaves: async () => { order.push('draft'); return true; } };
    globalThis.fetch = async (_url, options) => {
        order.push('upload');
        assert.equal(options.body.get('_token'), 'csrf-test');
        assert.equal(options.body.get('photo_caption'), 'location:chapter-one');
        return { ok: true, json: async () => ({ ok: true, message: 'Guardada', photos: [{ id: 'p' }] }) };
    };
    await item.save();
    assert.deepEqual(order, ['draft', 'upload']);
    assert.equal(item.saved[0].id, 'p'); assert.equal(item.hasFiles, false);
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
