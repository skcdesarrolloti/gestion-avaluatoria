import {test} from 'node:test';
import assert from 'node:assert/strict';
import {comparableExcel} from '../resources/js/comparable-excel.js';
function fixture() {
    const input={name:'comparables[0][contact_name]',value:'Antes',tagName:'INPUT',type:'text',closest:()=>null};
    const fields={version:{value:'1'},_token:{value:'token'},component_scope:{value:'oficina'}};
    const form={dataset:{excelUpdated:'Sin importación',excelPreviewEndpoint:'/revisar',excelSaveEndpoint:'/guardar'},querySelector:selector=>fields[selector.match(/name=([^\]]+)/)[1]]};
    const state={...comparableExcel(),refresh(){}};
    state.initExcel(form,[{used:true,data:{id:'sample'},controls:[input]}]);
    return {state,input,fields};
}
const event=()=>({target:{files:[new File(['xlsx'],'muestras.xlsx')],value:'muestras.xlsx'}});
test('Revisión Excel no modifica datos ni fecha; sólo confirmación guardada actualiza ambos',async()=>{
    const original=globalThis.fetch,{state,input,fields}=fixture();const requests=[];
    try {
        globalThis.fetch=async endpoint=>{requests.push(endpoint);return {ok:true,headers:{get:()=> 'application/json'},json:async()=>endpoint==='/revisar'
            ? {ok:true,version:1,rows:[{id:'sample',contact_name:'Excel'}]}
            : {ok:true,version:2,excel_updated:'Guardado 02/10/2026 13:00:00'}};};
        await state.prepareExcel(event());
        assert.equal(input.value,'Antes');assert.equal(state.excelUpdated,'Sin importación');assert.equal(state.excelReady,true);
        await state.applyExcel();
        assert.equal(input.value,'Excel');assert.equal(fields.version.value,'2');assert.equal(state.excelUpdated,'Guardado 02/10/2026 13:00:00');
        assert.deepEqual(requests,['/revisar','/guardar']);assert.equal(state.excelReady,false);
    } finally {globalThis.fetch=original;}
});
test('Carga rechazada no anuncia fecha ni cambia tabla; archivo idéntico puede confirmar carga',async()=>{
    const original=globalThis.fetch,{state,input}=fixture();
    try {
        globalThis.fetch=async endpoint=>({ok:endpoint==='/revisar',headers:{get:()=> 'application/json'},json:async()=>endpoint==='/revisar'
            ? {ok:true,version:1,rows:[{id:'sample',contact_name:'Antes'}]} : {ok:false,message:'Versión obsoleta'}});
        await state.prepareExcel(event());assert.equal(state.excelChanges.length,0);assert.equal(state.excelReady,true);
        await state.applyExcel();assert.equal(input.value,'Antes');assert.equal(state.excelUpdated,'Sin importación');assert.equal(state.excelMessage,'Versión obsoleta');
        state.cancelExcel();assert.equal(state.excelReady,false);
    } finally {globalThis.fetch=original;}
});
