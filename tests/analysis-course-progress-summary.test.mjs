import test from 'node:test';
import assert from 'node:assert/strict';
import {courseSummary} from '../resources/js/analysis-course-math.js';
import {courseProgressSummary} from '../resources/js/analysis-course-progress-summary.js';
import {courseReport} from '../resources/js/analysis-course-report.js';
const result=()=>{const valid=[1,10,10,10,11,11,12,12,13,100].map((y,i)=>({id:String(i),label:'Muestra '+i,y,reasons:[]}));return {valid,rows:valid,pending:[],summary:courseSummary(valid),simulated:true,histogram:'',basis:'offer',at:'now'};};
test('balance uses the actual selected center and preserves the sample and simulated scope in the report',()=>{
    const r=result();r.summary.estimators=[{label:'Mediana',value:11,mape:5},{label:'Media',value:20,mape:10}];
    const before=JSON.stringify(r),balance=courseProgressSummary(r);
    assert.match(balance.approximation,/Mediana = 11/);
    assert.match(balance.next,/2 inmuebles/);
    assert.match(balance.median,/No sustituye/);
    assert.match(balance.scope,/datos simulados/);
    assert.match(balance.distribution,/no un intervalo de valoración/);
    assert.match(courseReport(r),/Qué podemos afirmar hasta aquí/);
    assert.equal(JSON.stringify(r),before);
});
test('balance identifies ties and missing MAPE without selecting an arbitrary center',()=>{
    const r=result();r.summary.estimators=[{label:'Media',value:10,mape:5},{label:'Mediana',value:11,mape:5}];
    assert.match(courseProgressSummary(r).approximation,/Existe empate/);
    r.summary.estimators.forEach(e=>e.mape=null);
    assert.match(courseProgressSummary(r).approximation,/No hay un centro/);
});
