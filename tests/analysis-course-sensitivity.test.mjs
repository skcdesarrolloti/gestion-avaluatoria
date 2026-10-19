import test from 'node:test';
import assert from 'node:assert/strict';
import {courseSensitivity} from '../resources/js/analysis-course-sensitivity.js';
import {courseSummary} from '../resources/js/analysis-course-math.js';
import {courseDispersionPlot} from '../resources/js/analysis-course-dispersion-plot.js';
import {courseReport} from '../resources/js/analysis-course-report.js';
import {courseSnapshot,restoreCourseSnapshot} from '../resources/js/analysis-course-storage.js';
const result=values=>{const valid=values.map((y,i)=>({id:String(i),label:'Muestra '+(i+1),y,reasons:[]}));return {valid,rows:valid,pending:[],summary:courseSummary(valid),at:'2026-10-19',basis:'offer',simulated:true,histogram:''};};
test('sensitivity separates the middle quartile band, fence signals and positional trimming without changing the group',()=>{
    const r=result([1,2,3,4,5,6,7,8,9,100]),before=JSON.stringify(r),v=courseSensitivity(r);
    assert.equal(v.comparison[0].value,14.5);assert.equal(v.comparison[1].value,5.5);assert.equal(v.comparison[2].value,5.5);
    assert.equal(v.comparison[2].difference,-9);assert.ok(Math.abs(v.comparison[2].percent+900/14.5)<1e-10);
    assert.equal(v.k,2);assert.equal(v.retained,6);assert.equal(v.outside,1);assert.equal(v.outsideBand,6);
    assert.match(v.comparison[2].operation,/33 \/ 6/);assert.match(v.reading,/Muestra 10/);assert.match(v.next,/simulados/);
    assert.match(v.next,/no lo reduce ni acredita cumplimiento/);assert.equal(JSON.stringify(r),before);
    const svg=courseDispersionPlot(r.valid,r.summary,true);assert.equal((svg.match(/<circle /g)||[]).length,10);assert.match(svg,/Q1–Q3/);assert.match(svg,/mediana/);assert.doesNotMatch(svg,/NaN|Infinity/);
});
test('sensitivity keeps 34 observations and its concept reproducible after restoring the saved execution',()=>{
    const r=result(Array.from({length:34},(_,i)=>i+1));r.valid[33].label='<script>extremo</script>';
    const v=courseSensitivity(r);assert.equal(v.k,6);assert.equal(v.retained,22);assert.equal(v.comparison[2].value,17.5);
    const state={courseResult:r,courseBasis:'offer',courseConfidence:'.95',courseStep:4,courseNotes:{'0':'Conservar'},courseConclusion:'Pendiente'};
    const restored=restoreCourseSnapshot(courseSnapshot(state));assert.deepEqual(courseSensitivity(restored.result),v);assert.equal(restored.notes['0'],'Conservar');
    const html=courseReport(restored.result,restored.notes);assert.match(html,/Compar|Diferencia COP\/m²/);assert.ok(html.includes(v.reading));assert.ok(html.includes(v.next));
    assert.doesNotMatch(html,/<script>/);assert.match(html,/&lt;script&gt;/);
});
test('zero and constant series, small n, and inclusive fences do not invent percentages or exclusions',()=>{
    for(const values of [[0,0],[7,7,7],[1,2,3]]){
        const r=result(values),v=courseSensitivity(r),svg=courseDispersionPlot(r.valid,r.summary,true);
        assert.equal(v.k,0);assert.equal(v.retained,values.length);assert.doesNotMatch(svg,/NaN|Infinity/);
        assert.equal(v.comparison[0].percent,values[0]===0?null:0);
    }
    const r=result([0,1,2,3,4]);r.summary.lower=0;r.summary.upper=4;assert.equal(courseSensitivity(r).outside,0);
});
