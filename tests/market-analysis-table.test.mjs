import test from 'node:test';
import assert from 'node:assert/strict';
import {analysisCoverage,discountedAnalysis,marketAnalysisTable} from '../resources/js/market-analysis-table.js';
import {factorSuggestion} from '../resources/js/analysis-factor-suggestions.js';

test('coverage includes more than six observed factors, counts zero, and preserves incomplete rows',()=>{
 const facts=Object.fromEntries(Array.from({length:10},(_,i)=>['Factor '+i,'Sí']));
 const rows=[{id:'a',price_amount:'500000000',area_m2:'100',bathrooms:'0',published_attributes:JSON.stringify(facts)},
 {id:'b',price_amount:'400000000',area_m2:'80',bathrooms:'2',published_attributes:JSON.stringify({'Factor 0':'Sí','Sin dato':'No publicado'})}];
 const coverage=analysisCoverage(rows);
 assert.equal(coverage.find(f=>f.key==='bathrooms').count,2);assert.equal(coverage.find(f=>f.label==='Factor 0').count,2);
 assert.equal(coverage.length,11);assert.ok(!coverage.some(f=>f.label==='Sin dato'));
 const state=marketAnalysisTable(rows);assert.equal(state.analysisColumns().length,11);assert.equal(state.analysisTable.rows.length,2);
 assert.equal(state.analysisResult('a').value,null);state.analysisChange('a','10');
 assert.equal(state.analysisDiscounts.a,'50000000.0000');assert.equal(state.analysisResult('a').value,450000000);
 assert.equal(state.analysisResult('a').perM2,4500000);assert.equal(state.analysisResult('b').value,null);
 state.analysisChange('a','120');assert.equal(state.analysisDiscounts.a,'50000000.0000');assert.equal(state.analysisResult('a').value,null);
 state.analysisChange('a','');assert.equal(state.analysisDiscounts.a,'');assert.equal(rows[0].price_amount,'500000000');
});
test('office bedrooms stay in original evidence but are not analysis factors, independently of PH',()=>{
 const rows=[{id:'a',property_type:'Oficina',ph_regime:'si',bedrooms:'2',bathrooms:'1'},
 {id:'b',property_type:'Oficina',ph_regime:'no',bedrooms:'3',bathrooms:'2'}];
 const state=marketAnalysisTable(rows), bedrooms=state.analysisFactors.find(f=>f.key==='bedrooms');
 assert.equal(bedrooms.compatible,false);assert.ok(!state.analysisColumns().some(f=>f.key==='bedrooms'));
 assert.equal(state.analysisRows[0].bedrooms,'2');assert.equal(rows[1].bedrooms,'3');
 assert.match(state.analysisRegime(state.analysisRows[0]),/PH · falta soporte/);
 assert.match(state.analysisRegime(state.analysisRows[1]),/No PH · falta soporte/);
 state.analysisRows[1].ph_regime_source='Documento revisado';assert.match(state.analysisRegime(state.analysisRows[1]),/soporte registrado/);
 assert.match(factorSuggestion({compatible:true,count:7,distinct:3},71,50),/Pocos datos/);
 assert.match(factorSuggestion({compatible:true,count:71,distinct:1},71,50),/Sin variación/);
 assert.match(factorSuggestion({compatible:true,count:60,distinct:3},71,50),/Sugerido/);
 state.analysisApplySuggestion();assert.deepEqual(state.analysisSelected,['bathrooms']);
 const saved=state.analysisSelection();
 const restored=marketAnalysisTable([{...rows[0],analysis_factor_selection:saved},rows[1]]);
 assert.deepEqual(restored.analysisSelected,['bathrooms']);assert.equal(restored.analysisThreshold,50);
 assert.match(marketAnalysisTable([{id:'x',property_type:'Oficina'}]).analysisRegime({ph_regime:'por_verificar',ph_regime_source:''}),/sin verificar/);
});
test('discount formula distinguishes explicit zero, missing discount, zero area and price already per m2',()=>{
 const row={price_amount:'1.000.000',area_m2:'20'};
 assert.equal(discountedAnalysis(row,'0').perM2,50000);assert.equal(discountedAnalysis(row,'100').value,0);
 assert.equal(discountedAnalysis(row,'').value,null);assert.equal(discountedAnalysis({...row,area_m2:'0'},'10').perM2,null);
 assert.equal(discountedAnalysis({...row,price_unit:'valor_m2'},'10').perM2,null);
 const state=marketAnalysisTable([{id:'x',...row,negotiation_discount:'12345.6789'}]);
 assert.equal(state.analysisDiscounts.x,'12345.6789');assert.equal(state.analysisResult('x').value,1000000-12345.6789);
 const ph=marketAnalysisTable([{id:'ph',...row,ph_regime:'si'}]);assert.match(ph.analysisAreaNote('ph'),/sin confirmar/);
 ph.analysisRows[0].private_built_m2='20';ph.analysisRows[0].areas_source='Documento';assert.equal(ph.analysisAreaNote('ph'),'');
});
