import test from 'node:test';
import assert from 'node:assert/strict';
import {loadAnalysisTable} from '../resources/js/analysis-table-loader.js';
test('analysis loads only for marked forms, validates the module, then registers once before rendering',async()=>{
 let imports=0,registered=[];const alpine={data:(...args)=>registered.push(args)};
 const importer=async url=>{imports++;assert.match(url,/market-analysis-table.js\?v=/);return {marketAnalysisTable:()=>({})};};
 await loadAnalysisTable({querySelector:()=>null},alpine,importer);assert.equal(imports,0);
 await assert.rejects(loadAnalysisTable({querySelector:()=>true},alpine,async()=>({})),/No se pudo cargar/);assert.equal(registered.length,0);
 await loadAnalysisTable({querySelector:()=>true},alpine,importer);assert.equal(imports,1);assert.equal(registered[0][0],'marketAnalysisTable');
 await loadAnalysisTable({querySelector:()=>true},alpine,importer);assert.equal(imports,1);
});
