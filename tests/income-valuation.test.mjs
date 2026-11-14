import test from 'node:test';
import assert from 'node:assert/strict';
import { incomeValuation } from '../resources/js/income-valuation.js';

test('rental rate removal clears only its adoption; draft row changes remain pending',()=>{
    const s=incomeValuation();let changes=0;s.$nextTick=fn=>fn();s.$refs={valuationForm:{dispatchEvent:()=>changes++}};
    s.state.rates=[{id:'a'},{id:'b'}];s.state.adoption={direct_id:'a',discount_id:'b',exit_id:'a'};
    s.removeRow('rates',0);
    assert.deepEqual(s.state.adoption,{direct_id:'',discount_id:'b',exit_id:''});assert.equal(s.stale,true);assert.equal(changes,1);
    s.addRow('rates');assert.match(s.state.rates[1].id,/^[0-9a-f]{16}$/);assert.equal(changes,2);
});
test('unconfirmed or offline drafts prevent financial calculation and keep all inputs',async()=>{
    globalThis.window={gaFlushAutosaves:async()=>false};const s=incomeValuation();s.state.projection.market_rent='1000';
    await s.calculate();assert.equal(s.busy,false);assert.match(s.message,/sin confirmar/);assert.equal(s.state.projection.market_rent,'1000');
});
test('network failure retains the rental scenario for retry rather than claiming success',async()=>{
    globalThis.window={gaFlushAutosaves:async()=>true};globalThis.document={querySelector:()=>({content:'fixture'})};
    const s=incomeValuation();s.$refs={valuationForm:{dataset:{autosaveEndpoint:'/test'}}};s.state.adoption.direct_reason='Soporte';
    const original=globalThis.FormData,oldFetch=globalThis.fetch;globalThis.FormData=class {set(){}};let sent=0;
    globalThis.fetch=async()=>{sent++;throw new TypeError('Network failed');};
    try {await s.calculate();assert.equal(sent,1);assert.equal(s.busy,false);assert.match(s.message,/Network failed/);assert.equal(s.state.adoption.direct_reason,'Soporte');}
    finally {globalThis.FormData=original;globalThis.fetch=oldFetch;}
});
test('preliminary flow confirms the shared version without marking final valuation current',async()=>{
    globalThis.window={gaFlushAutosaves:async()=>true};globalThis.document={querySelector:()=>({content:'fixture'})};
    const s=incomeValuation(),version={value:'4'};s.stale=true;s.previewStale=true;
    s.$refs={valuationForm:{dataset:{autosaveEndpoint:'/test'},closest:()=>({querySelectorAll:()=>[version]})}};
    const original=globalThis.FormData,oldFetch=globalThis.fetch;let operation,refreshed=false;
    globalThis.FormData=class {set(k,v){if(k==='operation') operation=v;}};
    globalThis.fetch=async()=>({ok:true,status:200,json:async()=>({ok:true,version:5})});
    s.refreshResults=async()=>{refreshed=true;};
    try {
        await s.calculate('preview');assert.equal(operation,'preview');assert.equal(version.value,'5');assert.ok(refreshed);
        assert.equal(s.previewStale,false);assert.equal(s.stale,true);assert.equal(s.busy,false);assert.match(s.message,/preliminar guardado/);
    } finally {globalThis.FormData=original;globalThis.fetch=oldFetch;}
});
test('conflicting preliminary flow keeps the previous table stale and inputs available',async()=>{
    globalThis.window={gaFlushAutosaves:async()=>true};globalThis.document={querySelector:()=>({content:'fixture'})};
    const s=incomeValuation();s.previewStale=true;s.state.projection.scenario_rent='2941590.06';
    s.$refs={valuationForm:{dataset:{autosaveEndpoint:'/test'}}};
    const original=globalThis.FormData,oldFetch=globalThis.fetch;globalThis.FormData=class {set(){}};
    globalThis.fetch=async()=>({ok:false,status:409,json:async()=>({message:'La valoración cambió en otra pestaña.'})});
    try {await s.calculate('preview');assert.ok(s.previewStale);assert.equal(s.busy,false);assert.match(s.message,/otra pestaña/);assert.equal(s.state.projection.scenario_rent,'2941590.06');}
    finally {globalThis.FormData=original;globalThis.fetch=oldFetch;}
});
