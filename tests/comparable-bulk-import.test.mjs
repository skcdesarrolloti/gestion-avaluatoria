import { test } from 'node:test';
import assert from 'node:assert/strict';
import { parseComparableBlock, parseComparableText } from '../resources/js/comparable-bulk-import.js';
import { comparableUrlKey, hasComparableData, missingComparableFields } from '../resources/js/comparable-review.js';

test('explicit tabular operation determines unit despite dual-operation listing URL', () => {
    const [row] = parseComparableText('fuente\tenlace\tprecio\tarea\toperacion\nCiencuadras\thttps://www.ciencuadras.com/inmueble/oficina-en-arriendo-o-venta-123\t$600.000.000\t42\tVenta');
    assert.equal(row.operation, 'Venta');
    assert.equal(row.price_unit, 'precio_total');
    assert.equal(row.price_amount, '$600.000.000');
});

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

test('does not invent a telephone from a listing ID or price', () => {
    const row = parseComparableBlock('https://portal.example/3001234567 Venta $ 900000000 Área 80 m2');
    assert.equal(row.contact_phone, '');
    assert.equal(row.price_amount, '$ 900000000');
    assert.equal(row.area_m2, '80');
});

test('maps spreadsheet headers so price never becomes area', () => {
    const [row] = parseComparableText('Fuente\tEnlace\tPrecio\tÁrea\tTeléfono\nPortal\thttps://portal.example/123\t650000000\t329\t3001234567');
    assert.equal(row.price_amount, '650000000');
    assert.equal(row.area_m2, '329');
    assert.equal(row.contact_phone, '3001234567');
    assert.equal(row.source_name, 'Portal');
});

test('handles malformed pasted URLs without losing the batch', () => {
    assert.doesNotThrow(() => parseComparableText('https://[malformed\n$ 800.000.000\n\nhttps://example.com/ok'));
});

test('matches tracking variants but preserves different listing identifiers', () => {
    assert.equal(comparableUrlKey('https://www.example.com/a/?id=12&utm_source=x#ref'),
        comparableUrlKey('http://example.com/a?id=12'));
    assert.notEqual(comparableUrlKey('https://example.com/?id=12'), comparableUrlKey('https://example.com/?id=13'));
    assert.equal(comparableUrlKey('javascript:alert(1)'), '');
});

test('blank defaults do not count, coordinate-only drafts remain occupied', () => {
    assert.equal(hasComparableData({ operation: 'Venta', consulted_at: '2026-09-30' }), false);
    assert.equal(hasComparableData({ latitude: '10.391' }), true);
    assert.ok(missingComparableFields({ source_url: 'https://example.com' }).includes('área'));
});
