import test from 'node:test';
import assert from 'node:assert/strict';
import {DOMParser,parseHTML} from 'linkedom';
import {postAutosave} from '../resources/js/autosave-session.js';

const current='https://example.test/public/avaluos/abc/bien-sujeto';
function setup() {
    const old={document:globalThis.document,window:globalThis.window,DOMParser:globalThis.DOMParser,fetch:globalThis.fetch};
    globalThis.document=parseHTML('<html><head><meta name="csrf-token" content="old"></head><body><input name="_token" value="old"><textarea>Borrador intacto</textarea></body></html>').document;
    globalThis.window={location:{href:current}};globalThis.DOMParser=DOMParser;
    const form={dataset:{autosaveEndpoint:current+'/unidades/autoguardar'}};
    const body=new FormData();body.set('_token','old');body.set('version','7');body.set('units[x][notes]','Texto sin confirmar');
    return {form,body,cleanup:()=>Object.assign(globalThis,old)};
}
const fresh=()=>({ok:true,url:current,text:async()=>'<html><head><meta name="csrf-token" content="fresh"></head><body></body></html>'});

test('419 refreshes only the token and retries the same draft/version once',async()=>{
    const {form,body,cleanup}=setup();const calls=[];
    try {
        globalThis.fetch=async(url,request)=>{
            calls.push({url,method:request.method,token:request.headers['X-CSRF-Token']});
            if(calls.length===1) return {ok:false,status:419};
            if(calls.length===2) return fresh();
            assert.equal(request.body.get('version'),'7');assert.equal(request.body.get('units[x][notes]'),'Texto sin confirmar');
            assert.equal(request.body.get('_token'),'fresh');return {ok:true,status:200};
        };
        assert.equal((await postAutosave(form,body)).status,200);
        assert.equal(calls.length,3);assert.equal(calls[1].url,current);assert.equal(calls[2].token,'fresh');
        assert.equal(document.querySelector('textarea').value,'Borrador intacto');
        assert.equal(document.querySelector('input').value,'fresh');
    } finally {cleanup();}
});

test('expired login stops retries and leaves all draft fields in place',async()=>{
    const {form,body,cleanup}=setup();let calls=0;
    try {
        globalThis.fetch=async()=>++calls===1?{ok:false,status:419}:{ok:true,url:'https://example.test/public/login'};
        await assert.rejects(postAutosave(form,body),/otra pestaña/);
        assert.equal(calls,2);assert.equal(form.dataset.autosaveLogin,'1');assert.equal(body.get('version'),'7');
        assert.equal(body.get('units[x][notes]'),'Texto sin confirmar');assert.equal(document.querySelector('textarea').value,'Borrador intacto');
    } finally {cleanup();}
});

test('renewing a token does not bypass a conflict or retry indefinitely',async()=>{
    const {form,body,cleanup}=setup();
    try {
        for(const status of [409,419]) {
            let calls=0;globalThis.fetch=async()=>{calls++;return calls===1?{ok:false,status:419}:calls===2?fresh():{ok:false,status};};
            if(status===419) await assert.rejects(postAutosave(form,body),/sesión venció/);
            else assert.equal((await postAutosave(form,body)).status,409);
            assert.equal(calls,3);assert.equal(body.get('version'),'7');
        }
    } finally {cleanup();}
});

test('an HTML login redirect is never mistaken for a saved form',async()=>{
    const {form,body,cleanup}=setup();
    try {
        globalThis.fetch=async()=>({ok:true,status:200,url:'https://example.test/public/login'});
        await assert.rejects(postAutosave(form,body),/No recargues/);
        assert.equal(form.dataset.autosaveLogin,'1');
    } finally {cleanup();}
});
