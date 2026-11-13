import test from 'node:test';
import assert from 'node:assert/strict';
import {parseHTML} from 'linkedom';
import {showSaveBlocked} from '../resources/js/save-navigation-notice.js';

test('blocked chapter navigation exposes the reason, keeps the draft and offers login in another tab',()=>{
    const old=globalThis.document;
    const {document,HTMLElement}=parseHTML('<html><body><form action="https://example.test/public/logout"></form><main id="contenido"><a href="/public/avaluos/abc/metodologia-valuatoria">8 · Metodología valuatoria</a><form data-autosave-login="1"><textarea>Texto pendiente</textarea><p data-autosave-tone="error">La sesión venció.</p></form></main></body></html>');
    let focused=false,scrolled=false,retries=0;
    HTMLElement.prototype.focus=function(){focused=true;};HTMLElement.prototype.scrollIntoView=function(){scrolled=true;};
    globalThis.document=document;
    document.querySelector('form').action='https://example.test/public/logout';
    const link=document.querySelector('a');link.click=()=>{retries++;};
    try {
        showSaveBlocked(link);showSaveBlocked(link);
        assert.equal(document.querySelectorAll('[data-save-blocked]').length,1);
        const notice=document.querySelector('[data-save-blocked]');
        assert.match(notice.textContent,/8 · Metodología valuatoria/);assert.match(notice.textContent,/La sesión venció/);
        assert.equal(document.querySelector('textarea').value,'Texto pendiente');
        assert.equal(notice.querySelector('a').getAttribute('href'),'https://example.test/public/login');
        assert.equal(notice.querySelector('a').target,'_blank');assert.equal(notice.querySelector('a').rel,'noopener');
        notice.querySelector('button').click();assert.equal(retries,1);assert.ok(focused && scrolled);
    } finally {globalThis.document=old;}
});
