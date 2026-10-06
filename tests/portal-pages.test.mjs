import test from 'node:test';
import assert from 'node:assert/strict';
import { fillRows } from '../resources/js/comparable-bulk-import.js';
import { portalResultsPaste } from '../resources/js/portal-results-paste.js';

test('21 then 16 announcements append, retain prior attributes and repeated pages add no rows', () => {
    const keys=['source_url','source_name','price_amount','area_m2','bathrooms','query_used','active','status','intake_state','consulted_at','comparability_notes'];
    const rows=Array.from({length:40},(_,index)=>{
        const fields=keys.map(key=>({name:`comparables[${index}][${key}]`,value:'',tagName:'INPUT'}));
        return {sectionRowIndex:index,querySelectorAll:()=>fields,
            querySelector:selector=>fields.find(input=>selector.includes(`[${input.name.match(/\[([^\]]+)\]$/)[1]}]`))};
    });
    const form={querySelectorAll:()=>rows,dispatchEvent:()=>{}};
    const batch=(start,count)=>Array.from({length:count},(_,i)=>({source_url:`https://www.fincaraiz.com.co/oficina/${start+i}`,
        source_name:'FincaRaiz',price_amount:String(500000000+i),area_m2:String(40+i),bathrooms:'2'}));
    const first=batch(1,21),second=batch(22,16);
    assert.equal(fillRows(form,first,'').count,21);
    const before=rows.slice(0,21).map(row=>row.querySelectorAll().map(input=>input.value));
    assert.equal(fillRows(form,second,'').count,16);
    assert.deepEqual(rows.slice(0,21).map(row=>row.querySelectorAll().map(input=>input.value)),before);
    assert.equal(rows.filter(row=>row.querySelectorAll()[0].value).length,37);
    assert.equal(fillRows(form,second,'').count,0);
    assert.equal(rows.filter(row=>row.querySelectorAll()[0].value).length,37);
});

test('another-page button clears only paste field, retaining pages, pending cards and selection',()=>{
    const component=portalResultsPaste({label:'FincaRaiz'});
    component.results=[{suggested:true}]; component.pastedText='página actual';
    component.pages=[{signature:'first',count:21}]; component.selected=['first'];
    component.nextPage();
    assert.equal(component.pastedText,''); assert.equal(component.results.length,1);
    assert.equal(component.pages.length,1); assert.deepEqual(component.selected,['first']);
    assert.match(component.message,/Se conservan/);
});

test('collect both pasted pages before upload and keep exact repeats out without replacing original data',()=>{
    const component=portalResultsPaste({label:'FincaRaiz'});
    const batch=(start,count)=>Array.from({length:count},(_,i)=>({source_url:`https://www.fincaraiz.com.co/oficina/${start+i}`,
        source_name:'FincaRaiz',price_amount:String(500000000+i),area_m2:String(40+i),bathrooms:'2'}));
    const first=batch(1,21), second=batch(22,16);
    component.collectPage(first,'Mostrando 1 - 21 de 37 resultados');
    const original=structuredClone(component.results[0].row);
    component.nextPage();
    component.collectPage(second,'Mostrando 22 - 37 de 37 resultados');
    assert.equal(component.receivedCount,37); assert.equal(component.results.length,37);
    assert.equal(component.pages.length,2); assert.equal(component.portalTotal,37);
    component.collectPage(first,'Mostrando 1 - 21 de 37 resultados');
    assert.equal(component.pages.length,2); assert.equal(component.results.length,37);
    assert.deepEqual(component.results[0].row,original);
    component.collectPage([first[0],...batch(38,1)]);
    assert.equal(component.receivedCount,39); assert.equal(component.results.length,38);
});
