import { test } from 'node:test';
import assert from 'node:assert/strict';
import {
    bankSectionFromHash,
    nextBankSection,
    normalizeBankSection,
    targetSectorFor,
} from '../resources/js/sector-bank-tabs.js';

test('normalizes bank section codes from UI and hash values', () => {
    assert.equal(normalizeBankSection('4'), '04');
    assert.equal(normalizeBankSection('04'), '04');
    assert.equal(bankSectionFromHash('#banco-4'), '04');
    assert.equal(bankSectionFromHash('#banco-04'), '04');
});

test('computes next sector from the active visible tab', () => {
    const codes = ['01', '02', '03', '04'];
    assert.equal(nextBankSection(codes, '01'), '02');
    assert.equal(nextBankSection(codes, '04'), '');
    assert.equal(targetSectorFor(codes, '03'), 'banco-04');
    assert.equal(targetSectorFor(codes, '04'), 'bien-sujeto');
});
