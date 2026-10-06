import test from 'node:test';
import assert from 'node:assert/strict';
import {marketAnalysisTable} from '../resources/js/market-analysis-table.js';
import {portalTable} from '../resources/js/comparable-portal-table.js';

const samples=()=>Array.from({length:34},(_,i)=>({id:String(i),property_type:'Oficina',price_amount:'500000000',area_m2:String(40+i),bathrooms:String(i%3),floor_level:String(i%5),published_attributes:JSON.stringify({'Área Privada':String(40+i),'Antigüedad':String(i%10),'Estado':i<20?(i%2?'Usado':'Nuevo'):''})}));
test('manual missing values require support, preserve published facts and survive reload with immutable statistics',()=>{
    const rows=samples(), state=marketAnalysisTable(rows,'','oficina');state.analysisApplySuggestion();
    state.analysisSelected=['published:area privada','published:antiguedad','published:estado'];state.analysisUpdate();
    assert.equal(state.analysisComplete(),20);const previous=JSON.stringify(state.analysisStatistics);
    const property=state.analysisVisibleRows()[20], factor=state.analysisColumns().find(f=>f.key==='published:estado');
    assert.equal(state.analysisCellState(property,factor),'missing');
    state.analysisEdit(property,factor,'value','Usado');assert.equal(state.analysisComplete(),20);
    state.analysisEdit(property,factor,'source','Ficha verificada, 6 octubre, analista');assert.equal(state.analysisComplete(),21);
    assert.equal(state.analysisCellState(state.analysisVisibleRows()[20],factor),'manual');
    assert.equal(portalTable(state.analysisRows).rows[20].values[factor.key],undefined);
    assert.equal(state.analysisRows[20].published_attributes,rows[20].published_attributes);
    assert.equal(JSON.stringify(state.analysisStatistics),previous);
    state.analysisRecordManual();assert.equal(state.analysisStatistics.at(-1).complete,21);assert.equal(state.analysisStatistics.at(-2).complete,20);
    const restored=marketAnalysisTable(state.analysisRows.map((r,i)=>({...r,...(!i?{analysis_factor_selection:state.analysisSelection()}:{})})),'','oficina');
    assert.equal(restored.analysisComplete(),21);restored.analysisOnlyMissing=true;assert.equal(restored.analysisDisplayRows().length,13);
    state.analysisEdit(state.analysisVisibleRows()[0],factor,'value','Alterar');assert.equal(state.analysisManualEntry('0',factor.key).value,undefined);
});
test('combinations use joint coverage, valid offers and count area only once',()=>{
    const state=marketAnalysisTable(samples(),'','oficina');state.analysisApplySuggestion();
    state.analysisSelected=['published:area privada','published:antiguedad','published:estado'];state.analysisUpdate();
    const suggestions=state.analysisCombinations();assert.ok(suggestions.length);assert.equal(suggestions[0].count,34);assert.equal(suggestions[0].meets,true);
    state.analysisChooseCombination(suggestions[0]);state.analysisUpdate();assert.equal(state.analysisFactorCount(),3);assert.equal(state.analysisComplete(),34);
    state.analysisRows.slice(25).forEach(r=>r.price_amount='');state.analysisManualVersion++;
    assert.equal(state.analysisCombinations()[0].count,25);assert.equal(state.analysisCombinations()[0].meets,false);
});
