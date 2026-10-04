import { test } from 'node:test';
import assert from 'node:assert/strict';
import { researchValue, factorEvidence, researchSummary } from '../resources/js/research-plan.js';
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
    assert.equal(result.joint,1); assert.equal(result.parameters,3); assert.equal(result.target,30);
    assert.equal(result.stats.bathrooms.ready.length,2);
    plan.factors.bathrooms.decision='filter'; plan.factors.view.decision='defer';
    assert.equal(researchSummary(plan,evidence).joint,0);
});
