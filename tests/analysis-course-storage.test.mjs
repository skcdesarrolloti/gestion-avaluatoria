import test from 'node:test';
import assert from 'node:assert/strict';
import {marketAnalysisTable} from '../resources/js/market-analysis-table.js';
import {execFileSync} from 'node:child_process';
const rows=()=>Array.from({length:34},(_,i)=>({id:i.toString(16).padStart(32,'0'),ph_regime:'si',area_m2:String(40+i),price_amount:String(4e8+i*5e6),negotiation_discount:'0',bathrooms:String(i%3+1),parking_spaces:String(i%2),property_type:'Oficina',analysis_factor_selection:JSON.stringify({selected:['bathrooms','parking_spaces'],applied:['bathrooms','parking_spaces'],threshold:50,regime_applied:true,view:'result',statistics:[]})}));
test('saved execution restores central measures, quartiles, notes and exact calculation time without recalculating',async()=>{
    const s=marketAnalysisTable(rows(),'si','oficina');await s.courseCalculate();s.courseStep=3;s.courseNotes[s.analysisRows[0].id]='Revisar fuente';s.courseConclusion='Conclusión provisional';
    const snapshot=execFileSync('php',['-r','require "bootstrap.php"; echo App\\Services\\ComparableCourseExecution::normalize(stream_get_contents(STDIN));'],{input:s.courseSnapshot()}).toString(),reloadRows=rows();reloadRows[0].analysis_course_execution=snapshot;
    const reloaded=marketAnalysisTable(reloadRows,'si','oficina');let calculations=0;reloaded.courseCalculate=()=>{calculations++;};await reloaded.courseRestore();
    assert.equal(calculations,0);assert.equal(reloaded.courseStep,3);assert.equal(reloaded.courseResult.at,s.courseResult.at);
    assert.deepEqual(reloaded.courseResult.summary.estimators,s.courseResult.summary.estimators);
    assert.equal(reloaded.courseResult.summary.q1,s.courseResult.summary.q1);assert.equal(reloaded.courseResult.summary.q3,s.courseResult.summary.q3);
    assert.deepEqual(reloaded.courseNotes,s.courseNotes);assert.equal(reloaded.courseConclusion,s.courseConclusion);assert.equal(reloaded.courseCurrent(),true);
    reloaded.analysisRows[0].price_amount='900000000';assert.equal(reloaded.courseCurrent(),false);assert.equal(reloaded.courseResult.at,s.courseResult.at);
});
test('legacy prepared data reconstruct once without adding history stages and invalid snapshots are retained',async()=>{
    const s=marketAnalysisTable(rows(),'si','oficina');s.analysisStatistics=[{action:'factors'}];const before=JSON.stringify(s.analysisStatistics);await s.courseRestore();
    assert.equal(s.courseResult.reconstructed,true);assert.equal(s.courseStep,3);assert.equal(JSON.stringify(s.analysisStatistics),before);
    const broken=rows();broken[0].analysis_course_execution='{"v":999}';const state=marketAnalysisTable(broken,'si','oficina');await state.courseRestore();
    assert.match(state.courseError,/recuperar/);assert.equal(state.courseSnapshot(),broken[0].analysis_course_execution);
});
