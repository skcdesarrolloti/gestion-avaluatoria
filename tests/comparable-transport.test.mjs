import test from 'node:test';
import assert from 'node:assert/strict';
import { packComparableRows } from '../resources/js/comparable-transport.js';
import { fincaraizAreaSearch } from '../resources/js/fincaraiz-area-search.js';

test('60-row payload preserves the last fields without PHP input count truncation', () => {
    const body = new FormData(); body.set('_token', 'test'); body.set('version', '4');
    for (let i = 0; i < 60; i++) for (let c = 0; c < 50; c++) body.set(`comparables[${i}][field${c}]`, `${i}-${c}`);
    packComparableRows(body);
    assert.equal([...body.keys()].length, 3);
    assert.equal(body.get('version'), '4');
    assert.equal(JSON.parse(body.get('comparable_rows_json'))[59].field49, '59-49');
});
test('PH filtering never treats unspecified listings as non-PH and preserves hidden candidates', () => {
    const c = fincaraizAreaSearch();
    c.results = [{ row: {} }, { row: { ph_regime: 'si' } }, { row: { ph_regime: 'no' } }];
    c.phFilter = 'no'; assert.equal(c.visibleResults.length, 1);
    c.phFilter = 'por_verificar'; assert.equal(c.visibleResults.length, 1);
    c.phFilter = 'all'; assert.equal(c.visibleResults.length, 3);
});
