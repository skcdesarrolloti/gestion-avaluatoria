import test from 'node:test';
import assert from 'node:assert/strict';
import {courseSummary} from '../resources/js/analysis-course-math.js';
import {courseCalculation} from '../resources/js/analysis-course-calculation.js';
import {marketAnalysisTable} from '../resources/js/market-analysis-table.js';

const result = values => {
    const valid=values.map((y,i)=>({id:String(i),label:'Muestra '+(i+1),y}));
    return {valid,summary:courseSummary(valid)};
};
const near = (a,b) => assert.ok(Math.abs(a-b)<1e-8*Math.max(1,Math.abs(b)),`${a} ≠ ${b}`);

test('MAPE workings reconcile every center with all original observations and preserve their identity',()=>{
    for(const n of [2,5,34,71]) {
        const saved=result(Array.from({length:n},(_,i)=>100+(n-i)**2));
        const before=JSON.stringify(saved);
        for(const estimator of saved.summary.estimators) {
            const audit=courseCalculation(saved,estimator.label);
            near(audit.mape,estimator.mape);
            near(audit.sumPercent/audit.n,estimator.mape);
            for(const row of audit.rows) {
                const source=saved.valid.find(r=>r.id===row.id);
                assert.equal(row.y,source.y);
                near(row.absolute,Math.abs(source.y-estimator.value));
                near(row.percent,100*Math.abs(source.y-estimator.value)/Math.abs(source.y));
            }
            assert.equal(audit.rows.length,n); // Even a trimmed center compares against all n values.
            near(audit.meanSum/n,saved.summary.mean);
            near(audit.trimmedSum/audit.trimmedCount,saved.summary.estimators[2].value);
            near((audit.medianLow.y+audit.medianHigh.y)/2,saved.summary.median);
            near(Math.exp(audit.logSum/n),saved.summary.estimators[3].value);
            near(audit.max.y-audit.min.y,saved.summary.range);
            near(audit.deviations/(n-1),saved.summary.variance);
        }
        assert.equal(JSON.stringify(saved),before);
    }
});

test('MAPE zero denominator and unavailable geometric center stay non-estimable',()=>{
    const saved=result([0,10,20]);
    const audit=courseCalculation(saved,'Media');
    assert.equal(audit.sumPercent,null);assert.equal(audit.mape,null);
    assert.equal(audit.rows[0].percent,null);
    assert.equal(courseCalculation(saved,'Media geométrica').rows[1].absolute,null);
    assert.equal(audit.logSum,null);
    assert.equal(courseCalculation(null),null);
});

test('review from statistics opens the shared table without leaving the step or replacing the execution',async()=>{
    const state=marketAnalysisTable([{id:'a',area_m2:'20',price_amount:'200',ph_regime:'si'}],'si','oficina');
    state.analysisModule='statistics';state.courseStep=3;state.courseResult={at:'saved'};
    const prior=globalThis.document;
    let focused=false;
    globalThis.document={getElementById:id=>{assert.equal(id,'analysis-row-a');return {scrollIntoView(){},focus(){focused=true;}};}};
    state.$nextTick=async()=>{};
    try {
        await state.courseReview('a');
        assert.equal(state.analysisModule,'statistics');assert.equal(state.courseStep,3);
        assert.equal(state.courseDataOpen,true);assert.equal(state.courseResult.at,'saved');
        assert.equal(state.analysisReviewReturn,null);assert.equal(focused,true);
    } finally {globalThis.document=prior;}
});
