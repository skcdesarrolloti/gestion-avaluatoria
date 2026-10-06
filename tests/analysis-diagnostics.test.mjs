import test from 'node:test';
import assert from 'node:assert/strict';
import {linearRegression} from '../resources/js/analysis-regression-math.js';
import {distribution} from '../resources/js/analysis-distribution.js';
import {regressionDiagnostics,regressionEquation} from '../resources/js/analysis-regression-diagnostics.js';
import {normalQuantile,diagnosticPlots} from '../resources/js/analysis-diagnostic-plots.js';
import {diagnosticReport} from '../resources/js/analysis-diagnostic-report.js';
test('course S2 reproduces coefficients, R², leverage and no automatic exclusion',()=>{
    const x=[55,64,65,53,61,55,71,54,64,64,68,70].map(v=>[v]),y=[87,76,78,90,79,89,72,94,72,73,69,68];
    const r=linearRegression(x,y);assert.ok(Math.abs(r.coefficients[0]-161.0736173393124)<1e-9);
    assert.ok(Math.abs(r.coefficients[1]+1.325112107623318)<1e-10);assert.ok(Math.abs(r.r2-.8951038258181326)<1e-12);
    const mean=x.reduce((s,v)=>s+v[0],0)/x.length,ss=x.reduce((s,v)=>s+(v[0]-mean)**2,0);
    r.leverage.forEach((h,i)=>assert.ok(Math.abs(h-(1/12+(x[i][0]-mean)**2/ss))<1e-12));
    assert.ok(Math.abs(r.leverage.reduce((s,v)=>s+v,0)-2)<1e-12);
    r.matrix={cols:[{label:'Área'}],complete:x.map((v,i)=>({id:String(i),label:'Muestra '+(i+1),x:v,y:y[i]}))};
    const before=JSON.stringify(r.matrix);r.diagnostics=regressionDiagnostics(r);r.plots=diagnosticPlots(r);
    assert.equal(JSON.stringify(r.matrix),before);assert.equal(r.diagnostics.cases.length,12);
    assert.ok(regressionEquation(r).includes('− 1,3251 × Área'));assert.ok(diagnosticReport(r).includes('Q-Q'));
});
test('adjusted sample kurtosis agrees with Excel and constant/zero series remain explicit',()=>{
    const d=distribution([3,4,5,2,3,4,5,6,4,7]);assert.ok(Math.abs(d.kurt+.151799637)<1e-8);
    assert.equal(distribution([2,2,2,2]).kurt,null);assert.equal(distribution([-1,0,1,2]).estimators[3].value,null);
    assert.equal(distribution([-1,0,1,2]).estimators[0].mape,null);
    assert.ok(Math.abs(normalQuantile(.975)-1.9599639845)<1e-5);assert.ok(Math.abs(normalQuantile(.5))<1e-6);
});
test('large residual, distribution tails and Cook influence are labelled independently',()=>{
    const x=Array.from({length:30},(_,i)=>[i]),y=x.map(([v],i)=>10+2*v+(i===14?150:Math.sin(i)));
    const r=linearRegression(x,y);r.matrix={cols:[{label:'Área <script>'}],complete:x.map((v,i)=>({id:String(i),label:'Muestra '+(i+1),x:v,y:y[i]}))};
    r.diagnostics=regressionDiagnostics(r);r.plots=diagnosticPlots(r);const c=r.diagnostics.cases[14];
    assert.ok(c.flags.some(f=>f.includes('studentizado')));assert.ok(c.flags.some(f=>f.includes('Cook')));
    assert.equal(r.n,30);assert.ok(diagnosticReport(r).includes('&lt;script&gt;'));assert.ok(!diagnosticReport(r).includes('<script>'));
});
test('multiple regression Cook agrees with actual deletion and hat trace equals parameter count',()=>{
    const x=Array.from({length:40},(_,i)=>[i,Math.sin(i),i%3]),y=x.map(([a,b,c],i)=>50+3*a-2*b+c+Math.cos(i*.8));
    const r=linearRegression(x,y);r.matrix={cols:[{label:'Área'},{label:'Estado'},{label:'Parqueo'}],complete:x.map((v,i)=>({id:String(i),label:String(i),x:v,y:y[i]}))};
    const d=regressionDiagnostics(r),omit=7,fit=linearRegression(x.filter((_,i)=>i!==omit),y.filter((_,i)=>i!==omit));
    const moved=x.map(v=>fit.coefficients[0]+v.reduce((s,a,j)=>s+a*fit.coefficients[j+1],0));
    const cook=moved.reduce((s,v,i)=>s+(v-r.fitted[i])**2,0)/(4*r.rmse**2);
    assert.ok(Math.abs(cook-d.cases[omit].cook)<1e-10);assert.ok(Math.abs(r.leverage.reduce((s,v)=>s+v,0)-4)<1e-12);
});
