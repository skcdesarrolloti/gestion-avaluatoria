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

test('another-page button retains preview until new announcements are uploaded and save acknowledged',async()=>{
    const component=portalResultsPaste({label:'FincaRaiz'});
    component.refresh=()=>{};
    component.results=[{suggested:true}]; component.pastedText='página actual';
    let saves=0;
    await component.nextPage(async()=>{saves++;return true;});
    assert.equal(saves,0); assert.equal(component.pastedText,'página actual');
    component.results=[{suggested:false,tone:'registered'}];
    await component.nextPage(async()=>false);
    assert.equal(component.pastedText,'página actual'); assert.match(component.message,/Guardado pendiente/);
    await component.nextPage(async()=>true);
    assert.equal(component.pastedText,''); assert.deepEqual(component.results,[]);
    assert.match(component.message,/siguen guardados/);
});
