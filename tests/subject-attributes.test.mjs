import test from 'node:test';
import assert from 'node:assert/strict';
import { subjectAttributes } from '../resources/js/subject-attributes.js';

function attributeRow(ratingValue, weightValue = '', checked = true) {
    const weight = { value: weightValue, dispatched: false, dispatchEvent() { this.dispatched = true; } };
    const toggle = { checked };
    const row = {
        querySelector: (selector) => selector === '[data-attribute-weight]' ? weight : selector === '[data-attribute-toggle]' ? toggle : null,
    };
    const rating = {
        value: ratingValue,
        disabled: !checked,
        matches: (selector) => selector === '[data-attribute-rating]',
        closest: () => row,
    };
    return { rating, weight, toggle };
}

test('subject attribute score updates from current unsaved controls', () => {
    const rows = [attributeRow('4', '3'), attributeRow('3', '2'), attributeRow('5', '3'), attributeRow('2', '1')];
    const unit = {
        dataset: { unitId: 'u1' },
        querySelectorAll: (selector) => selector === '[data-attribute-rating]' ? rows.map((row) => row.rating) : [],
    };
    const component = subjectAttributes('u1');
    component.$el = { querySelectorAll: (selector) => selector === '[data-attribute-unit]' ? [unit] : [] };
    component.init();
    assert.equal(component.unitAdjustment('u1'), '+4,5%');
    assert.equal(component.unitScoreText('u1'), 'Ajuste +4,5% · índice 78%');
});

test('subject attribute rating assigns medium weight by default', () => {
    const row = attributeRow('4');
    const component = subjectAttributes('u1');
    component.$el = { querySelectorAll: () => [] };
    component.$nextTick = (callback) => callback();
    component.handleAttributeChange({ target: row.rating });
    assert.equal(row.weight.value, '2');
    assert.equal(row.weight.dispatched, true);
});

test('subject attribute score ignores incomplete rows', () => {
    const rows = [attributeRow('', ''), attributeRow('2', '2'), attributeRow('5', '')];
    const unit = {
        dataset: { unitId: 'u1' },
        querySelectorAll: (selector) => selector === '[data-attribute-rating]' ? rows.map((row) => row.rating) : [],
    };
    const component = subjectAttributes('u1');
    component.$el = { querySelectorAll: (selector) => selector === '[data-attribute-unit]' ? [unit] : [] };
    component.init();
    assert.equal(component.unitAdjustment('u1'), '-5%');
    assert.equal(component.unitScoreText('u1'), 'Ajuste -5% · índice 40%');
});

test('subject attribute selector suggests six but allows more when justified', () => {
    const toggles = Array.from({ length: 7 }, () => ({ checked: true }));
    const unit = { querySelectorAll: selector => selector === '[data-attribute-toggle]:checked' ? toggles.filter(t => t.checked) : [] };
    const target = {
        checked: true,
        matches: selector => selector === '[data-attribute-toggle]',
        closest: selector => selector === '[data-attribute-unit]' ? unit : null,
    };
    const component = subjectAttributes('u1');
    component.$el = { querySelector: () => unit, querySelectorAll: () => [] };
    component.$nextTick = callback => callback();
    component.handleAttributeChange({ target });
    assert.equal(target.checked, true);
    assert.match(component.unitLimitText('u1'), /máximo 6/);
});

test('subject attribute score ignores unchecked selected controls', () => {
    const rows = [attributeRow('5', '3', false), attributeRow('3', '2', true)];
    const unit = {
        dataset: { unitId: 'u1' },
        querySelectorAll: (selector) => selector === '[data-attribute-rating]' ? rows.map((row) => row.rating) : [],
    };
    const component = subjectAttributes('u1');
    component.$el = { querySelectorAll: (selector) => selector === '[data-attribute-unit]' ? [unit] : [] };
    component.init();
    assert.equal(component.unitAdjustment('u1'), '0%');
});
