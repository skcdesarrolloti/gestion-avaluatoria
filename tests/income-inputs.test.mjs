import test from 'node:test';
import assert from 'node:assert/strict';
import {parseHTML,DOMParser,CustomEvent} from 'linkedom';
import {incomeInputs} from '../resources/js/income-inputs.js';

function fixture() {
    const saved={window:globalThis.window,location:globalThis.location,fetch:globalThis.fetch,
        DOMParser:globalThis.DOMParser,CustomEvent:globalThis.CustomEvent};
    const {document}=parseHTML('<html><body><section data-income-state=\'{"incomeTab":"concept","incomePortal":"Metrocuadrado"}\' data-income-capture-url="/avaluos/test/metodo?renta_paso=capture"><div data-income-capture-content></div></section></body></html>');
    let calls=0,mounts=0,flushed=true,release;const events=[];
    globalThis.location=new URL('https://app.example/avaluos/test/metodo');
    globalThis.DOMParser=DOMParser;globalThis.CustomEvent=CustomEvent;
    globalThis.window={gaFlushAutosaves:async()=>flushed,dispatchEvent:event=>events.push(event),
        Alpine:{mutateDom:fn=>fn(),initTree:()=>mounts++}};
    const root=document.querySelector('section');
    const capture='<div data-income-capture-content><form id="tabla-madre-83"><textarea name="draft">Aviso original</textarea></form></div>';
    const reply=(html=capture,url='https://app.example/avaluos/test/metodo?renta_paso=capture')=>({ok:true,url,
        headers:{get:()=> 'text/html'},text:async()=>html});
    globalThis.fetch=async(url,options)=>{
        calls++;assert.match(url,/renta_paso=capture/);assert.equal(options.credentials,'same-origin');
        assert.equal(options.cache,'no-store');return new Promise(resolve=>{release=()=>resolve(reply());});
    };
    const component=incomeInputs();component.$el=root;component.init();
    return {component,root,events,reply,get calls(){return calls;},get mounts(){return mounts;},
        finish:async()=>{for(let i=0;i<8&&!release;i++) await Promise.resolve();release();},
        setFlushed:value=>{flushed=value;},restore:()=>Object.assign(globalThis,saved)};
}
test('capture waits for confirmed save, deduplicates concurrent clicks, and preserves the mounted draft',async()=>{
    const f=fixture();try {
        f.component.$el=f.root.ownerDocument.createElement('a'); // Alpine event target differs from component root.
        const one=f.component.openTab('capture'),two=f.component.openTab('capture');
        await f.finish();await Promise.all([one,two]);
        assert.equal(f.calls,1);assert.equal(f.mounts,1);assert.equal(f.component.incomeLoaded,true);
        const draft=f.root.querySelector('textarea');draft.value='Pegado sin incorporar';
        await f.component.openTab('concept');await f.component.openTab('capture');
        assert.equal(f.calls,1);assert.equal(f.root.querySelector('textarea'),draft);assert.equal(draft.value,'Pegado sin incorporar');
        assert.equal(f.events.at(-1).detail,'Metrocuadrado');
    } finally {f.restore();}
});
test('failed save blocks loading; retry works without discarding the conceptual state',async()=>{
    const f=fixture();try {
        f.setFlushed(false);await f.component.openTab('capture');
        assert.equal(f.calls,0);assert.equal(f.component.incomeLoaded,false);assert.match(f.component.incomeError,/guardado pendiente/);
        f.setFlushed(true);const retry=f.component.openTab('capture');await f.finish();await retry;
        assert.equal(f.component.incomeLoaded,true);assert.equal(f.component.incomeError,'');
    } finally {f.restore();}
});
test('network failure, login redirect and obsolete HTML do not mark capture loaded and allow retry',async()=>{
    const f=fixture();try {
        for(const response of [new Error('offline'),f.reply('', 'https://app.example/login'),f.reply('<p>Versión anterior</p>')]) {
            globalThis.fetch=async()=>{if(response instanceof Error) throw response;return response;};
            await f.component.openTab('capture');assert.equal(f.component.incomeLoaded,false);
            assert.equal(f.mounts,0);assert.match(f.component.incomeError,/Reintenta/);
        }
        globalThis.fetch=async()=>f.reply();await f.component.openTab('capture');assert.equal(f.mounts,1);
    } finally {f.restore();}
});

