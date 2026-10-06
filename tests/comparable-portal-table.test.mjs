import test from 'node:test';
import assert from 'node:assert/strict';
import { portalTable } from '../resources/js/comparable-portal-table.js';
import { comparableIntake } from '../resources/js/comparable-intake.js';

test('portal table keeps 19 separate samples, actual values, zero and no empty catalog columns', () => {
    const ads=Array.from({length:19},(_,i)=>({id:String(i),price_amount:String(100+i),area_m2:'60',
        published_attributes:JSON.stringify({Precio:String(100+i),'Área':'60 m²',Baños:i===0 ? '0' : '',Anunciante:'Agencia',...(i===1 ? {Balcón:'Mencionado'} : {})})}));
    const before=JSON.stringify(ads), table=portalTable(ads);
    assert.equal(table.rows.length,19);
    assert.equal(table.columns.filter(c=>c.key==='price_amount').length,1);
    assert.equal(table.columns.filter(c=>c.key==='area_m2').length,1);
    assert.equal(table.rows[0].values.bathrooms,'0');
    assert.equal(table.rows[1].values.bathrooms,undefined);
    assert.equal(table.columns.some(c=>c.label==='Vista'),false);
    assert.equal(table.rows[1].values['published:balcon'],'Mencionado');
    assert.equal(table.rows[2].values['published:balcon'],undefined);
    assert.equal(JSON.stringify(ads),before);
});

test('table switches portals and confirmation scope without merging linked ads or using subject factors', () => {
    const entries=[{used:true,index:0,data:{id:'a',property_group:'same',source_name:'FincaRaíz',price_amount:'100',capture_confirmation:'confirmed'}},
        {used:true,index:1,data:{id:'b',property_group:'same',source_name:'FincaRaíz',price_amount:'110'}},
        {used:true,index:2,data:{id:'c',property_group:'same',source_name:'Agencia',price_amount:'120'}}];
    const state=comparableIntake(()=>entries,()=>({dataset:{intakeSources:'["FincaRaíz","Agencia"]',
        intakeEvidence:'{"catalog":{"vista":{"label":"Vista","sample":"view_quality"}}}'}}));
    state.intakePortal='FincaRaíz'; state.rebuildIntake();
    assert.deepEqual(state.intakeTableData.rows.map(r=>r.values.price_amount),['100','110']);
    state.intakeView='confirmed'; state.rebuildIntake();
    assert.deepEqual(state.intakeTableData.rows.map(r=>r.key),['a']);
    state.intakeView='table'; state.intakePortal='Agencia'; state.rebuildIntake();
    assert.deepEqual(state.intakeTableData.rows.map(r=>r.values.price_amount),['120']);
    assert.equal(state.intakeTableData.columns.some(c=>c.label==='Vista'),false);
});
