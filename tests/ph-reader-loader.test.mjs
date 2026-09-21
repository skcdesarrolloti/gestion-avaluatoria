import { test } from 'node:test';
import assert from 'node:assert/strict';
import { phReaderUrl, loadPhPdfReader } from '../resources/js/ph-reader-loader.js';

test('PH reader version changes its cache key while preserving the hosting subdirectory', () => {
    const base = 'https://example.test/public/assets/app.js?v=app-version';
    assert.equal(phReaderUrl(base, 'reader-a'), 'https://example.test/public/assets/ph-pdf-reader.js?v=reader-a');
    assert.notEqual(phReaderUrl(base, 'reader-a'), phReaderUrl(base, 'reader-b'));
    assert.equal(phReaderUrl('https://example.test/assets/app.js', 'reader-a'),
        'https://example.test/assets/ph-pdf-reader.js?v=reader-a');
});

test('PH ignores the old cached image reader and calls the matching text reader', async () => {
    const base = 'https://example.test/public/assets/app.js?v=new-app';
    const cache = new Map([
        ['https://example.test/public/assets/ph-pdf-reader.js', { preparePhPdfImages: async () => [] }],
    ]);
    const calls = [];
    const reader = await loadPhPdfReader(base, 'new-reader', async url => {
        calls.push(url);
        return cache.get(url) ?? { preparePhPdfText: async files => ({ pages: 244, file: files[0] }) };
    });
    assert.deepEqual(await reader(['reglamento.pdf']), { pages: 244, file: 'reglamento.pdf' });
    assert.equal(calls[0], phReaderUrl(base, 'new-reader'));
});

test('PH reports an incomplete deployment without calling an undefined minified function', async () => {
    await assert.rejects(loadPhPdfReader('https://example.test/assets/app.js', 'v2',
        async () => ({ preparePhPdfImages: () => [] })), /lector PDF publicado está desactualizado/);
});
