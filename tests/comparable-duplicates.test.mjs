import test from 'node:test';
import assert from 'node:assert/strict';
import { duplicateEvidence, checkComparableDuplicates } from '../resources/js/comparable-duplicates.js';
const a = { source_url:'https://a.example/1', source_name:'A', area_m2:'46', price_amount:'$ 450.000.000', neighborhood:'Bocagrande' };
test('different portal/code triggers review on matching area price and sector', () => {
 const match=duplicateEvidence({...a, source_url:'https://b.example/999',price_amount:'450000000'},a);
 assert.equal(match.exact,false); assert.equal(match.reasons.length,3);
});
test('price and area alone do not identify a property',()=>assert.equal(duplicateEvidence({...a,source_url:'https://b.example/2',neighborhood:''},a),null));
test('street address and area can flag changed asking price',()=>assert.ok(duplicateEvidence({...a,source_url:'https://b.example/2',address_hint:'Calle 5 # 4-20',price_amount:'400000000'}, {...a,address_hint:'Calle 5 # 4-20'})));
test('empty records are not duplicates',()=>assert.equal(duplicateEvidence({},{}),null));
test('same URL ignores tracking and blocks without confirmation',()=>assert.deepEqual(checkComparableDuplicates({...a,source_url:a.source_url+'?utm_source=x'},[a],()=>{throw Error('must not ask');}),{blocked:true,exact:true}));
test('cancel keeps candidate unchanged and blocks insertion',()=>{
 const candidate={...a,source_url:'https://b.example/3'}; const before={...candidate};
 assert.equal(checkComparableDuplicates(candidate,[a],()=>false).blocked,true); assert.deepEqual(candidate,before);
});
test('distinct confirmation records review without changing existing sample',()=>{
 const candidate={...a,source_url:'https://b.example/3'}; const before={...a};
 assert.equal(checkComparableDuplicates(candidate,[a],()=>true).blocked,false);
 assert.match(candidate.comparability_notes,/declaró inmueble distinto/); assert.deepEqual(a,before);
});
