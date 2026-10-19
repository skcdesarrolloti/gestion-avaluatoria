import test from 'node:test';
import assert from 'node:assert/strict';
import {execFileSync} from 'node:child_process';
import {marketAnalysisTable} from '../resources/js/market-analysis-table.js';

const create=()=>marketAnalysisTable(Array.from({length:6},(_,i)=>({id:(i+1).toString(16).padStart(32,'0'),ph_regime:'si',property_type:'Oficina',
    price_amount:String(400000000+i*10000000),area_m2:'40',negotiation_discount:'0',bathrooms:i===0?'1':'',parking_spaces:'1',
    analysis_factor_selection:JSON.stringify({selected:['bathrooms','parking_spaces'],applied:[],threshold:50,view:'result',regime_applied:true})})),'si','oficina');

test('an enabled attribute remains available to complete when its only captured value is outside the working group',()=>{
    const s=create();s.analysisRows[0].ph_regime='no';
    assert.equal(s.analysisActiveRows().length,5);
    assert.equal(s.analysisVerificationColumns().find(f=>f.key==='bathrooms').count,0);
    assert.equal(s.courseInlineDraft(s.analysisRows[1].id).factors.find(f=>f.key==='bathrooms').value,'');
    assert.equal(s.analysisVerificationComplete(),0);assert.equal(s.analysisColumns().length,0);
});

test('enabled sparse and constant attributes can be completed without becoming eligible model factors',async()=>{
    const s=create();await s.courseCalculate();const result=s.courseResult,at=result.at,history=JSON.stringify(s.analysisStatistics);
    assert.deepEqual(s.analysisVerificationColumns().map(f=>f.key).sort(),['bathrooms','parking_spaces']);
    assert.equal(s.analysisColumns().length,0);assert.equal(s.analysisVerificationComplete(),1);
    const id=s.analysisRows[1].id,draft=s.courseInlineEdit(id);
    draft.factors.find(f=>f.key==='bathrooms').value='2';draft.source='Ficha cotejada, fecha y responsable';
    assert.equal(await s.courseInlineApply(id,draft),true);
    assert.equal(s.analysisVerificationComplete(),2);assert.equal(s.analysisRows[1].bathrooms,'');
    assert.equal(s.analysisVisibleRows()[1].values.bathrooms,'2');assert.equal(s.analysisColumns().length,0);
    assert.equal(s.courseResult,result);assert.equal(s.courseResult.at,at);assert.equal(JSON.stringify(s.analysisStatistics),history);
    s.analysisUpdate();assert.deepEqual(s.analysisApplied,[]);assert.equal(s.analysisRows.length,6);
});

test('disabling a field hides its editor, ignores its pending correction, and preserves stored data on reload',async()=>{
    const s=create(),id=s.analysisRows[0].id,draft=s.courseInlineEdit(id);
    draft.factors.find(f=>f.key==='parking_spaces').value='99';draft.source='Soporte de la oferta';draft.offer='450000000';
    s.analysisSelected=['bathrooms'];assert.deepEqual(s.courseInlineFields(draft).map(f=>f.key),['bathrooms']);
    assert.equal(await s.courseInlineApply(id,draft),true);
    assert.equal(s.analysisRows[0].parking_spaces,'1');assert.doesNotMatch(s.analysisRows[0].analysis_manual_factors,/parking_spaces/);
    s.analysisRows[0].analysis_factor_selection=s.analysisSelection();
    const restored=marketAnalysisTable(s.analysisRows,'si','oficina');
    assert.deepEqual(restored.analysisVerificationColumns().map(f=>f.key),['bathrooms']);
    restored.analysisSelected.push('parking_spaces');
    assert.equal(restored.courseInlineEdit(id).factors.find(f=>f.key==='parking_spaces').value,'1');
    assert.equal(restored.analysisRows[0].price_amount,'450000000');
});

test('study basis persists through server validation before the first calculation',async()=>{
    const s=create();s.courseBasis='offer';
    const normalized=execFileSync('php',['-r','require "bootstrap.php"; $r=\\App\\Services\\ComparableCaptureDetail::normalize(["analysis_factor_selection"=>stream_get_contents(STDIN)]); echo $r["analysis_factor_selection"];'],{input:s.analysisSelection()}).toString();
    const restored=marketAnalysisTable(s.analysisRows.map((r,i)=>({...r,analysis_factor_selection:i===0?normalized:''})),'si','oficina');
    await restored.init();assert.equal(restored.courseBasis,'offer');assert.equal(restored.courseResult,null);
    assert.deepEqual(restored.analysisSelected,s.analysisSelected);
});
