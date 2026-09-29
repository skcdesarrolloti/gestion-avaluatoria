import { test } from 'node:test';
import assert from 'node:assert/strict';
import { normalizeMoney } from '../resources/js/money-input.js';

test('formats Colombian money values for rent fields', () => {
    assert.equal(normalizeMoney('3500000'), '$ 3.500.000');
    assert.equal(normalizeMoney('3.500.000,50'), '$ 3.500.000,50');
    assert.equal(normalizeMoney('$850.000'), '$ 850.000');
});
