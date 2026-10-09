import test from 'node:test';
import assert from 'node:assert/strict';
import {courseDispersionPlot,outsideCourseLimits} from '../resources/js/analysis-course-dispersion-plot.js';
import {courseSummary} from '../resources/js/analysis-course-math.js';
const rows=values=>values.map((y,i)=>({id:String(i),label:'Muestra '+(i+1),y}));

test('dispersion cloud preserves all samples and marks only observations outside existing IQR limits',()=>{
    const input=rows([-50,10,10,11,11,12,12,13,14,100]),before=JSON.stringify(input),s=courseSummary(input),svg=courseDispersionPlot(input,s);
    assert.equal((svg.match(/<circle /g)||[]).length,input.length);
    assert.equal((svg.match(/stroke="#be123c"/g)||[]).length,2);
    assert.deepEqual(input.filter(r=>outsideCourseLimits(r,s)).map(r=>r.y),[-50,100]);
    assert.equal(JSON.stringify(input),before);
    assert.ok(svg.includes('Límite inferior')&&svg.includes('Límite superior')&&svg.includes('orden de listado'));
});

test('dispersion boundaries are inclusive and constant series render without invalid coordinates',()=>{
    const s={lower:0,upper:20};
    assert.deepEqual(rows([-1,0,20,21]).map(r=>outsideCourseLimits(r,s)),[true,false,false,true]);
    const input=rows([7e6,7e6,7e6]),svg=courseDispersionPlot(input,courseSummary(input));
    assert.doesNotMatch(svg,/NaN|Infinity/);
    assert.equal((svg.match(/<circle /g)||[]).length,3);
    assert.equal((svg.match(/stroke="#be123c"/g)||[]).length,0);
});

test('dispersion observation labels are escaped in SVG descriptions',()=>{
    const input=rows([1,2,3]);input[0].label='<script>alert("x")</script>';
    const svg=courseDispersionPlot(input,courseSummary(input));
    assert.doesNotMatch(svg,/<script>/);
    assert.match(svg,/&lt;script&gt;/);
});
