import test from 'node:test';
import assert from 'node:assert/strict';
import { sectorPhotoUpload } from '../resources/js/sector-photo-upload.js';

test('seleccionar una imagen sectorial inicia la carga sin otro clic', () => {
    const item = sectorPhotoUpload(); let saves = 0;
    item.$nextTick = fn => fn(); item.save = () => saves++;
    item.update({ files: [new Blob(['photo'], { type: 'image/png' })] });
    assert.equal(saves, 1); assert.equal(item.hasFiles, true);
    item.previews.forEach(p => URL.revokeObjectURL(p.url));
});
test('un conflicto del texto conserva la imagen pendiente y permite reintentar', async () => {
    globalThis.window = { gaFlushAutosaves: async () => false };
    const item = sectorPhotoUpload(); item.hasFiles = true;
    await item.save();
    assert.equal(item.busy, false); assert.equal(item.hasFiles, true);
    assert.match(item.status, /guardado pendiente/);
    item.busy = true; item.failed(); assert.equal(item.busy, false);
});
test('una imagen pendiente bloquea navegación y libera manejadores al cerrar', () => {
    const listeners = new Map();
    globalThis.document = { addEventListener: (k, fn) => listeners.set(k, fn), removeEventListener: k => listeners.delete(k) };
    globalThis.window = { addEventListener() {}, removeEventListener() {} };
    const item = sectorPhotoUpload(); item.$root = { contains: () => false }; item.init(); item.hasFiles = true;
    let prevented = false;
    listeners.get('click')({ type: 'click', target: { closest: () => true }, preventDefault() { prevented = true; }, stopImmediatePropagation() {} });
    assert.equal(prevented, true); assert.match(item.status, /pendiente/);
    item.destroy(); assert.equal(listeners.size, 0);
});
