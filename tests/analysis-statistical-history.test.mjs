import test from 'node:test';
import assert from 'node:assert/strict';
import {marketAnalysisTable} from '../resources/js/market-analysis-table.js';
import {statistics} from '../resources/js/analysis-statistical-history.js';
test('sample statistics omit missing values and use sample deviation',()=>{
 assert.deepEqual(statistics([null,undefined]),{n:0,mean:null,median:null,sd:null,cv:null});
 assert.deepEqual(statistics([10,20,30]),{n:3,mean:20,median:20,sd:10,cv:50});
 assert.equal(statistics([10]).sd,null);
});
test('34,35,36 remain independent snapshots after reinclusions, discounts and reload',async()=>{
 const rows=Array.from({length:36},(_,i)=>({id:i.toString(16).padStart(32,'0'),ph_regime:i<34?'si':'no',area_m2:'10',price_amount:String(100+i*10),bathrooms:String(i%3+1),published_attributes:JSON.stringify({Piso:String(i%5)})}));
 const state=marketAnalysisTable(rows,'si');state.analysisView='regime';await state.analysisApplyRegime();
 assert.deepEqual(state.analysisStatistics.map(v=>v.count),[36,34]);
 const before=JSON.stringify(state.analysisStatistics[1]);
 await state.analysisReinclude(rows[34].id,true);await state.analysisReinclude(rows[35].id,true);
 assert.deepEqual(state.analysisStatistics.map(v=>v.count),[36,34,35,36]);assert.equal(JSON.stringify(state.analysisStatistics[1]),before);
 state.analysisChange(rows[0].id,'10');state.analysisSelected=['bathrooms','published:piso'];state.analysisUpdate();
 assert.equal(state.analysisStatistics.at(-1).action,'factors');assert.equal(state.analysisStatistics.at(-1).adjusted.n,1);
 assert.equal(state.analysisStatistics[1].adjusted.n,0);assert.equal(state.analysisStatistics.at(-1).complete,36);
 const reload=marketAnalysisTable([{...rows[0],analysis_factor_selection:state.analysisSelection()},...rows.slice(1)],'si');
 assert.deepEqual(reload.analysisStatistics,state.analysisStatistics);
 await reload.analysisReinclude(rows[34].id,false);assert.equal(reload.analysisStatistics.at(-1).count,35);assert.equal(reload.analysisStatistics[1].count,34);
});
test('an already applied group gets a baseline before the first reinclusion',async()=>{
 const rows=[{id:'a'.repeat(32),ph_regime:'si',area_m2:'10',price_amount:'100',analysis_factor_selection:JSON.stringify({scope:'subject',applied_scope:'subject',regime_applied:true,view:'regime',review:[{id:'b'.repeat(32),reason:'other',at:new Date().toISOString(),restored_at:''}]})},{id:'b'.repeat(32),ph_regime:'no',area_m2:'20',price_amount:'200'}];
 const state=marketAnalysisTable(rows,'si');await state.analysisReinclude(rows[1].id,true);
 assert.deepEqual(state.analysisStatistics.map(v=>v.count),[2,1,2]);assert.equal(state.analysisStatistics[1].action,'filter');
});

test('history capacity never overwrites previous stages or applies a blocked change',async()=>{
 const state=marketAnalysisTable([{id:'a'.repeat(32),ph_regime:'si'}],'si');state.analysisView='regime';
 state.analysisStatistics=Array.from({length:80},()=>({count:1,action:'filter'}));const before=JSON.stringify(state.analysisStatistics);
 await state.analysisApplyRegime();assert.equal(state.analysisRegimeApplied,false);assert.equal(JSON.stringify(state.analysisStatistics),before);assert.ok(state.analysisError);
});
