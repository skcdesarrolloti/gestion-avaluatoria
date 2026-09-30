import test from 'node:test';
import assert from 'node:assert/strict';
import { comparableRemoval } from '../resources/js/comparable-removal.js';

function setup(flush = async () => true) {
    const entries = [0, 1].map(index => ({ index, used: true, opened: true, tr: { hidden: index === 1 },
        controls: Object.entries({ id: String(index + 1).repeat(32), source_url: `https://example.com/${index}`, operation: 'Venta', ph_regime: 'si' })
            .map(([key, value]) => ({ name: `comparables[${index}][${key}]`, value })) }));
    const component = comparableRemoval(flush);
    const events = [];
    component.initRemoval({ dispatchEvent: event => events.push(event.type) }, entries);
    return { component, entries, events };
}
test('removal waits for confirmation, clears only selected rows, rotates identity and restores original IDs', async () => {
    const { component, entries, events } = setup();
    component.selectRemovalPage(); component.requestRemoval();
    assert.equal(entries[0].controls[1].value, 'https://example.com/0');
    await component.confirmRemoval();
    assert.equal(entries[0].controls[1].value, '');
    assert.notEqual(entries[0].controls[0].value, '1'.repeat(32));
    assert.equal(entries[1].controls[1].value, 'https://example.com/1');
    assert.equal(component.undoCount, 1);
    assert.ok(events.includes('comparable-matrix-changed'));
    await component.undoRemoval();
    assert.equal(entries[0].controls[0].value, '1'.repeat(32));
    assert.equal(entries[0].controls[1].value, 'https://example.com/0');
});
test('reset includes hidden rows and never claims success when persistence fails', async () => {
    let calls = 0;
    const { component, entries } = setup(async () => ++calls === 1);
    component.requestRemoval(true); await component.confirmRemoval();
    assert.ok(entries.every(entry => entry.controls[1].value === ''));
    assert.match(component.removalMessage, /SIN confirmar guardado/);
    assert.equal(component.undoCount, 2);
});
test('conflict before deletion preserves every row', async () => {
    const { component, entries, events } = setup(async () => false);
    component.requestRemoval(true); await component.confirmRemoval();
    assert.equal(entries[0].controls[1].value, 'https://example.com/0');
    assert.equal(entries[1].controls[1].value, 'https://example.com/1');
    assert.equal(events.length, 0);
});
