import test from 'node:test';
import assert from 'node:assert/strict';
import { parseHTML } from 'linkedom';
import { rowFactory } from '../resources/js/comparable-row-growth.js';
import { fillRows } from '../resources/js/comparable-bulk-import.js';

test('batch grows beyond 60 and preserves original ID, coordinates and photo target', () => {
    const { document, Event, CustomEvent } = parseHTML('<html><body></body></html>');
    const previous = { Event: globalThis.Event, CustomEvent: globalThis.CustomEvent };
    Object.assign(globalThis, { Event, CustomEvent });
    const keys = ['id', 'source_url', 'price_amount', 'area_m2', 'latitude', 'status'];
    const form = document.createElement('form');
    form.innerHTML = `<table><tbody><tr><td><label><input type="checkbox" value="0">1</label><button>Fotos</button></td>${keys.map(key => `<td><input name="comparables[0][${key}]" value=""></td>`).join('')}</tr></tbody></table>`;
    form.querySelector('[name$="[id]"]').value = 'original';
    form.querySelector('[name$="[source_url]"]').value = 'https://example.test/original';
    form.querySelector('[name$="[latitude]"]').value = '10.39';
    const factory = rowFactory(form);
    form.addEventListener('comparable-grow', () => {
        const index = form.querySelectorAll('tbody tr').length;
        const tr = factory(index);
        Object.defineProperty(tr, 'sectionRowIndex', { value: index });
        form.querySelector('tbody').append(tr);
    });
    try {
        const data = Array.from({ length: 84 }, (_, i) => ({ source_url: `https://example.test/${i}`, price_amount: '500', area_m2: '40' }));
        const result = fillRows(form, data, '', undefined, { deferDuplicateReview: true });
        assert.equal(result.count, 84);
        assert.equal(result.overflow, 0);
        assert.equal(form.querySelectorAll('tbody tr').length, 85);
        assert.equal(form.querySelector('[name="comparables[0][id]"]').value, 'original');
        assert.equal(form.querySelector('[name="comparables[0][latitude]"]').value, '10.39');
        assert.equal(form.querySelector('[name="comparables[84][latitude]"]').value, '');
        const last = form.querySelectorAll('tbody tr')[84];
        assert.equal(last.querySelector('button').getAttribute('@click'), 'openPhotos(84)');
        assert.equal(new Set([...form.querySelectorAll('[name$="[id]"]')].map(input => input.value)).size, 85);
    } finally { Object.assign(globalThis, previous); }
});
