import test from 'node:test';
import assert from 'node:assert/strict';
import {courseQuartileCalculation,courseQuartileOperation,courseQuartileLimitOperations} from '../resources/js/analysis-course-quartile-calculation.js';
import {courseSummary} from '../resources/js/analysis-course-math.js';
test('34-observation audit identifies actual source rows, one-based positions and existing quartile results without mutation',()=>{
    const rows=Array.from({length:34},(_,i)=>({id:String(i),label:'Muestra '+i,y:34-i})),before=JSON.stringify(rows),audit=courseQuartileCalculation(rows),s=courseSummary(rows);
    assert.deepEqual(audit.cuts.map(c=>c.position),[9.25,17.5,25.75]);
    assert.deepEqual(audit.cuts.map(c=>[c.lower.position,c.upper.position,c.fraction]),[[9,10,.25],[17,18,.5],[25,26,.75]]);
    assert.deepEqual(audit.cuts.map(c=>c.value),[s.q1,s.median,s.q3]);
    assert.equal(audit.cuts[0].lower.id,'25');
    assert.match(courseQuartileOperation(audit.cuts[0],String),/9 \+ 0.25 × \(10 − 9\) = 9.25/);
    assert.equal(courseQuartileLimitOperations(s,String)[0],'RIC = 25.75 − 9.25 = 16.5 COP/m²');
    assert.equal(JSON.stringify(rows),before);
});
test('audit handles integral positions, duplicate prices and empty data',()=>{
    const audit=courseQuartileCalculation([1,1,1,2,3].map((y,i)=>({label:String(i),y})));
    assert.equal(audit.cuts[0].lower.position,audit.cuts[0].upper.position);
    assert.equal(audit.cuts[0].value,1);
    assert.deepEqual(courseQuartileCalculation([]),{ordered:[],cuts:[]});
});
