import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { execFileSync } from 'node:child_process';
import { filterIgacOptions, igacUnitSelector } from '../resources/js/igac-unit-selector.js';

const php = process.platform === 'win32' ? 'C:/xampp/php/php.exe' : 'php';
const rules = JSON.parse(execFileSync(php, ['-r',
    "require 'bootstrap.php'; echo json_encode(App\\Support\\IgacTypologyFilter::rules());"], { encoding: 'utf8' }));
const catalog = JSON.parse(readFileSync('resources/data/tipologias_igac_catalogo.json', 'utf8'))
    .filter(item => item.categoria === 'ANEXOS').map(item => ({ value: item.denominacion, label: item.denominacion,
        description: item.descripcion, specifications: item.especificaciones }));

test('garage and parking bay aliases find actual related references, with accents', () => {
    const expected = filterIgacOptions(catalog, { type: 'parqueo', rules });
    assert.ok(expected.length > 0 && expected.length < catalog.length);
    for (const query of ['garaje', 'garage', 'parqueadero', 'celda de parqueo', 'estacionamiento']) {
        assert.deepEqual(filterIgacOptions(catalog, { type: 'parqueo', query, rules }), expected);
    }
    assert.ok(expected.some(item => item.value === 'Anexos.Sótano_Sencillo'));
    assert.ok(!expected.some(item => item.value.includes('Estacion_Sistema_Transporte')));
    const deposits = filterIgacOptions(catalog, { type: 'deposito', query: 'cuarto útil', rules });
    assert.deepEqual(deposits.map(item => item.value), ['Anexos.Depósitos_1']);
});

test('search never erases saved selection or substitutes a typology on empty results', () => {
    const selected = 'Anexos.Depósitos_1';
    const options = filterIgacOptions(catalog, { type: 'parqueo', query: 'inexistente', selected, rules });
    assert.deepEqual(options.map(item => item.value), [selected]);
    assert.deepEqual(filterIgacOptions(catalog, { type: 'parqueo', query: 'inexistente', rules }), []);
    assert.equal(filterIgacOptions(catalog, { type: 'parqueo', showAll: true, rules }).length, catalog.length);
    const selector = Object.assign(igacUnitSelector({ unitKind: 'annex', constructionType: 'parqueo',
        igacCategory: 'ANEXOS', igacHint: selected }), { typologies: { ANEXOS: catalog }, igacFilterRules: rules,
        constructionIgacCategories: { parqueo: 'ANEXOS' } });
    selector.syncIgacFromConstruction();
    assert.equal(selector.igacHint, selected);
});
