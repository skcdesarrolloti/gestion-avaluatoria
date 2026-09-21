import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readPhPages } from '../resources/js/ph-page-reader.js';

test('reads all 244 mixed PDF pages including the last; OCR is per page', async () => {
    const cleaned = [], recognized = [], progress = [];
    const pdf = { numPages: 244, getPage: async number => ({
        number,
        getTextContent: async () => ({ items: [{ str: number % 2 ? 'Reglamento '.repeat(20) : '' }] }),
        cleanup: () => cleaned.push(number),
    }) };
    const pages = await readPhPages(pdf, async page => {
        recognized.push(page.number);
        return { text: `Texto OCR página ${page.number}`, confidence: 90 };
    }, (page, total) => progress.push([page, total]));
    assert.equal(pages.length, 244);
    assert.equal(pages[243].text, 'Texto OCR página 244');
    assert.equal(recognized.length, 122);
    assert.equal(cleaned.length, 244);
    assert.deepEqual(progress.at(-1), [244, 244]);
});

test('an unreadable page interrupts instead of returning a silently truncated document', async () => {
    let cleaned = false;
    const pdf = { numPages: 5, getPage: async () => ({
        getTextContent: async () => ({ items: [] }), cleanup: () => { cleaned = true; },
    }) };
    await assert.rejects(readPhPages(pdf, async () => { throw new Error('OCR unavailable'); }), /OCR unavailable/);
    assert.equal(cleaned, true);
});
