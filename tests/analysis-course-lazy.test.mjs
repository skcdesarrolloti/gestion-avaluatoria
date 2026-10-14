import test from 'node:test';
import assert from 'node:assert/strict';
import {execFileSync} from 'node:child_process';
import {parseHTML} from 'linkedom';
import {marketAnalysisTable} from '../resources/js/market-analysis-table.js';

test('only the selected statistics step mounts its rows and plots; notes and results survive navigation', async () => {
    const rows = Array.from({length:34},(_,i)=>({id:String(i),ph_regime:'si',area_m2:'40',price_amount:String(4e8+i*5e6),negotiation_discount:'0'}));
    const state = marketAnalysisTable(rows,'si','oficina');
    state.analysisRegimeApplied = true;
    state.analysisView = 'result';
    await state.courseCalculate();
    const at = state.courseResult.at;
    state.courseNotes['0'] = 'Revisión conservada';
    state.coursePersist = async () => {}; // Transport is exercised in the autosave tests.
    let plots = 0;
    state.coursePlot = () => { plots++; };
    const html = execFileSync('php',['-r','require "bootstrap.php"; foreach (["preparation","descriptive","precision"] as $name) require "app/Views/appraisals/methodology-course-$name.php";']).toString();
    const {window} = parseHTML('<html><body><section x-data="probe">'+html+'</section></body></html>');
    // Linkedom exposes template contents as children; browsers keep them inert.
    Object.defineProperty(window.HTMLTemplateElement.prototype,'firstElementChild',{configurable:true,get(){return null;}});
    const names = ['window','document','MutationObserver','Element','HTMLElement','ShadowRoot','Event','CustomEvent','getComputedStyle','requestAnimationFrame'];
    const prior = Object.fromEntries(names.map(name=>[name,globalThis[name]]));
    Object.assign(globalThis,{window,document:window.document,MutationObserver:window.MutationObserver,Element:window.Element,
        HTMLElement:window.HTMLElement,ShadowRoot:window.ShadowRoot,Event:window.Event,CustomEvent:window.CustomEvent,getComputedStyle:()=>({display:'block'}),requestAnimationFrame:cb=>setTimeout(cb,0)});
    const {default:packageAlpine} = await import('alpinejs');
    const Alpine = packageAlpine.default || packageAlpine;
    const root = document.querySelector('section');
    const settle = () => new Promise(resolve=>setTimeout(resolve,25));
    try {
        Alpine.data('probe',()=>state);
        Alpine.initTree(root);
        await settle();
        const live = Alpine.$data(root);
        assert.equal(plots,0);
        assert.equal(root.querySelectorAll('textarea').length,0);
        live.courseStep = 3;
        await settle();
        assert.equal(plots,2);
        assert.equal(root.querySelectorAll('textarea').length,0);
        live.courseStep = 4;
        await settle();
        assert.equal(root.querySelectorAll('textarea').length,34);
        assert.equal(plots,2);
        const draft = live.courseInlineEdit('0');
        draft.offer = '450000000';
        draft.source = 'Corrección aún por aplicar';
        live.courseNotes['0'] = 'Nota editada';
        live.courseStep = 2;
        await settle();
        assert.equal(root.querySelectorAll('textarea').length,0);
        live.courseStep = 4;
        await settle();
        assert.equal(live.courseNotes['0'],'Nota editada');
        assert.equal(root.querySelector('textarea').value,'Nota editada');
        assert.equal(live.courseInlineEdit('0').offer,'450000000');
        assert.equal(live.courseInlineEdit('0').source,'Corrección aún por aplicar');
        assert.equal(live.analysisRows[0].price_amount,'400000000');
        assert.equal(live.courseResult.at,at);
        assert.equal(plots,2);
    } finally {
        Alpine.destroyTree(root);
        delete window.HTMLTemplateElement.prototype.firstElementChild;
        for(const name of names) { if(prior[name]===undefined) delete globalThis[name]; else globalThis[name]=prior[name]; }
    }
});
