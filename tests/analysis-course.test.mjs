import test from 'node:test';
import assert from 'node:assert/strict';
import {courseSummary,frequencyClasses,studentQuantile,studentCDF,courseBootstrap} from '../resources/js/analysis-course-math.js';
import {courseMethods} from '../resources/js/analysis-course.js';
import {regressionMethods} from '../resources/js/analysis-regression.js';
import {courseReport} from '../resources/js/analysis-course-report.js';
import {courseHistogram} from '../resources/js/analysis-course-plots.js';
const rows=values=>values.map((y,i)=>({id:String(i),label:'Muestra '+(i+1),y,reasons:[]}));
const a=[2.55,2.70,2.85,3.05,2.62,2.98,2.78,3.10,2.90,2.66,3.02,2.80];
test('course sample A reproduces mean, sample variance, CV and t interval, not normal interval',()=>{
    const s=courseSummary(rows(a));assert.ok(Math.abs(s.mean-2.834166666666667)<1e-12);
    assert.ok(Math.abs(s.variance-.03242651515151514)<1e-12);assert.ok(Math.abs(s.median-2.825)<1e-12);
    assert.ok(Math.abs(s.t-2.200985160082949)<1e-9);assert.equal(s.lowerCI.toFixed(4),'2.7198');assert.equal(s.upperCI.toFixed(4),'2.9486');
    assert.ok(s.margin>1.96*s.se);assert.ok(Math.abs(studentQuantile(.975,1)-12.7062047364)<1e-8);
    assert.ok(Math.abs(studentCDF(-s.t,11)-.025)<1e-10);assert.equal(studentQuantile(.5,29),0);
    const b=courseSummary(rows(a.map(v=>v===3.1?3.95:v)));assert.equal(b.median,s.median);assert.ok(b.cv>s.cv);assert.ok(b.margin>s.margin);
});
test('class boundaries preserve every sample once, keep original y, include maximum and constant series',()=>{
    const input=rows(Array.from({length:30},(_,i)=>5.5e6+i*(10.5e6/29))),before=JSON.stringify(input),classes=frequencyClasses(input);
    assert.equal(classes.length,6);assert.equal(classes.reduce((s,c)=>s+c.frequency,0),30);
    assert.equal(new Set(classes.flatMap(c=>c.members.map(r=>r.id))).size,30);assert.equal(classes.at(-1).members.at(-1).y,16e6);
    assert.equal(classes.at(-1).cumulative,30);assert.equal(JSON.stringify(input),before);
    const constant=courseSummary(rows([7,7,7,7]));assert.equal(constant.classes.length,1);assert.equal(constant.mad,0);assert.deepEqual(constant.modes,[7]);assert.equal(constant.skew,null);assert.equal(constant.lowerCI,7);
});
test('descriptive counts all 34 y values while regression keeps 30 complete rows with reasons',async()=>{
    const originals=Array.from({length:34},(_,i)=>({id:String(i),area_m2:'40',price_amount:'400000000',ph_regime:'no',ph_regime_source:''}));
    const visible=originals.map((r,i)=>({key:r.id,analysisIndex:i,values:{factor:i<30?'2':'No publicado'}}));
    const state={...courseMethods(),...regressionMethods(),analysisRows:originals,analysisDiscounts:Object.fromEntries(originals.map(r=>[r.id,'0'])),analysisRegimeApplied:true,
        analysisVisibleRows:()=>visible,analysisActiveRows:()=>originals,analysisResult:()=>({value:400000000,perM2:10000000}),analysisHasSimulated:()=>true,analysisAreaNote:()=>'',analysisRegime:()=>'',regressionColumns:()=>[{key:'factor',label:'Parqueaderos'}]};
    state.regressionBasis='adjusted';await state.courseCalculate();assert.equal(state.courseResult.valid.length,34);
    const matrix=state.regressionMatrix();assert.equal(matrix.complete.length,30);assert.equal(matrix.rows.filter(r=>r.reasons.length).length,4);assert.match(matrix.rows[33].reasons[0],/Parqueaderos/);
    assert.equal(matrix.rows[33].codeKeys.length,0);visible[33].values.factor='Más de 30 años';const coded=state.regressionMatrix().rows[33];assert.equal(coded.codeKeys.length,1);assert.match(coded.reasons[0],/falta codificar/);
    state.courseBasis='offer';assert.equal(state.courseCurrent(),false);
});
test('seeded bootstrap replicates exactly, yields progress and preserves observations',async()=>{
    let progress=0;const before=[...a],one=await courseBootstrap(a,.95,n=>progress=n,1000),two=await courseBootstrap(a,.95,()=>{},1000);
    assert.deepEqual(one,two);assert.equal(progress,1000);assert.deepEqual(a,before);assert.ok(one.mean[0]<2.834&&one.mean[1]>2.834);
    assert.ok(one.median[0]<2.825&&one.median[1]>2.825);assert.equal(one.cvAvailable,1000);
    const zero=await courseBootstrap([0,0],.95,()=>{},250);assert.deepEqual(zero.cv,[null,null]);
});
test('memory follows the seven stages, escapes notes and preserves original individual values',()=>{
    const valid=rows(a),summary=courseSummary(valid),html=courseReport({at:'now',simulated:true,basis:'adjusted',rows:valid,valid,pending:[],summary,histogram:courseHistogram(summary.classes)}, {'0':'<script>bad</script>'},'<img>');
    for(let i=1;i<=7;i++)assert.ok(html.includes('<h2>'+i+'.'));
    assert.ok(html.indexOf('2. Bloques')<html.indexOf('3. Tendencia'));assert.ok(html.includes('&lt;script&gt;'));assert.ok(!html.includes('<img>'));
    assert.ok(html.includes('no submercados demostrados'));assert.ok(html.includes('semiancho')||html.includes('Semiancho'));
});
