import { test } from 'node:test';
import assert from 'node:assert/strict';
import { parseComparableBlock, parseComparableText } from '../resources/js/comparable-bulk-import.js';

test('extracts useful fields from copied listing text', () => {
    const row = parseComparableBlock(`Venta oficina Edificio Bahía
https://www.fincaraiz.com.co/oficina-cartagena-123
$ 650.000.000
Área 329 m2
Tel 3001234567`);
    assert.equal(row.source_name, 'Fincaraiz');
    assert.equal(row.source_type, 'portal');
    assert.equal(row.source_url, 'https://www.fincaraiz.com.co/oficina-cartagena-123');
    assert.equal(row.operation, 'Venta');
    assert.equal(row.price_amount, '$ 650.000.000');
    assert.equal(row.price_unit, 'precio_total');
    assert.equal(row.area_m2, '329');
    assert.equal(row.contact_phone, '3001234567');
    assert.match(row.project_name, /Edificio/);
});

test('splits several pasted listing blocks', () => {
    const rows = parseComparableText(`https://metrocuadrado.com/uno
Canon $ 3.500.000
80 m2

https://ciencuadras.com/dos
Venta $ 900.000.000
120 m2`);
    assert.equal(rows.length, 2);
    assert.equal(rows[0].operation, 'Arriendo');
    assert.equal(rows[0].price_unit, 'canon_mensual');
    assert.equal(rows[1].source_name, 'Ciencuadras');
});