test('next portal keeps one mounted capture and its unsaved paste while changing source and view',async()=>{
    const f=fixture();try {
        Object.assign(f.component,{incomePortals:['FincaRaiz','Metrocuadrado','Ciencuadras'],incomeSources:['FincaRaiz','Metrocuadrado','Ciencuadras']});
        const loading=f.component.openTab('capture');await f.finish();await loading;
        const draft=f.root.querySelector('textarea');draft.value='Página pegada pendiente de subir';
        assert.equal(await f.component.nextPortal(),true);
        assert.equal(f.component.incomePortal,'Ciencuadras');assert.equal(f.calls,1);assert.equal(f.mounts,1);
        assert.equal(f.root.querySelector('textarea'),draft);assert.equal(draft.value,'Página pegada pendiente de subir');
        assert.equal(f.events.at(-2).type,'income-portal');assert.equal(f.events.at(-2).detail,'Ciencuadras');
        assert.equal(f.events.at(-1).type,'income-source-view');assert.equal(f.events.at(-1).detail,'capture');
        assert.equal(await f.component.choosePortal('FincaRaiz','review'),true);
        assert.equal(f.events.at(-1).detail,'review');assert.equal(f.component.incomePortal,'FincaRaiz');
        assert.equal(f.root.querySelector('textarea'),draft);
    } finally {f.restore();}
});

test('pending server save blocks next portal; last portal does not wrap or submit samples',async()=>{
    const f=fixture();try {
        Object.assign(f.component,{incomePortals:['Metrocuadrado','Ciencuadras'],incomeSources:['Metrocuadrado','Ciencuadras']});
        f.setFlushed(false);assert.equal(await f.component.nextPortal(),false);
        assert.equal(f.component.incomePortal,'Metrocuadrado');assert.equal(f.calls,0);
        assert.match(f.component.incomePortalError,/pendientes de guardar/);assert.equal(f.events.length,0);
        f.component.incomePortal='Ciencuadras';assert.equal(await f.component.nextPortal(),false);
        assert.equal(await f.component.choosePortal('portal-inventado'),false);assert.equal(f.calls,0);
    } finally {f.restore();}
});

test('navigation remembers only tab and configured source in the current URL, preserving study context',async()=>{
    const f=fixture();try {
        let remembered;window.history={state:{route:'retained'},replaceState:(state,title,url)=>{remembered={state,url:new URL(url)};}};
        globalThis.location=new URL('https://app.example/avaluos/test/metodo?method=renta&component=office');
        f.component.incomeSources=['Metrocuadrado'];
        assert.equal(await f.component.choosePortal('Metrocuadrado','search'),true);
        assert.equal(remembered.url.searchParams.get('renta_portal'),'Metrocuadrado');
        assert.equal(remembered.url.searchParams.get('renta_paso'),'search');
        assert.equal(remembered.url.searchParams.get('component'),'office');
        assert.equal(remembered.url.searchParams.get('method'),'renta');assert.deepEqual(remembered.state,{route:'retained'});
        assert.equal(f.calls,0);
    } finally {f.restore();}
});

test('saved source aliases keep the configured portal selected and its next-source navigation',async()=>{
    const f=fixture();try {
        Object.assign(f.component,{incomePortals:['FincaRaiz','Metrocuadrado','Mercado Libre Inmuebles'],incomeSources:['FincaRaiz','Metrocuadrado','Mercado Libre Inmuebles']});
        f.component.incomePortal='FincaRaíz';assert.equal(f.component.incomeNextPortal,'Metrocuadrado');
        assert.equal(await f.component.choosePortal('Mercado Libre','search'),true);
        assert.equal(f.component.incomePortal,'Mercado Libre Inmuebles');assert.equal(f.component.incomeNextPortal,'');
        assert.equal(f.calls,0);
    } finally {f.restore();}
});
