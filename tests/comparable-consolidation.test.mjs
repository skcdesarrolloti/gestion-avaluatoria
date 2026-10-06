import test from 'node:test';
import assert from 'node:assert/strict';
import { intakeGroups,comparableIntake } from '../resources/js/comparable-intake.js';
import { primaryListing,resolvedGroup } from '../resources/js/comparable-consolidation.js';

test('56 advertisements become 20 explicitly grouped properties and 20 principal fichas',()=>{
 const rows=Array.from({length:56},(_,i)=>({id:'ad'+i,property_group:'property'+(i%20),source_name:i<19?'FincaRaíz':'Inmobiliaria',
 capture_confirmation:'confirmed',price_amount:String(i+100),area_m2:'80',research_primary:i===20?'si':''}));
 const groups=intakeGroups(rows);
 assert.equal(groups.length,20); assert.ok(groups.every(resolvedGroup));
 assert.equal(groups.map(primaryListing).length,20);assert.equal(primaryListing(groups[0]).id,'ad20');
 const state=comparableIntake(()=>rows.map((data,index)=>({used:true,index,data})),()=>({dataset:{}}));
 state.intakeView='research';state.rebuildIntake();
 assert.equal(state.consolidationDuplicates,36);assert.equal(state.intakeTableData.rows.length,20);
 assert.equal(rows.length,56);assert.equal(rows[0].price_amount,'100');
 groups[0].rows[0].capture_confirmation=''; assert.equal(resolvedGroup(groups[0]),false);
});

test('primary selection cannot reference an advertisement outside its property',()=>{
 const state=comparableIntake(()=>[],()=>({}));let writes=0;state.intakeWrite=()=>writes++;
 state.intakePrimary({rows:[{id:'a'}]},'foreign');assert.equal(writes,0);
});

test('candidate pairs show evidence without merging or confirming; bulk keep excludes alerts',()=>{
 const rows=[{id:'a',source_name:'FincaRaíz',source_url:'https://fincaraiz.com.co/oficina-1',listing_code:'1',area_m2:'80',price_amount:'500000000',neighborhood:'Centro'},
 {id:'b',source_name:'Inmobiliaria',source_url:'https://agency.test/oficina-2',listing_code:'2',area_m2:'80',price_amount:'500000000',neighborhood:'Centro'},
 {id:'c',source_name:'FincaRaíz',area_m2:'45',price_amount:'300000000',neighborhood:'Centro'}];
 const groups=intakeGroups(rows);
 assert.equal(groups.length,3);assert.deepEqual(groups[0].candidates[0].reasons,['misma área','mismo precio','mismo sector']);
 assert.equal(groups[0].candidates[0].row.listing_code,'2');assert.equal(rows[0].property_group,undefined);
 const state=comparableIntake(()=>rows.map((data,index)=>({used:true,index,data})),()=>({dataset:{}}));state.rebuildIntake();
 const writes=[];state.intakeWrite=(index,key,value)=>writes.push({index,key,value});state.intakeChanged=()=>{};
 assert.equal(state.consolidationVisible().length,2);state.consolidationShowAll=true;assert.equal(state.consolidationVisible().length,3);
 assert.equal(state.unambiguousGroups().length,1);state.confirmUnambiguous();
 assert.deepEqual(writes,[{index:2,key:'capture_confirmation',value:'confirmed'}]);
});
