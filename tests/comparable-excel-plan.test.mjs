import {test} from 'node:test';
import assert from 'node:assert/strict';
import {parseHTML} from 'linkedom';
import {excelPlan,applyExcelPlan} from '../resources/js/comparable-excel-plan.js';
import {addWorkbookContext} from '../resources/js/xlsx-context.js';
test('Excel updates sorted IDs, preserves omitted columns, distinguishes cleared cell and numeric formatting',()=>{
    const {document}=parseHTML('<table><tbody><tr><td><label class="comparable-field-label">Oferta</label><input name="comparables[0][price_amount]" value="$ 500.000.000"></td><td><input name="comparables[0][contact_phone]" value="123"></td></tr></tbody></table>');
    const controls=[...document.querySelectorAll('input')];
    const entry={used:true,data:{id:'1',source_name:'Fuente'},controls};
    const changes=excelPlan([entry],[{id:'1',price_amount:'500000000',contact_phone:''}]);
    assert.equal(changes.length,1); assert.equal(changes[0].before,'123');
    applyExcelPlan(changes); assert.equal(controls[1].value,''); assert.equal(controls[0].value,'$ 500.000.000');
    assert.throws(()=>excelPlan([entry],[{id:'other',contact_phone:'456'}]));
});
test('Excel preview does not overwrite edits made during review',()=>{
    const input={value:'original'}; const changes=[{input,before:'original',value:'Excel'}];
    input.value='Cambio en tabla';assert.throws(()=>applyExcelPlan(changes));assert.equal(input.value,'Cambio en tabla');
});
test('Excel normalizes decimal number controls and rejects dates that would clear the field',()=>{
    const number={type:'number',tagName:'INPUT',name:'comparables[0][private_built_m2]',value:'',closest:()=>null};
    const date={type:'date',tagName:'INPUT',name:'comparables[0][listing_date]',value:'',closest:()=>null};
    const entry={used:true,data:{id:'1'},controls:[number,date]};
    const changes=excelPlan([entry],[{id:'1',private_built_m2:'12,5'}]);
    assert.equal(changes[0].value,'12.5');
    assert.throws(()=>excelPlan([entry],[{id:'1',listing_date:'2026-02-30'}]));
    assert.throws(()=>excelPlan([entry],[{id:'1',listing_date:'fecha pendiente'}]));
});
test('Export context keeps identity, version and hidden sheet after Excel reorder',()=>{
    const files={'[Content_Types].xml':'<Types></Types>','xl/workbook.xml':'<sheets></sheets>','xl/_rels/workbook.xml.rels':'<Relationships></Relationships>'};
    addWorkbookContext(files,{appraisal:'abc',scope:'unit',version:4},[{key:'id',label:'ID'}],'Datos');
    assert.match(files['xl/workbook.xml'],/state="veryHidden"/);
    assert.match(files['xl/worksheets/sheet2.xml'],/sucasa-comparables-2/);
    assert.match(files['xl/worksheets/sheet2.xml'],/&quot;version&quot;:4/);
});
