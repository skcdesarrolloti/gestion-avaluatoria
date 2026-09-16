import { test } from 'node:test';
import assert from 'node:assert/strict';
import { isFetchableUrl, redirectedUrl, shouldHandleLink } from '../resources/js/fetch-navigation.js';

const current = 'https://example.test/public/avaluos?page=1';

function link(overrides = {}) {
    return {
        href: 'https://example.test/public/normas-tecnicas-sectoriales',
        target: '',
        hasAttribute: () => false,
        closest: () => null,
        ...overrides,
    };
}

function click(overrides = {}) {
    return {
        altKey: false,
        button: 0,
        ctrlKey: false,
        defaultPrevented: false,
        metaKey: false,
        shiftKey: false,
        ...overrides,
    };
}

test('accepts same-origin html navigation', () => {
    assert.equal(isFetchableUrl('/public/normas-tecnicas-sectoriales', current), true);
    assert.equal(shouldHandleLink(link(), click(), current), true);
});

test('keeps external, modified and native links out of fetch navigation', () => {
    assert.equal(isFetchableUrl('https://otro.test/public', current), false);
    assert.equal(shouldHandleLink(link({ target: '_blank' }), click(), current), false);
    assert.equal(shouldHandleLink(link(), click({ ctrlKey: true }), current), false);
    assert.equal(shouldHandleLink(link({ hasAttribute: name => name === 'download' }), click(), current), false);
});

test('lets same-page anchors scroll normally', () => {
    const anchor = 'https://example.test/public/avaluos?page=1#nuevo-avaluo';
    assert.equal(isFetchableUrl(anchor, current), false);
});

test('keeps return_to hash after post redirects', () => {
    const body = new FormData();
    body.set('return_to', '/public/avaluos/abc/bien-sujeto#atributos');
    assert.equal(
        redirectedUrl('https://example.test/public/avaluos/abc/bien-sujeto', current, body, current),
        'https://example.test/public/avaluos/abc/bien-sujeto#atributos',
    );
});

test('keeps app-route return_to hash after post redirects', () => {
    const body = new FormData();
    body.set('return_to', 'avaluos/abc/bien-sujeto#fotos-general');
    assert.equal(
        redirectedUrl('https://example.test/public/avaluos/abc/bien-sujeto', current, body, current),
        'https://example.test/public/avaluos/abc/bien-sujeto#fotos-general',
    );
});
