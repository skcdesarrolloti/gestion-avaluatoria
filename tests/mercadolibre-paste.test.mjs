import test from 'node:test';
import assert from 'node:assert/strict';
import { parseHTML } from 'linkedom';
import { copiedMercadolibreRows, mercadolibreUrl } from '../resources/js/mercadolibre-paste.js';
const card = (id = '12345678', operation = 'Oficina en venta', area = '40 m² cubiertos', place = 'Bocagrande, Cartagena De Indias, Bolívar') => `
<div class="poly-card"><span class="poly-component__headline">${operation}</span>
<h3><a class="poly-component__title" href="https://inmueble.mercadolibre.com.co/MCO-${id}-oficina-_JM#tracking=1">Oficina venta o arriendo</a></h3>
<div class="poly-price__current"><span>$</span><span>435.000.000</span></div>
<ul><li class="poly-attributes_list__item">1 baño</li><li class="poly-attributes_list__item">${area}</li></ul>
<span class="poly-component__location">${place}</span></div>`;
function read(html) {
    const previous = globalThis.document;
    globalThis.document = parseHTML('<html></html>').document;
    try { return copiedMercadolibreRows(html, 'Cartagena de Indias'); }
    finally { globalThis.document = previous; }
}
test('Mercado Libre reads cards, preserves published area class and canonicalizes tracking', () => {
    const rows = read(card() + card());
    assert.equal(rows.length, 1);
    assert.equal(rows[0].price_amount, '435.000.000');
    assert.equal(rows[0].area_m2, '40');
    assert.equal(rows[0].neighborhood, 'Bocagrande');
    assert.equal(rows[0].ph_regime, 'por_verificar');
    assert.match(rows[0].comparability_notes, /40 m² cubiertos/);
    assert.ok(!rows[0].source_url.includes('#'));
});
test('Mixed results omit rentals, other types/cities and incomplete cards without borrowing values', () => {
    const html = card('123', 'Oficina en arriendo') + card('124', 'Local en venta')
        + card('125', 'Oficina en venta', '', 'Bocagrande, Cartagena, Bolívar')
        + card('126', 'Oficina en venta', '30 m² cubiertos', 'Chapinero, Bogotá, Colombia')
        + card('127', 'Oficina en venta', '71,27 m² totales', 'Carrera 3, Bocagrande, Cartagena, Bolívar');
    const rows = read(html);
    assert.equal(rows.length, 1);
    assert.equal(rows[0].area_m2, '71,27');
    assert.equal(rows[0].neighborhood, 'Bocagrande');
});
test('Mercado Libre rejects search URLs, unsafe domains, foreign currencies and ambiguous price or area', () => {
    for (const url of ['javascript:alert(1)', 'https://listado.mercadolibre.com.co/oficinas',
        'https://user@inmueble.mercadolibre.com.co/MCO-123-oficina-_JM', 'https://evil.test/MCO-123-oficina-_JM'])
        assert.equal(mercadolibreUrl(url), '');
    for (const html of [card().replace('<span>$', '<span>US$'), card().replace('<span>$', '<span>Desde $'),
        card().replace('40 m² cubiertos', '40 - 90 m² cubiertos')]) assert.equal(read(html).length, 0);
});
