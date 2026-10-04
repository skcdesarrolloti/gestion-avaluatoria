import { test } from 'node:test';
import assert from 'node:assert/strict';
import { researchValue, researchCode, researchScale, factorEvidence, researchSummary, comparisonState, researchPlan, publishedAreaState } from '../resources/js/research-plan.js';
import { usableAssessment } from '../resources/js/research-assessments.js';
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
    assert.equal(ui.comparisonKeys.length,5); ui.onlyCandidates=false;
    assert.equal(ui.comparisonKeys.length,6); assert.equal(ui.subjectLabel('bathrooms'),'2');
    assert.equal(ui.comparisonGroup.ads.length,0); assert.equal(ui.modelUnavailable('view'),true);
    assert.equal(ui.modelUnavailable('bathrooms'),false); assert.equal(ui.summary.target,40);
    factors.floor.decision='investigate'; assert.equal(ui.summary.selected,3); assert.equal(ui.modelUnavailable('view'),false);
});

test('ordinal codes share one ascending scale; original yes does not imply total',()=>{
    const ordinal={...numeric,decision:'model',kind:'ordinal',categories:'No\nParcial\nTotal'};
    assert.equal(researchValue('No',ordinal,'generator'),0);
    assert.equal(researchValue('Parcial',ordinal,'generator'),1);
    assert.equal(researchValue('Total',ordinal,'generator'),2);
    assert.equal(researchValue('Sí',ordinal,'generator'),null);
    assert.equal(researchValue('',ordinal,'generator'),null);
    assert.equal(researchCode('',ordinal,'generator'),'Código: pendiente');
    assert.equal(researchScale(ordinal),'0 = No · 1 = Parcial · 2 = Total');
    assert.equal(researchCode('Total',ordinal,'generator'),'Código: 2');
    assert.equal(researchCode('Oficina',{kind:'categorical',categories:'Comercial\nOficina'},'destination'),'Clase: Oficina · sin jerarquía');
    const ads=[ad('FR',{generator:'Total'}),ad('CC',{generator:'total'})];
    assert.equal(comparisonState('generator',ordinal,ads),'equal');
    assert.equal(comparisonState('generator',ordinal,[...ads,ad('ML',{generator:'Parcial'})]),'difference');
    assert.equal(comparisonState('generator',ordinal,[...ads,ad('ML',{generator:'Sí'})]),'review');
    assert.equal(researchSummary({target_ratio:10,factors:{generator:ordinal}},{subjects:{generator:'Total'},groups:[group('a',ads)]}).parameters,1);
});

test('destination stays outside factor codes and sample targets; manual factors remain candidates',()=>{
    const factors={destination:{...numeric,decision:'model',kind:'categorical',categories:'Residencial\nComercial'},view:{...numeric,decision:'model',collection:'manual'}};
    const ui=researchPlan({plan:{target_ratio:10,factors},evidence:{subjects:{view:'2'},groups:[]},catalog:{}});
    assert.deepEqual(ui.comparisonKeys,['view']); ui.onlyCandidates=false;
    assert.deepEqual(ui.comparisonKeys,['view']); assert.equal(ui.modelCount,1);
    assert.equal(ui.summary.target,10); assert.equal(ui.summary.warnings.some(w=>w.startsWith('destination:')),true);
});

test('manual qualification needs support and current source/scale; portal disagreements stay visible',()=>{
    const factor={...numeric,kind:'ordinal',categories:'No\nParcial\nTotal'};
    const property={...group('a',[ad('FR',{generator:'No'}),ad('CC',{generator:'Total'})]),signature:'source-v1'};
    const qualification={value:'Parcial',support:'Visita y fotografía',basis:'source-v1',scale_kind:'ordinal',scale_categories:factor.categories};
    const plan={target_ratio:10,factors:{generator:factor},assessments:{a:{generator:qualification}}};
    assert.equal(usableAssessment(qualification,factor,'source-v1'),true);
    assert.deepEqual(researchSummary(plan,{subjects:{generator:'Parcial'},groups:[property]}).stats.generator.ready,['a']);
    assert.equal(comparisonState('generator',factor,property.ads),'difference');
    assert.equal(property.ads[0].values.generator,'No');
    qualification.support=''; assert.equal(usableAssessment(qualification,factor,'source-v1'),false);
    qualification.support='Visita'; property.signature='source-v2';
    assert.equal(researchSummary(plan,{subjects:{generator:'Parcial'},groups:[property]}).joint,0);
    assert.equal(usableAssessment(qualification,{...factor,categories:'No\nTotal'},'source-v1'),false);
});

test('legacy area candidate stays a calculation basis, outside factor count and sample target',()=>{
    const result=researchSummary({target_ratio:10,factors:{area:{...numeric,decision:'model'},bathrooms:{...numeric,decision:'model'}}},{subjects:{bathrooms:'2'},groups:[]});
    assert.equal(result.selected,1); assert.equal(result.target,10);
});

test('first manual qualification survives JSON payload when PHP initially emits empty array',()=>{
    const ui=researchPlan({plan:{factors:{generator:{...numeric,kind:'ordinal',categories:'No\nParcial\nTotal'}},assessments:[],target_ratio:10},catalog:{},evidence:{subjectSignature:'subject-v1',subjects:{},groups:[{id:'a',signature:'source-v1',ads:[]}]}});
    ui.setAssessment('a','generator','value','Parcial'); ui.setAssessment('a','generator','support','Visita');
    assert.equal(JSON.parse(ui.payload).assessments.a.generator.value,'Parcial');
    assert.equal(ui.assessmentStatus('a','generator'),'Registrado por el analista · soporte indicado');
});

test('adopting a new hierarchy retains prior qualification and support, pending recoding',()=>{
    const old={...numeric,kind:'ordinal',categories:'No\nParcial\nTotal'};
    const ui=researchPlan({plan:{factors:{generator:old},assessments:{subject:{generator:{value:'Parcial',support:'Registro anterior',basis:'s1',scale_kind:'ordinal',scale_categories:old.categories}}}},catalog:{generator:{kind:'ordinal',categories:'Sin respaldo\nParcial\nTotal',why:'Cobertura'}},evidence:{subjectSignature:'s1',subjects:{},groups:[]}});
    ui.adoptScale('generator');
    assert.equal(ui.plan.assessments.subject.generator.support,'Registro anterior');
    assert.equal(ui.assessmentCode('subject','generator'),'Código: pendiente por cambio de escala');
});
