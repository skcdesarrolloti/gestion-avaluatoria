import test from 'node:test';
import assert from 'node:assert/strict';
import { parseHTML } from 'linkedom';
import { copiedProperatiRows } from '../resources/js/properati-paste.js';

function read(html) {
    const previous = globalThis.document;
    globalThis.document = parseHTML('<html></html>').document;
    try { return copiedProperatiRows(html, 'Cartagena de Indias'); }
    finally { globalThis.document = previous; }
}
const card = (id, price, area, location = 'Bocagrande, Cartagena, Bolívar') => `<div>
    <div><img alt="Foto 1 de SE VENDE OFICINA EN BOCAGRANDE"></div>
    <div><div><a href="https://www.properati.com.co/detalle/1234-${id}"
        title="Oficina en Venta en Bocagrande">Oficina en Venta en Bocagrande</a>
        <div>${price}</div><div>${location}</div></div>
        <div><span>2 baños</span><span>${area}</span></div></div></div>`;

test('Native clipboard without article or data-test preserves each listing URL, price and area', () => {
    const rows = read(card('aa', '$ 400.000.000', '40 m²') + card('bb', '$ 1.750.000.000', '170 m²'));
    assert.deepEqual(rows.map(row => [row.source_url, row.price_amount, row.area_m2]), [
        ['https://www.properati.com.co/detalle/1234-aa', '400.000.000', '40'],
        ['https://www.properati.com.co/detalle/1234-bb', '1.750.000.000', '170'],
    ]);
});

test('Clipboard fallback never borrows missing area from the next card or imports another city', () => {
    const rows = read(`<div>${card('aa', '$ 400.000.000', '')}${card('bb', '$ 800.000.000', '80 m²')}
        ${card('cc', '$ 350.000.000', '35 m²', 'Chapinero, Bogotá, Cundinamarca')}</div>`);
    assert.equal(rows.length, 1);
    assert.equal(rows[0].source_url, 'https://www.properati.com.co/detalle/1234-bb');
});

test('Clipboard rejects uncertain prices, missing links and unrelated addresses without executing markup', () => {
    assert.equal(read(card('aa', 'Desde $ 400.000.000', '40 m²')).length, 0);
    assert.equal(read(card('aa', '$ 4.000.000/mes', '40 m²')).length, 0);
    assert.equal(read(card('aa', '$ 400.000.000', '40 m²').replaceAll('href=', 'data-lost-href=')).length, 0);
    assert.equal(read(card('aa', '$ 400.000.000', '40 m²').replaceAll('www.properati.com.co', 'evil.test')).length, 0);
    assert.equal(read('<script>throw new Error("must not run")</script>' + card('aa', '$ 400.000.000', '40 m²')).length, 1);
});

test('Clipboard keeps a title attribute when the portal excludes the link text from selection', () => {
    const html = card('aa', '$ 400.000.000', '40 m²').replace('>Oficina en Venta en Bocagrande</a>', '></a>');
    assert.equal(read(html).length, 1);
});

const sharedCard = (id, price, area) => `<article><img alt="Foto 1 de Oficina">
    <div><wl-share title="Oficina en Venta en Bocagrande"
        url="https://www.properati.com.co/detalle/1234-${id}"><div class="share__icon"></div></wl-share>
    <div data-test="snippet__price">${price}</div>
    <div data-test="snippet__location">Bocagrande, Cartagena, Bolívar</div></div>
    <span data-test="area-value">${area}</span><span data-test="agency-name">Agencia publicada</span></article>`;

test('Actual Chrome selection without title links reads the retained share URL and title per article', () => {
    const rows = read(sharedCard('aa', '$ 1.260.000.000', '300 m²') + sharedCard('bb', '$ 1.750.000.000', '170 m²'));
    assert.deepEqual(rows.map(row => [row.source_url, row.price_amount, row.area_m2]), [
        ['https://www.properati.com.co/detalle/1234-aa', '1.260.000.000', '300'],
        ['https://www.properati.com.co/detalle/1234-bb', '1.750.000.000', '170'],
    ]);
    assert.equal(rows[0].contact_name, 'Agencia publicada');
    assert.equal(rows[0].ph_regime, 'por_verificar');
});

test('Share fallback validates domain, operation and missing fields like normal title links', () => {
    const html = sharedCard('aa', '$ 400.000.000', '40 m²');
    assert.equal(read(html.replace('www.properati.com.co', 'evil.test')).length, 0);
    assert.equal(read(html.replace('en Venta', 'en Arriendo')).length, 0);
    assert.equal(read(html.replace('40 m²', '') + sharedCard('bb', '$ 500.000.000', '50 m²')).length, 1);
    assert.equal(read(html + html).length, 1);
});
