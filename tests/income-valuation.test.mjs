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
