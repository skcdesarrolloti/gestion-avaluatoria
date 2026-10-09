import test from 'node:test';
import assert from 'node:assert/strict';
import {courseQuartileBar} from '../resources/js/analysis-course-quartile-plot.js';
import {courseSummary} from '../resources/js/analysis-course-math.js';
const rows=values=>values.map((y,i)=>({label:'Muestra '+(i+1),y}));

test('quartile points preserve each price and stack close observations without changing their horizontal position',()=>{
    const input=rows([0,10,10,10.01,11,12,13,14,15,100]),before=JSON.stringify(input),s=courseSummary(input),svg=courseQuartileBar(s,input);
    const circles=[...svg.matchAll(/<circle cx="([^"]+)" cy="([^"]+)"/g)].map(m=>[Number(m[1]),Number(m[2])]);
    assert.equal(circles.length,input.length);
    circles.forEach((p,i)=>assert.equal(p[0],70+(input[i].y-s.min)/(s.max-s.min)*720));
    assert.equal(circles[1][0],circles[2][0]);
    assert.notEqual(circles[1][1],circles[2][1]);
    assert.notEqual(circles[2][1],circles[3][1]);
    assert.equal((svg.match(/fuera de límites exploratorios<\/title>/g)||[]).length,2);
    assert.equal(JSON.stringify(input),before);
});

test('quartile points keep repeated prices visible and escape labels in a constant series',()=>{
    const input=rows(Array(34).fill(7e6));input[0].label='<script>"x"</script>';
    const svg=courseQuartileBar(courseSummary(input),input);
    assert.doesNotMatch(svg,/NaN|Infinity|<script>/);
    assert.match(svg,/&lt;script&gt;/);
    assert.equal((svg.match(/<circle cx="430"/g)||[]).length,34);
    assert.equal(new Set([...svg.matchAll(/cy="([^"]+)"/g)].map(m=>m[1])).size,34);
});
