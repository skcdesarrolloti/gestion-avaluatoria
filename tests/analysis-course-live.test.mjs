import test from 'node:test';
import assert from 'node:assert/strict';
import {marketAnalysisTable} from '../resources/js/market-analysis-table.js';
const create=()=>{const s=marketAnalysisTable([100,100,100].map((price,i)=>({id:String(i),price_amount:String(price),area_m2:'10',negotiation_discount:'0',ph_regime:'no'})),'no','oficina');s.analysisRegimeApplied=true;s.analysisView='result';return s;};
test('editing multiple drafts updates all preview statistics while rows, saved execution and snapshot stay unchanged',async()=>{
    const s=create();await s.courseCalculate();const rows=JSON.stringify(s.analysisRows),snapshot=s.courseSnapshot(),at=s.courseResult.at;
    const d=s.courseInlineEdit('0');d.offer='120';let p=s.courseLivePreview();
    assert.equal(p.n,3);assert.equal(p.drafts,1);assert.equal(p.summary.mean,32/3);assert.match(p.status,/supera/);
    s.courseInlineEdit('1').area='20';p=s.courseLivePreview();assert.equal(p.summary.mean,9);assert.equal(p.drafts,2);
    assert.equal(JSON.stringify(s.analysisRows),rows);assert.equal(s.courseSnapshot(),snapshot);assert.equal(s.courseResult.at,at);
    d.offer='100';delete s.courseInlineDrafts['1'];assert.equal(s.courseLivePreview().summary.cv,0);assert.match(s.courseLivePreview().status,/dentro/);
});
test('an incomplete row blocks the group preview instead of silently excluding it; bases and current discounts are respected',async()=>{
    const s=create();await s.courseCalculate();const d=s.courseInlineEdit('0');d.area='';let p=s.courseLivePreview();assert.equal(p.summary,null);assert.equal(p.pending.length,1);assert.equal(p.n,3);
    d.area='10';d.percent='101';assert.equal(s.courseLivePreview().summary,null);d.percent='';assert.equal(s.courseLivePreview().summary,null);
    s.courseBasis='offer';assert.equal(s.courseLivePreview().summary.mean,10);s.courseBasis='adjusted';d.percent='0';
    s.analysisChange('0','10');assert.equal(s.courseLivePreviewRow('0').y,9);assert.equal(s.courseLivePreview().drafts,0);
    d.offer='200';assert.equal(s.courseLivePreviewRow('0').y,18);s.analysisChange('1','101');assert.equal(s.courseLivePreview().summary,null);
});
test('preview rounding agrees with applied corrections, and applying still requires evidence before updating the execution',async()=>{
    const s=create();await s.courseCalculate();const old=s.courseResult,d=s.courseInlineEdit('0');d.offer='123.4567';d.area='13';d.percent='7.1234';
    const predicted=s.courseLivePreviewRow('0').y;assert.equal(await s.courseInlineApply('0',d),false);assert.equal(s.courseResult,old);
    d.source='Fuente verificada y motivo';assert.equal(await s.courseInlineApply('0',d),true);assert.equal(s.courseLivePreviewRow('0').y,predicted);assert.equal(s.courseResult,old);
    await s.courseCalculate();assert.equal(s.courseResult.valid[0].y,predicted);assert.equal(s.courseLivePreview().drafts,0);
});
test('an untouched cached draft does not undo a current discount edited directly in the shared table',async()=>{
    const s=create();await s.courseCalculate();const d=s.courseInlineEdit('0');s.analysisChange('0','10');d.offer='200';d.source='Fuente verificada';
    const predicted=s.courseLivePreviewRow('0').y;assert.equal(predicted,18);
    assert.equal(await s.courseInlineApply('0',d),true);assert.equal(s.analysisPercents['0'],'10');
    assert.equal(s.courseLivePreviewRow('0').y,predicted);await s.courseCalculate();assert.equal(s.courseResult.valid[0].y,predicted);
});
