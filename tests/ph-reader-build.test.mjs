import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { createHash } from 'node:crypto';

const hash = file => createHash('sha256').update(readFileSync(file)).digest('hex').slice(0, 20);

test('published app and PH reader reference the content revisions of their deployed dependencies', () => {
    const app = readFileSync('public/assets/app.js', 'utf8');
    const reader = readFileSync('public/assets/ph-pdf-reader.js', 'utf8');
    assert.ok(app.includes(JSON.stringify(hash('public/assets/ph-pdf-reader.js'))),
        'Rebuild app.js after changing the PH reader');
    assert.ok(reader.includes(JSON.stringify(hash('public/assets/pdf.worker.mjs'))),
        'Rebuild the PH reader after changing PDF.js');
});
