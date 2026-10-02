import {test} from 'node:test';
import assert from 'node:assert/strict';
import {parseHTML} from 'linkedom';
import {orderExcelColumns} from '../resources/js/comparable-excel-columns.js';
import {exportCapture} from '../resources/js/comparable-sheet.js';
import {columnName} from '../resources/js/xlsx-table.js';

test('Excel prioriza captura aunque cambie la vista, conservando campos y metadatos', () => {
    const columns = ['id','capture_pending','ph_parking_nature','negotiated_amount',
        'area_m2','source_url','price_amount','source_name'].map(key => ({key,label:key,
            numeric:key==='price_amount',options:key==='ph_parking_nature'?{privado:'Privado'}:undefined}));
    const sorted = orderExcelColumns(columns);
    assert.deepEqual(sorted.slice(0,4).map(column=>column.key), ['source_name','source_url','price_amount','area_m2']);
    assert.deepEqual(orderExcelColumns([...columns].reverse()).slice(0,4),sorted.slice(0,4));
    assert.equal(sorted.length,columns.length);
    assert.deepEqual(new Set(sorted),new Set(columns));
    assert.equal(columns[0].key,'id');
    assert.deepEqual(sorted.slice(-2).map(column=>column.key),['capture_pending','id']);
});

test('Descarga ordenada conserva valores, identidad, contexto y referencias de fórmulas', async () => {
    const data = {id:'a'.repeat(32),source_name:'FincaRaíz',price_amount:'500000000',
        area_m2:'100',negotiation_discount:'25000000',negotiated_amount:'475000000',
        ph_parking_nature:'Privado'};
    const keys=Object.keys(data), numeric=new Set(['price_amount','area_m2','negotiation_discount','negotiated_amount']);
    const {document}=parseHTML(`<form data-ph-subject="si" data-appraisal-id="${'b'.repeat(32)}">
        <input name="component_scope" value="oficina"><input name="version" value="5">
        <table><thead><tr>${keys.map(key=>`<th>${key}</th>`).join('')}</tr></thead><tbody><tr>
        ${keys.map(key=>`<td><input name="rows[0][${key}]" type="${numeric.has(key)?'number':'text'}"></td>`).join('')}
        </tr></tbody></table></form>`);
    const form=document.querySelector('form'),tr=form.querySelector('tbody tr');
    tr.cells=[...tr.querySelectorAll('td')];
    const previousDocument=globalThis.document,previousUrl=URL.createObjectURL;
    let blob;
    globalThis.document={createElement:()=>({click(){}})};
    URL.createObjectURL=value=>{blob=value;return 'blob:test';};
    try {
        exportCapture([{used:true,data,tr,controls:[...tr.querySelectorAll('input')],capturePending:[]}],form);
        const contents=new TextDecoder().decode(await blob.arrayBuffer());
        const sorted=orderExcelColumns([{key:'id'},{key:'capture_pending'},...keys.filter(key=>key!=='id').map(key=>({key}))]);
        const ref=key=>`${columnName(sorted.findIndex(column=>column.key===key))}2`;
        assert.match(contents,/<c r="A1"[^>]*><is><t[^>]*>source_name<\/t>/);
        assert.ok(contents.includes(`<c r="${ref('price_amount')}" s="2"><v>500000000</v>`));
        assert.ok(contents.includes(`<c r="${ref('area_m2')}" s="2"><v>100</v>`));
        assert.ok(contents.includes(`${ref('price_amount')}-${ref('negotiation_discount')}`));
        assert.ok(contents.includes(`<c r="${ref('id')}" s="0" t="inlineStr"><is><t xml:space="preserve">${data.id}</t>`));
        assert.ok(contents.includes('Privado'));
        assert.ok(contents.includes('&quot;scope&quot;:&quot;oficina&quot;'));
        assert.ok(contents.includes('&quot;version&quot;:5'));
    } finally {
        globalThis.document=previousDocument;
        URL.createObjectURL=previousUrl;
    }
});
