import test from 'node:test';
import assert from 'node:assert/strict';
import {marketAnalysisTable} from '../resources/js/market-analysis-table.js';
import {reactive,effect,stop} from '@vue/reactivity';
test('reactive cache reuses model reads and invalidates data, scope, factors and discounts',()=>{
    const rows=Array.from({length:34},(_,i)=>({id:String(i),ph_regime:i<30?'si':'no',area_m2:String(40+i),price_amount:'400000000',bathrooms:String(1+i%3),parking_spaces:String(i%2),property_type:'Oficina'}));
    const s=marketAnalysisTable(rows,'si','oficina');s.analysisReactiveCache=true;s.analysisRegimeApplied=true;s.analysisView='result';s.analysisApplied=['bathrooms','parking_spaces'];
    const first=s.analysisVisibleRows();assert.equal(s.analysisVisibleRows(),first);assert.equal(first.length,30);
    const table=s.analysisTable;assert.equal(s.analysisTable,table);s.analysisRows[0].bathrooms='4';s.analysisDataRevision++;
    assert.notEqual(s.analysisTable,table);assert.equal(s.analysisVisibleRows()[0].values.bathrooms,'4');
    s.analysisApplied=['bathrooms'];assert.equal(s.analysisColumns().length,1);
    s.analysisAppliedScope='all';assert.equal(s.analysisVisibleRows().length,34);
    s.analysisChange('0','10');s.regressionBasis='adjusted';assert.equal(s.regressionMatrix().complete.length,1);
    s.analysisModule='regression';s.analysisView='raw';s.analysisAppliedScope='subject';assert.equal(s.analysisActiveRows().length,30);
});
test('navigation reuses prepared reads and combination lookup does not mutate reactive state or history',()=>{
    const rows=Array.from({length:71},(_,i)=>({id:String(i),ph_regime:i<34?'si':'no',area_m2:String(40+i),price_amount:'400000000',bathrooms:String(1+i%3),parking_spaces:String(i%2),property_type:'Oficina'}));
    const state=reactive(marketAnalysisTable(rows,'si','oficina'));state.analysisReactiveCache=true;state.analysisRegimeApplied=true;state.analysisView='result';state.analysisApplied=['bathrooms','parking_spaces'];
    const first=state.analysisVisibleRows(),before=JSON.stringify(state.analysisStatistics);
    let renders=0;const read=effect(()=>{state.analysisVisibleRows();renders++;});
    for(const panel of ['location','statistics','regression','samples']){
        state.analysisModule=panel;assert.equal(state.analysisVisibleRows(),first);
    }
    assert.equal(renders,1);stop(read);
    const mutations=[];const lookup=effect(()=>state.analysisCombinations(),{onTrigger:e=>mutations.push(e.key)});
    state.analysisModule='regression';state.analysisCombinations();
    assert.deepEqual(mutations,[]);stop(lookup);
    assert.equal(JSON.stringify(state.analysisStatistics),before);
    state.analysisRows[0].bathrooms='5';state.analysisDataRevision++;
    assert.equal(state.analysisVisibleRows()[0].values.bathrooms,'5');
});
