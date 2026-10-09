import test from 'node:test';
import assert from 'node:assert/strict';
test('applied group count stays at 34 while original consultation shows 71 without changing saved decisions',()=>{
 const saved={scope:'subject',applied_scope:'subject',regime_applied:true,view:'result',selected:[],applied:[],statistics:[],review:[]};
 const rows=Array.from({length:71},(_,i)=>({id:String(i),ph_regime:i<34?'si':'no',area_m2:'40',price_amount:'400000000',...(i===0?{analysis_factor_selection:JSON.stringify(saved)}:{})}));
 const state=marketAnalysisTable(rows,'si');const before=state.analysisSelection();
 state.analysisModule='samples';state.analysisView='raw';assert.equal(state.analysisActiveRows().length,71);assert.equal(state.analysisWorkingCount(),34);
 state.analysisView='result';assert.equal(state.analysisWorkingCount(),34);assert.equal(state.analysisSelection(),before);
});
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
 state.analysisChange(rows[0].id,'10');state.analysisSelected=['bathrooms','floor_level'];state.analysisUpdate();
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

test('opening result applies changed draft factors and preserves the previous six-factor stage',async()=>{
 const rows=Array.from({length:71},(_,i)=>({id:i.toString(16).padStart(32,'0'),ph_regime:i<34?'si':'no',price_amount:String(100000000+i*1000000),area_m2:String(40+i),bathrooms:String(i%3+1),parking_spaces:String(i%2),published_attributes:JSON.stringify({Piso:String(i%5),Ascensor:i%2?'Sí':'No',Vigilancia:i%2?'No':'Sí'})}));
 const state=marketAnalysisTable(rows,'si');state.analysisView='regime';await state.analysisApplyRegime();state.analysisView='clean';
 state.analysisSelected=['bathrooms','parking_spaces','floor_level','published:ascensor','published:vigilancia'];state.analysisUpdate();
 const old=JSON.stringify(state.analysisStatistics.at(-1));assert.equal(state.analysisStatistics.at(-1).factors.length+1,6);
 state.analysisView='clean';state.analysisSelected=['bathrooms','parking_spaces','floor_level'];assert.equal(state.analysisFactorsPending(),true);
 state.analysisShowResult();assert.equal(state.analysisView,'result');assert.equal(state.analysisColumns().length+1,4);assert.equal(state.analysisActiveRows().length,34);
 assert.equal(state.analysisStatistics.at(-1).factors.length+1,4);assert.equal(JSON.stringify(state.analysisStatistics.at(-2)),old);assert.equal(state.analysisFactorsPending(),false);
 const count=state.analysisStatistics.length;state.analysisView='clean';state.analysisShowResult();assert.equal(state.analysisStatistics.length,count);
});
test('opening results before factor selection keeps factors pending',async()=>{
 const state=marketAnalysisTable([{id:'a'.repeat(32),ph_regime:'si',price_amount:'100',area_m2:'10'}],'si');state.analysisView='regime';await state.analysisApplyRegime();state.analysisShowResult();
 assert.equal(state.analysisStatistics.some(v=>v.action==='factors'),false);
});

test('private area is the single area predictor: three factors need thirty complete rows, without changing unit-price denominator',async()=>{
 const rows=Array.from({length:34},(_,i)=>({id:i.toString(16).padStart(32,'0'),ph_regime:'si',price_amount:'100000000',area_m2:String(40+i),published_attributes:JSON.stringify({'Área Privada':(30+i)+' m2','Antigüedad':i>=2&&i<29?String(i):'','Estado':i<22?(i%2?'Usado':'Nuevo'):''})}));
 const state=marketAnalysisTable(rows,'si');state.analysisView='regime';await state.analysisApplyRegime();state.analysisView='clean';
 state.analysisSelected=['published:area privada','published:antiguedad','published:estado'];state.analysisUpdate();
 assert.equal(state.analysisFactorCount(),3);assert.equal(state.analysisModelArea().key,'published:area privada');
 assert.deepEqual(state.analysisSampleRule(),{factors:3,complete:20,required:30,maximum:2,meets:false});
 assert.equal(state.analysisStatistics.at(-1).factor_count,3);assert.equal(state.analysisStatistics.at(-1).complete,20);
 state.analysisChange(rows[0].id,'10');assert.equal(state.analysisResult(rows[0].id).perM2,2250000);
 const old=structuredClone(state.analysisStatistics.at(-1));delete old.model_area;delete old.factor_count;
 state.analysisStatistics=[old];state.analysisView='clean';assert.equal(state.analysisFactorsPending(),true);state.analysisShowResult();
 assert.deepEqual(state.analysisStatistics[0],old);assert.equal(state.analysisStatistics.at(-1).factor_count,3);
 const reload=marketAnalysisTable([{...rows[0],analysis_factor_selection:state.analysisSelection()},...rows.slice(1)],'si');
 assert.equal(reload.analysisFactorCount(),3);assert.deepEqual(reload.analysisStatistics,state.analysisStatistics);
});
test('model area must be positive; private free area never replaces mandatory area',async()=>{
 const rows=Array.from({length:34},(_,i)=>({id:i.toString(16).padStart(32,'0'),ph_regime:'si',area_m2:'40',bathrooms:String(i%3),published_attributes:JSON.stringify({'Área Privada':i===0?'0 m²':i+' m²','Área Privada Libre':String(i)})}));
 const state=marketAnalysisTable(rows,'si');state.analysisView='regime';await state.analysisApplyRegime();state.analysisView='clean';state.analysisSelected=['published:area privada','bathrooms'];state.analysisUpdate();
 assert.equal(state.analysisComplete(),33);assert.equal(state.analysisStatistics.at(-1).complete,33);assert.equal(state.analysisFactorCount(),2);
 state.analysisView='clean';state.analysisSelected=['published:area privada libre','bathrooms'];state.analysisUpdate();assert.equal(state.analysisModelArea().key,'area_m2');assert.equal(state.analysisFactorCount(),3);
});
