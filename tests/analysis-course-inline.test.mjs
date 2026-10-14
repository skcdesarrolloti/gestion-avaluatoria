import test from 'node:test';
import assert from 'node:assert/strict';
import {marketAnalysisTable} from '../resources/js/market-analysis-table.js';
const create=()=>marketAnalysisTable(Array.from({length:34},(_,i)=>({id:i.toString(16).padStart(32,'0'),ph_regime:'si',area_m2:'40',price_amount:String(4e8+i*5e6),negotiation_discount:'0',bathrooms:String(i%3+1),parking_spaces:String(i%2),property_type:'Oficina',analysis_factor_selection:JSON.stringify({selected:['bathrooms','parking_spaces'],applied:['bathrooms','parking_spaces'],threshold:50,regime_applied:true,view:'result'})})),'si','oficina');
test('inline correction stays at sensitivity, preserves previous execution and originals of factors, and recalculates only on request',async()=>{
    const s=create();s.analysisModule='statistics';s.courseStep=4;await s.courseCalculate();const id=s.analysisRows[0].id,oldResult=s.courseResult,oldHistory=JSON.stringify(s.analysisStatistics);
    const draft=s.courseInlineDraft(id);draft.offer='500000000';draft.area='50';draft.percent='5,5';draft.source='Fuente verificada, fecha y motivo';draft.factors.find(f=>f.key==='bathrooms').value='4';
    assert.equal(await s.courseInlineApply(id,draft),true);
    assert.equal(s.analysisModule,'statistics');assert.equal(s.courseStep,4);assert.equal(s.courseResult,oldResult);assert.equal(s.courseCurrent(),false);
    assert.equal(s.analysisRows[0].bathrooms,'1');assert.equal(s.analysisVisibleRows()[0].values.bathrooms,'4');
    assert.match(s.analysisRows[0].source_updates,/400000000 → 500000000/);assert.equal(JSON.stringify(s.analysisStatistics),oldHistory);
    await s.courseCalculate();assert.equal(s.courseCurrent(),true);assert.equal(s.courseResult.valid[0].y,9450000);
});
test('invalid or unsupported corrections leave source rows and execution unchanged',async()=>{
    const s=create(),id=s.analysisRows[0].id;await s.courseCalculate();const before=JSON.stringify(s.analysisRows),draft=s.courseInlineDraft(id);draft.area='0';draft.source='Fuente';
    assert.equal(await s.courseInlineApply(id,draft),false);assert.equal(JSON.stringify(s.analysisRows),before);
    draft.area='50';draft.source='';assert.equal(await s.courseInlineApply(id,draft),false);assert.equal(JSON.stringify(s.analysisRows),before);
});
