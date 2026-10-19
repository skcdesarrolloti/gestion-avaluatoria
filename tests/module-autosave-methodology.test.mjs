import {test} from 'node:test';
import assert from 'node:assert/strict';
import {HTMLFormElement,HTMLInputElement,setup} from './module-autosave-support.mjs';

test('method and memory share acknowledged version while comparable and PH forms retain their independent versions',async()=>{
    const {listeners,runTimer,cleanup}=setup();
    try {
        const method=new HTMLFormElement(),memory=new HTMLFormElement(),samples=new HTMLFormElement(),ph=new HTMLFormElement();
        for(const [form,action] of [[method,'/flujo'],[memory,'/flujo'],[samples,'/comparables'],[ph,'/alcance-ph']]){
            form.getAttribute=key=>key==='action'?action:null;
            form.version.setAttribute=(key,value)=>{assert.equal(key,'value');form.version.value=value;};
        }
        method.closest=selector=>selector==='[data-methodology-version-group]'?{querySelectorAll:()=>[method,memory,samples,ph]}:null;
        globalThis.fetch=async()=>({ok:true,json:async()=>({ok:true,version:8})});
        listeners.input({target:new HTMLInputElement(method)});await runTimer();
        assert.match(method.status.textContent,/confirmado/);assert.equal(memory.version.value,'8');
        assert.equal(samples.version.value,'7');assert.equal(ph.version.value,'7');
    } finally {cleanup();}
});
