import test from 'node:test';
import assert from 'node:assert/strict';
import { parseCiencuadrasCards, ciencuadrasUrl, ciencuadrasPaste } from '../resources/js/ciencuadras-paste.js';
import { fillRows } from '../resources/js/comparable-bulk-import.js';

const card = { url: '/inmueble/oficina-en-arriendo-o-venta-en-boca-grande-cartagena-123',
    heading: 'Oficina en arriendo o ventacartagena, comuna 1, boca grande ',
    text: 'Valor de compra: $600.000.000 71.27 m2 Habit. 0 Baños 1 Garaje 1' };

test('copied cards preserve sale price, decimals, location and unverified PH', () => {
    const [row] = parseCiencuadrasCards([card, card], 'Cartagena de Indias');
    assert.equal(parseCiencuadrasCards([card, card], 'Cartagena').length, 1);
    assert.equal(row.price_unit, 'precio_total');
    assert.equal(row.operation, 'Venta');
    assert.equal(row.price_amount, '600.000.000');
    assert.equal(row.area_m2, '71,27');
    assert.equal(row.neighborhood, 'boca grande');
    assert.equal(row.ph_regime, 'por_verificar');
});
test('rejects other cities, types, search URLs, unsafe links and incomplete cards', () => {
    for (const url of ['javascript:alert(1)', 'https://evil.test/inmueble/a-123', '/venta/oficina?v=Bocagrande',
        'https://user@www.ciencuadras.com/inmueble/a-123']) assert.equal(ciencuadrasUrl(url), '');
    assert.equal(parseCiencuadrasCards([card], 'Bogotá').length, 0);
    assert.equal(parseCiencuadrasCards([{ ...card, heading: 'Apartamento en venta Cartagena' }], 'Cartagena').length, 0);
    assert.equal(parseCiencuadrasCards([{ ...card, text: 'Valor de compra: $600.000.000' }], 'Cartagena').length, 0);
});
test('pasting and selecting a tabulated batch never writes the matrix', () => {
    const savedDocument = globalThis.document;
    let events = 0;
    const form = { querySelectorAll: () => [], dispatchEvent: () => events++ };
    globalThis.document = { getElementById: () => form };
    try {
        const ui = ciencuadrasPaste(); ui.$el = { dataset: { city: 'Cartagena' } }; ui.init();
        const text = 'fuente\tenlace\tprecio\tarea\toperacion\nCiencuadras\thttps://www.ciencuadras.com/inmueble/oficina-123\t600000000\t42\tVenta';
        ui.paste({ preventDefault() {}, clipboardData: { getData: type => type === 'text/plain' ? text : '' } });
        assert.equal(ui.results.length, 1); assert.equal(ui.selected.length, 0);
        ui.selectAll(); assert.equal(ui.selected.length, 1); assert.equal(events, 0);
    } finally { globalThis.document = savedDocument; }
});
test('deferred review imports possible matches, blocks exact URLs, and never declares them distinct', () => {
    const original = { source_url: 'https://www.ciencuadras.com/inmueble/oficina-123', price_amount: '600000000', area_m2: '42', neighborhood: 'Bocagrande' };
    const keys = ['source_url', 'price_amount', 'area_m2', 'neighborhood', 'comparability_notes', 'status'];
    const rows = [original, {}].map((data, i) => ({ sectionRowIndex: i, querySelectorAll: () => keys.map(key => controls[i][key]) }));
    const controls = [original, {}].map((data, i) => Object.fromEntries(keys.map(key => [key, { name: `comparables[${i}][${key}]`, value: data[key] || '', tagName: 'INPUT' }])));
    const form = { querySelectorAll: () => rows, dispatchEvent() {} };
    const result = fillRows(form, [original, { ...original, source_url: 'https://www.ciencuadras.com/inmueble/oficina-456' }], '', undefined, { deferDuplicateReview: true });
    assert.equal(result.count, 1); assert.equal(result.duplicates, 1);
    assert.match(controls[1].comparability_notes.value, /pendiente/);
    assert.doesNotMatch(controls[1].comparability_notes.value, /declaró inmueble distinto/);
    assert.equal(controls[1].status.value, 'por_verificar');
});
