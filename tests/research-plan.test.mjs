import { test } from 'node:test';
import assert from 'node:assert/strict';
import { researchValue, factorEvidence, researchSummary, comparisonState, researchPlan, publishedAreaState } from '../resources/js/research-plan.js';
const numeric = {kind:'numeric',decision:'model',definition:'Cantidad',reason:'Dotación',categories:''};
const binary = {...numeric,kind:'binary'};
const ad = (portal,values,revision=false) => ({portal,values,revision});
const group = (id,ads,contextPending=false) => ({id,ads,contextPending});
test('unknown is not zero and ranges are not exact ages', () => {
    assert.equal(researchValue('',binary,'elevator'),null);
    assert.equal(researchValue('No',binary,'elevator'),0);
    assert.equal(researchValue('0',numeric,'bathrooms'),0);
    assert.equal(researchValue('0',numeric,'area'),null);
    assert.equal(researchValue('16 a 30',numeric,'age'),null);
    assert.equal(researchValue('Sin vista', {kind:'categorical',categories:'Sin vista\nInterior'},'view'),'sin vista');
    assert.equal(researchValue('2',{kind:'categorical',categories:'Sin vista\nInterior'},'view'),null);
});
test('portal matrix warns on 3/3/2 without comparing unrelated properties or treating missing as zero',()=>{
    assert.equal(comparisonState('bathrooms',numeric,[ad('FR',{bathrooms:'3'}),ad('CC',{bathrooms:'3'}),ad('ML',{bathrooms:'2'})]),'difference');
    assert.equal(comparisonState('bathrooms',numeric,[ad('FR',{bathrooms:'3'}),ad('CC',{bathrooms:'3'})]),'equal');
    assert.equal(comparisonState('bathrooms',numeric,[ad('FR',{bathrooms:'3'}),ad('CC',{bathrooms:''})]),'incomplete');
    assert.equal(comparisonState('bathrooms',numeric,[ad('FR',{bathrooms:'0'}),ad('CC',{bathrooms:'0'})]),'equal');
    assert.equal(comparisonState('age',numeric,[ad('FR',{age:'16 a 30'}),ad('CC',{age:'16 a 30'})]),'review');
    const ui=researchPlan({plan:{target_ratio:10,factors:{area:{...numeric,decision:'defer'},bathrooms:numeric}},catalog:{},evidence:{subjects:{},groups:[group('a',[ad('FR',{area:'50',bathrooms:'3'})]),group('b',[ad('ML',{area:'50',bathrooms:'2'})])]}});
    assert.equal(ui.comparisonState('bathrooms'),'single');
    assert.deepEqual(ui.comparisonKeys,['area','bathrooms']);
});
test('area is required for joint research count even if excluded as regression predictor',()=>{
    const plan={target_ratio:10,factors:{area:{...numeric,decision:'defer',kind:'categorical',categories:'0\n50'},bathrooms:numeric}};
    const evidence={subjects:{bathrooms:'2'},groups:[group('a',[ad('FR',{area:'50',bathrooms:'2'})]),group('b',[ad('CC',{area:'',bathrooms:'3'})]),group('c',[ad('ML',{area:'0',bathrooms:'2'})])]};
    const result=researchSummary(plan,evidence);
    assert.equal(result.parameters,1); assert.equal(result.areaReady,1); assert.equal(result.joint,1);
});
test('published areas only coincide with same stated base and never fill PH private built area',()=>{
    assert.equal(publishedAreaState([{publishedArea:'80',areaBasis:'Total'},{publishedArea:'80.0',areaBasis:'Total'}]),'equal');
    assert.equal(publishedAreaState([{publishedArea:'80',areaBasis:'Total'},{publishedArea:'80',areaBasis:'Privada'}]),'review');
    assert.equal(publishedAreaState([{publishedArea:'80',areaBasis:''},{publishedArea:'80',areaBasis:''}]),'review');
    assert.equal(publishedAreaState([{publishedArea:'80',areaBasis:'Total'},{publishedArea:'82',areaBasis:'Total'}]),'difference');
});
test('linked sources complement but disagreement and re-read require reconciliation', () => {
    const result=factorEvidence('bathrooms',numeric,[
        group('a',[ad('FR',{bathrooms:'2'}),ad('CC',{bathrooms:''})]),
        group('b',[ad('FR',{bathrooms:'2'}),ad('CC',{bathrooms:'3'})]),
        group('c',[ad('FR',{bathrooms:'2'},true)]),
        group('d',[ad('CC',{bathrooms:'2 baños'})]),
        group('e',[ad('CC',{bathrooms:'2'})],true),
    ]);
    assert.deepEqual(result.ready,['a']); assert.equal(result.conflicts,2);
    assert.equal(result.formats,1); assert.equal(result.context,1);
    assert.equal(result.portals.CC.ads,4); assert.equal(result.portals.CC.present,3);
});
test('joint count counts confirmed properties, not advertisements or independent factor totals', () => {
    const evidence={subjects:{bathrooms:'2',view:'Interior'},groups:[
        group('a',[ad('FR',{bathrooms:'2',view:'Interior'}),ad('CC',{bathrooms:'2',view:'Interior'})]),
        group('b',[ad('CC',{bathrooms:'3',view:''})]),
        group('c',[ad('FR',{bathrooms:'',view:'Exterior'})]),
    ]};
    const plan={target_ratio:10,factors:{bathrooms:numeric,view:{...numeric,kind:'categorical',categories:'Interior\nExterior\nPanorámica'}}};
    const result=researchSummary(plan,evidence);
    assert.equal(result.joint,1); assert.equal(result.parameters,3); assert.equal(result.target,20); assert.equal(result.coefficientTarget,30);
    assert.equal(result.stats.bathrooms.ready.length,2);
    plan.factors.bathrooms.decision='filter'; plan.factors.view.decision='defer';
    assert.equal(researchSummary(plan,evidence).joint,0);
});
test('summary shows every factor and subject while limiting model to four, not investigation',()=>{
    const factors=Object.fromEntries(['area','bathrooms','parking','age','floor','view'].map(k=>[k,{...numeric,decision:k==='area'?'defer':k==='view'?'investigate':'model'}]));
    const ui=researchPlan({plan:{factors,target_ratio:10},catalog:{},evidence:{subjects:{bathrooms:'2'},groups:[]}});
    assert.equal(ui.comparisonKeys.length,6); assert.equal(ui.subjectLabel('bathrooms'),'2');
    assert.equal(ui.comparisonGroup.ads.length,0); assert.equal(ui.modelUnavailable('view'),true);
    assert.equal(ui.modelUnavailable('bathrooms'),false); assert.equal(ui.summary.target,40);
    factors.floor.decision='investigate'; assert.equal(ui.summary.selected,3); assert.equal(ui.modelUnavailable('view'),false);
});
