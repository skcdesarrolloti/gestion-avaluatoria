import {test} from 'node:test';
import assert from 'node:assert/strict';
import {researchPlan} from '../resources/js/research-plan.js';

test('research summary is reused across bindings and refreshed after nested plan or evidence changes', () => {
    const factor={kind:'numeric',decision:'model',definition:'Cantidad',reason:'Dotación',categories:''};
    const ui=researchPlan({plan:{target_ratio:10,factors:{bathrooms:factor}},catalog:{},
        evidence:{subjects:{bathrooms:'2'},groups:[{id:'one',ads:[{portal:'Prueba',values:{bathrooms:'2'}}]}]}});
    const watchers={};
    ui.$watch=(key,callback)=>{watchers[key]=callback;};
    ui.init();
    const original=ui.summary;
    for(let i=0;i<250;i++) assert.equal(ui.summary,original);
    assert.equal(original.target,10);
    ui.plan.target_ratio=15;
    watchers.plan();
    assert.equal(ui.summary.target,15);
    assert.notEqual(ui.summary,original);
    ui.evidence.groups[0].ads[0].values.bathrooms='';
    watchers.evidence();
    assert.equal(ui.summary.stats.bathrooms.missing,1);
    assert.deepEqual(ui.summary.stats.bathrooms.ready,[]);
    assert.equal(ui.evidence.groups.length,1);
});
