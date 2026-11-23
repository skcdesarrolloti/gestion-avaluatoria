import test from 'node:test';
import assert from 'node:assert/strict';
import {loadPageComponents} from '../resources/js/page-components-loader.js';

test('localización carga su componente antes de Alpine, reintenta errores y permite navegación SPA',async()=>{
    const page={querySelector:selector=>selector==='[x-data^="locationPhotos("]'?{}:null};
    const registered=[],alpine={data:(...args)=>registered.push(args)};let imports=0;
    await assert.rejects(loadPageComponents(page,alpine,async()=>({})),/Fotos/);
    await loadPageComponents(page,alpine,async url=>{imports++;assert.match(url,/location-photos.js\?v=/);return {locationPhotos:()=>({})};});
    assert.equal(registered[0][0],'locationPhotos');
    await loadPageComponents(page,alpine,async()=>{imports++;});assert.equal(imports,1);
});

test('Renta code loads only on its page, retries a failed import, and registers before Alpine initializes',async()=>{
    let imports=0;const registered=[];
    const alpine={data:(...args)=>registered.push(args)};
    const unrelated={querySelector:()=>null};
    await loadPageComponents(unrelated,alpine,async()=>{imports++;});assert.equal(imports,0);
    const income={querySelector:selector=>selector==='[data-income-inputs]'?{}:null};
    await assert.rejects(loadPageComponents(income,alpine,async()=>({})),/Renta/);
    assert.equal(registered.length,0);
    await loadPageComponents(income,alpine,async url=>{
        imports++;assert.match(url,/income-inputs.js\?v=/);return {incomeInputs:()=>({})};
    });
    assert.equal(imports,1);assert.equal(registered[0][0],'incomeInputs');
    await loadPageComponents(income,alpine,async()=>{imports++;});assert.equal(imports,1);
});

test('IGAC loads on its form, retries a missing bundle and is ready before mounting chapter 3',async()=>{
    const page={querySelector:selector=>selector==='[x-data^="igacUnitSelector("]'?{}:null};
    const registered=[],alpine={data:(...args)=>registered.push(args)};let imports=0;
    await assert.rejects(loadPageComponents(page,alpine,async()=>({})),/IGAC/);
    await loadPageComponents(page,alpine,async url=>{imports++;assert.match(url,/igac-unit-selector.js\?v=/);return {igacUnitSelector:()=>({})};});
    assert.equal(registered[0][0],'igacUnitSelector');
    await loadPageComponents(page,alpine,async()=>{imports++;});assert.equal(imports,1);
});
