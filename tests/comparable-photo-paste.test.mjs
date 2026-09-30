import test from 'node:test';
import assert from 'node:assert/strict';
import { pastedPhoto } from '../resources/js/comparable-photo-paste.js';
import { comparablePhotos } from '../resources/js/comparable-photos.js';

const clipboard = file => ({ items: [{ kind: 'file', getAsFile: () => file }] });
test('clipboard image gets a filename matching its actual MIME, without using HTML', () => {
    const file = pastedPhoto(clipboard(new File(['image bytes'], 'clipboard', { type: 'image/png' })));
    assert.match(file.name, /\.png$/); assert.equal(file.type, 'image/png');
    assert.throws(() => pastedPhoto({ items: [{ kind: 'string', type: 'text/html' }] }), /Copiar imagen/);
    assert.throws(() => pastedPhoto(clipboard(new File(['svg'], 'x.svg', { type: 'image/svg+xml' }))), /máximo 5 MB/);
});
test('oversized pasted image is rejected before uploading', () => {
    assert.throws(() => pastedPhoto(clipboard(new File([new Uint8Array(5 * 1024 * 1024 + 1)], 'x.png', { type: 'image/png' }))), /máximo 5 MB/);
});
test('paste uploads multipart to the selected sample and retry keeps the photo until acknowledged', async () => {
    const state = comparablePhotos();
    state.initPhotos({ querySelector: () => ({ value: 'csrf-test' }) });
    state.photoReady = true; state.photoEndpoint = '/comparables/sample-a/fotos';
    state.$refs = { photoFile: { value: '', files: [] } };
    let calls = 0;
    state.photoRequest = async options => {
        assert.equal(state.photoEndpoint, '/comparables/sample-a/fotos');
        assert.equal(options.body.get('_token'), 'csrf-test');
        assert.equal(options.body.get('photo').type, 'image/png');
        if (++calls === 1) throw new Error('Sin conexión');
        return { photos: [{ id: 'saved' }] };
    };
    await state.pastePhoto({ clipboardData: clipboard(new File(['bytes'], 'x.png', { type: 'image/png' })) });
    assert.equal(state.photoRetry, true); assert.deepEqual(state.photos, []);
    await state.retryPhoto();
    assert.equal(state.photoRetry, false); assert.equal(state.photos[0].id, 'saved');
    assert.match(state.photoMessage, /guardada/);
});
