import test from 'node:test';
import assert from 'node:assert/strict';
import { candidateMatches, unresolvedCandidates } from '../resources/js/comparable-candidate-review.js';

const row = { source_url: 'https://example.com/1', price_amount: '420000000', area_m2: '40', neighborhood: 'Bocagrande' };
test('unchecking a matching candidate permits keeping just one, but matrix matches still require review', () => {
    const results = [{ row }, { row: { ...row, source_url: 'https://example.com/2' } }];
    candidateMatches(results, []).forEach((matches, i) => { results[i].matches = matches; });
    assert.equal(unresolvedCandidates(results, results.map(item => item.row.source_url)).length, 2);
    assert.equal(unresolvedCandidates(results, [row.source_url]).length, 0);
    candidateMatches(results, [{ ...row, source_url: 'https://example.com/3' }]).forEach((matches, i) => { results[i].matches = matches; });
    assert.equal(unresolvedCandidates(results, [row.source_url]).length, 1);
    results[0].distinct = true;
    assert.equal(unresolvedCandidates(results, [row.source_url]).length, 0);
});
test('preflight identifies both matching candidates before either is inserted', () => {
    const results = [{ row }, { row: { ...row, source_url: 'https://example.com/2' } }, { row: { ...row, source_url: 'https://example.com/3', area_m2: '90' } }];
    const before = JSON.stringify(results);
    const matches = candidateMatches(results, []);
    assert.equal(matches[0][0].label, 'Aviso 2 de esta página');
    assert.equal(matches[1][0].label, 'Aviso 1 de esta página');
    assert.equal(matches[0][0].exact, false);
    assert.deepEqual(matches[2], []);
    assert.equal(JSON.stringify(results), before);
});
test('preflight preserves actual matrix numbering including empty rows and identifies exact URLs', () => {
    const matches = candidateMatches([{ row: { ...row, source_url: row.source_url + '?utm_source=test' } }], [{}, {}, {}, row]);
    assert.equal(matches[0][0].label, 'Muestra 4 de la matriz');
    assert.equal(matches[0][0].exact, true);
    assert.equal(matches[0][0].row.source_url, row.source_url);
});

