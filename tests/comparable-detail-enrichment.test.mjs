import { test } from 'node:test';
import assert from 'node:assert/strict';
import { completeListingDetails } from '../resources/js/comparable-detail-enrichment.js';

test('individual fichas enrich every card, preserve contradictions and keep failed listings', async () => {
    const oldDocument=globalThis.document,oldFetch=globalThis.fetch;
    globalThis.document={querySelector:()=>({content:'csrf'})};
    const cards=[{row:{source_url:'https://source.test/1',price_amount:'100',published_text:'Listado',published_attributes:'{"Baños":"1"}'}},
        {row:{source_url:'https://source.test/2',area_m2:'90'}}];
    let active=0,calls=0;
    globalThis.fetch=async (_,request)=>{
        assert.equal(active++,0); calls++;
        assert.equal(request.headers['X-CSRF-Token'],'csrf');
        assert.equal(request.body.get('_token'),'csrf');
        await Promise.resolve(); active--;
        const first=request.body.get('source_url').endsWith('/1');
        return {ok:first,status:first?200:502,headers:{get:()=> 'application/json'},json:async()=>first?
            {ok:true,row:{price_amount:'110',bathrooms:'2',elevator:'Sí',published_text:'Descripción de la ficha con recepción',published_attributes:'{"Baños":"2","Recepción":"Sí"}'}}:
            {ok:false,message:'Portal bloqueó la lectura'}};
    };
    const result=await completeListingDetails(cards,'/leer');
    assert.deepEqual(result,{completed:1,failed:1}); assert.equal(calls,2);
    assert.equal(cards[0].row.price_amount,'100'); assert.equal(cards[0].row.bathrooms,'2');
    assert.equal(JSON.parse(cards[0].row.published_attributes).Recepción,'Sí');
    assert.match(cards[0].row.source_updates,/Precio publicado/);
    assert.match(cards[0].row.published_text,/Listado[\s\S]*recepción/);
    assert.match(cards[1].detailState,/pendiente/); assert.equal(cards[1].row.area_m2,'90');
    globalThis.document=oldDocument; globalThis.fetch=oldFetch;
});

test('rate limiting stops further requests and keeps all remaining cards pending', async () => {
    const oldDocument=globalThis.document,oldFetch=globalThis.fetch;
    globalThis.document={querySelector:()=>({content:'csrf'})};
    let calls=0;
    globalThis.fetch=async()=>{calls++;return {ok:false,status:429,headers:{get:()=> 'application/json'},json:async()=>({ok:false})};};
    const cards=[1,2,3].map(number=>({row:{source_url:'https://source.test/'+number}}));
    assert.deepEqual(await completeListingDetails(cards,'/leer'),{completed:0,failed:3});
    assert.equal(calls,1); assert.ok(cards.every(card=>/Límite/.test(card.detailState)));
    globalThis.document=oldDocument; globalThis.fetch=oldFetch;
});
