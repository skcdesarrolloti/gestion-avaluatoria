import test from 'node:test';
import assert from 'node:assert/strict';
import {courseSummary} from '../resources/js/analysis-course-math.js';
import {courseDiagnostics} from '../resources/js/analysis-course-diagnostics.js';
import {courseReport} from '../resources/js/analysis-course-report.js';
const fixture=values=>{const valid=values.map((y,i)=>({id:String(i),label:'Muestra '+(i+1),y,reasons:[]}));return {summary:courseSummary(valid),valid,rows:valid,pending:[],histogram:'',basis:'offer',at:'now'};};
const fmt=v=>Number.isFinite(v)?v.toLocaleString('es-CO',{maximumFractionDigits:4}):'No estimable';
test('diagnostic explanations follow the saved signs, show MAD units, identify robust signals and preserve input',()=>{
    const r=fixture([1,2,3,4,100]),before=JSON.stringify(r),items=courseDiagnostics(r,fmt);
    assert.match(items[0].reading,/valores altos/);assert.match(items[1].reading,/positivo/);
    assert.equal(items[2].value,'1 COP/m²');assert.match(items[2].definition,/mediana de las distancias/);
    assert.match(items[3].reading,/no indica atipicidad/);assert.match(items[4].reading,/Muestra 5/);
    assert.equal(items[4].value,'1 de 5 con |z| > 3,5');assert.equal(JSON.stringify(r),before);
    const left=courseDiagnostics(fixture([-100,-4,-3,-2,-1]),fmt);assert.match(left[0].reading,/valores bajos/);
    assert.match(courseDiagnostics(fixture([1,2,3,4,5]),fmt)[1].reading,/negativo/);
});
test('zero MAD in a nonconstant group is explicitly not an absence of signals; unavailable shape is explained',()=>{
    const items=courseDiagnostics(fixture([1,1,1,1,100]),fmt);
    assert.match(items[2].reading,/no significa que todos los datos sean iguales/);
    assert.equal(items[4].value,'No estimable');assert.match(items[4].reading,/No debe interpretarse como ausencia/);
    const short=courseDiagnostics(fixture([1,2]),fmt);assert.match(short[0].reading,/No es estimable/);assert.match(short[1].reading,/No es estimable/);
    assert.match(courseDiagnostics(fixture([1,2,3]),fmt)[0].reading,/no demuestra/);
});
test('download includes the same diagnostic explanations and escapes flagged sample labels',()=>{
    const r=fixture([1,2,3,4,100]);r.summary.considerations[4].label='<script>bad</script>';
    const items=courseDiagnostics(r,fmt),html=courseReport(r);
    for(const item of items)assert.ok(html.includes(item.definition));
    assert.match(html,/Cómo leer tu resultado/);assert.match(html,/&lt;script&gt;bad&lt;\/script&gt;/);assert.doesNotMatch(html,/<script>/);
});
