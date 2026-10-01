import test from 'node:test';
import assert from 'node:assert/strict';
import { parseProperatiCards, properatiUrl, properatiPaste } from '../resources/js/properati-paste.js';

const card = { url: '/detalle/14032-32-9698-8882b78662f2-423e0a5c-8aea-4173?utm_source=test',
    heading: 'Oficina en Venta en Bocagrande', location: 'Bocagrande, Cartagena, Bolívar',
    price: '$ 1.260.000.000', area: '71,27 m²', agency: 'Inmobiliaria de prueba' };

test('Properati keeps actual portal and published location, distinguishes Proppit network, canonicalizes duplicate cards', () => {
    const rows = parseProperatiCards([card, { ...card, url: card.url.split('?')[0] }], 'Cartagena de Indias');
    assert.equal(rows.length, 1);
    assert.equal(rows[0].source_name, 'Properati');
    assert.equal(rows[0].price_amount, '1.260.000.000');
    assert.equal(rows[0].area_m2, '71,27');
    assert.equal(rows[0].neighborhood, 'Bocagrande');
    assert.equal(rows[0].contact_name, card.agency);
    assert.equal(rows[0].ph_regime, 'por_verificar');
    assert.match(rows[0].comparability_notes, /Red Proppit/);
    assert.ok(!rows[0].source_url.includes('?'));
});

test('Properati rejects projects, unsafe URLs, monthly or starting prices, other cities/types, ambiguous areas', () => {
    for (const url of ['javascript:alert(1)', 'https://evil.test/detalle/12-34', '/s/bocagrande/oficina/venta',
        '/proyecto/12-34', 'https://user@properati.com.co/detalle/12-34']) assert.equal(properatiUrl(url), '');
    for (const change of [{ heading: 'Oficina en Arriendo en Bocagrande' }, { heading: 'Apartamento en Venta en Bocagrande' },
        { location: 'Bogotá, Cundinamarca' }, { price: 'Desde $ 300.000.000' }, { price: '$ 3.000.000/mes' },
        { area: '40 - 90 m²' }, { area: '' }]) assert.equal(parseProperatiCards([{ ...card, ...change }], 'Cartagena').length, 0);
    assert.equal(parseProperatiCards([card], '').length, 0);
    assert.equal(parseProperatiCards([{ ...card, location: 'Cartagena, Bolívar' }], 'Cartagena')[0].neighborhood, '');
});

test('Properati does not import a bare search URL or tabulated data from another source on paste', () => {
    const saved = globalThis.document;
    globalThis.document = { getElementById: () => ({ querySelectorAll: () => [] }) };
    try {
        const ui = properatiPaste(); ui.$el = { dataset: { city: 'Cartagena' } }; ui.init();
        for (const text of ['https://www.properati.com.co/s/bocagrande/oficina/venta', 'fuente\tenlace\tprecio\tarea\nCiencuadras\thttps://www.ciencuadras.com/inmueble/oficina-123\t400000000\t40']) {
            ui.paste({ preventDefault() {}, clipboardData: { getData: type => type === 'text/plain' ? text : '' } });
            assert.equal(ui.results.length, 0); assert.match(ui.message, /No se reconocieron tarjetas/);
        }
    } finally { globalThis.document = saved; }
});
