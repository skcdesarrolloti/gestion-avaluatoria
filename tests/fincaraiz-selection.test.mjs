import test from 'node:test';
import assert from 'node:assert/strict';
import { fincaraizAreaSearch } from '../resources/js/fincaraiz-area-search.js';

test('searching and selecting suggested listings never write matrix fields or trigger autosave', async () => {
    const source = 'https://example.com/old';
    const input = { name: 'comparables[0][source_url]', value: source };
    const tr = { querySelectorAll: () => [input] };
    let events = 0;
    const form = {
        querySelectorAll: () => [tr],
        querySelector: () => ({ value: 'test-token' }),
        dispatchEvent: () => { events++; },
    };
    const component = fincaraizAreaSearch();
    component.$el = { closest: () => form, dataset: {
        neighborhoodId: '1', neighborhoods: JSON.stringify([{ id: '1', name: 'Bocagrande' }]), endpoint: '/test-search', portal: 'metrocuadrado',
    } };
    const originalFetch = globalThis.fetch;
    globalThis.fetch = async (url, options) => {
        assert.equal(options.body.get('portal'), 'metrocuadrado');
        return { ok: true, headers: new Headers({ 'content-type': 'application/json' }), json: async () => ({
        ok: true, page: 1, has_next: false, url: 'https://example.com/search',
        results: [{ title: 'Nueva oficina', row: { source_url: 'https://example.com/new', price_amount: '450000000', area_m2: '46' } }],
    }) }; };
    try {
        component.init();
        await component.search();
        assert.equal(component.results.length, 1);
        assert.deepEqual(component.selected, []);
        component.selectSuggested();
        assert.deepEqual(component.selected, ['https://example.com/new']);
        assert.equal(input.value, source);
        assert.equal(events, 0);
    } finally { globalThis.fetch = originalFetch; }
});
