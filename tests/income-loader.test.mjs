import test from 'node:test';
import assert from 'node:assert/strict';
import {loadPageComponents} from '../resources/js/page-components-loader.js';

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
