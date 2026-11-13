import test from 'node:test';
import assert from 'node:assert/strict';
import {incomeSelection} from '../resources/js/income-selection.js';

test('cambiar muestra conserva su borrador y no lleva motivos de otra muestra',async()=>{
    globalThis.window={gaFlushAutosaves:async()=>true};
    const s=incomeSelection();let changes=0;
    Object.assign(s,{sample:'a',category:'error',reason:'Dato equivocado',support:'Soporte A',
        drafts:{b:{category:'duplicate',reason:'Mismo inmueble',support:'Soporte B'}},
        $nextTick:fn=>fn(),$refs:{sample:{dispatchEvent:()=>changes++},reason:{focus(){}}}});
    await s.choose('b');assert.equal(s.reason,'Mismo inmueble');assert.equal(s.drafts.a.reason,'Dato equivocado');
    await s.choose('c');assert.equal(s.reason,'');assert.equal(s.support,'');
    await s.choose('a');assert.equal(s.reason,'Dato equivocado');assert.equal(changes,3);
    window.gaFlushAutosaves=async()=>false;await s.choose('b');assert.equal(s.sample,'a');assert.equal(s.busy,false);
    delete globalThis.window;
});
test('guardado sin confirmar no aplica una exclusión',async()=>{
    globalThis.window={gaFlushAutosaves:async()=>false};const s=incomeSelection();
    await s.decide('exclude');assert.equal(s.applied,false);assert.equal(s.busy,false);assert.match(s.message,/borrador pendiente/);
    delete globalThis.window;
});
test('actualizar desde un botón sustituye el panel completo sin anidar resultados',async()=>{
    const calls=[],fresh={},panel={replaceWith:value=>calls.push(['replace',value])};
    const previous=globalThis.fetch;
    globalThis.location={href:'https://example.test/renta',pathname:'/renta'};
    globalThis.fetch=async()=>({ok:true,url:location.href,text:async()=>''});
    globalThis.DOMParser=class {parseFromString(){return {querySelector:()=>fresh};}};
    globalThis.window={Alpine:{mutateDom:fn=>fn(),destroyTree:node=>calls.push(['destroy',node]),initTree:node=>calls.push(['init',node])}};
    try {
        const s=incomeSelection();s.$el={replaceWith:()=>assert.fail('no reemplazar botón')};s.$refs={form:{closest:()=>panel}};
        await s.refresh();assert.deepEqual(calls,[['destroy',panel],['replace',fresh],['init',fresh]]);
    } finally {globalThis.fetch=previous;delete globalThis.location;delete globalThis.DOMParser;delete globalThis.window;}
});
