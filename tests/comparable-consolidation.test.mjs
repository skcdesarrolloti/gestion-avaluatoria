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
